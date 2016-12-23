(function($){
	"use strict";
	/**
	 * AutoComplete
	 * @param inputSelectorString - строка с селектором для jQuery
	 * @param callback - callback function
	 * @param category -ex. sale, rent, invest
	 * @param invest - if it is Invest object
	 * @constructor
	 */
	function AutoComplete(
		inputSelectorString,
		callback, category, invest
	) {
		this.jqInput = $(inputSelectorString);
		this.callback = callback;
		this.category = category;
		this.autocompleteSelected = null;
		this.invest = invest;
		this.errors = {
			google: false
		};

		try {
			//https://developers.google.com/maps/documentation/javascript/3.exp/reference#AutocompleteService
			this.autocompleteService = new google.maps.places.AutocompleteService();
			//https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlacesService
			this.placeService = new google.maps.places.PlacesService(document.createElement("div"));
		}catch(e){
			this.errors.google = true;
			console.error("Google maps service not loaded");
		}
		if(this.category !== 'invest') {
			this.attachTypeAheadPlugin();
		}
	}

	/**
	 * Цепляет typeahed плагин на инпут
	 *
	 * опции плагина: https://github.com/bassjobsen/Bootstrap-3-Typeahead#optionshttps://github.com/bassjobsen/Bootstrap-3-Typeahead#options
	 */
	AutoComplete.prototype.attachTypeAheadPlugin = function(){
		var $this = this;
		this.jqInput.typeahead(
			{
				delay: 500,
				items: 'all',
				fitToElement: true,
				matcher: function(item){ //нам не нужна доп. фильтрация совпадений плагином, так что переназначаем нативный метод
					return true;
				},
				menu: '<ul class="sp-search-dropdown" role="listbox"></ul>',
				item: '<li role="option"><a href="#" tabindex="-1"></a></li>',
				afterSelect: $this.afterSelect.bind($this),
				minLength: 2,
				source: function(query, process){
					if(query && query.length > 0){
						$this.getMatches(query)
							.done($this.getMatchesSuccess.bind($this, query, process))
							.fail($this.getMatchesError.bind($this, query, process));
					}else{
						$this.autocompleteSelected = null;
						$this.callback();
					}
				},
				render: $this.renderForTypeahead
			}
		);

		this.jqInput.on('keydown', this.scrollOnKeydown);
		this.jqInput.on('blur', this.onBlur.bind(this));
	};

	/**
	 * Call api suggester and google maps api requesters
	 *
	 * @param {String} query
	 * @returns {Promise}
	 */
	AutoComplete.prototype.getMatches = function(query){
		return $.when(this.askAPI(query), this.askGoogleAPI(query));
	};

	/**
	 * Запрашивает совпадения у API (тот что через вордпресс).
	 *
	 * @param query - значение из инпута автокомплита
	 * @returns {Promise} - jQuery promise (http://api.jquery.com/Types/#Deferred)
	 */
	AutoComplete.prototype.askAPI = function(query){
		var data = {
			query: query,
			dataType: 'json',
			action: 'do_ajax',
			fn: 'get_suggestions'
		};
		if(this.category === 'sale') {
			data.scope = 'for_sale';
		} else if(this.category === 'rent') {
			data.scope = 'for_rent';
		}
		return $.ajax(
			{
				url: LpData.ajaxUrl,
				data: data

			}
		);
	};

	/**
	 * Запрашивает совпадения у GoogleMap API посредством AutocompleteService
	 *
	 * @param query - значение из инпута автокомплита
	 * @returns {Promise} - jQuery promise (http://api.jquery.com/Types/#Deferred)
	 */
	AutoComplete.prototype.askGoogleAPI = function(query){
		var deferred = $.Deferred(),
		    $this = this;
		this.autocompleteService.getQueryPredictions(
			{input: query},
			function(array, status){
				status === "OK" ? deferred.resolve($this.getParsedPlacesArray(array)) : deferred.reject(status); // jshint ignore:line, strict:true
			}
		);
		return deferred.promise();
	};

	/**
	 * Обрабатывает ответ апи (массив совпадений) в удобоваримый для typeahead плагина формат.
	 * Меняет имя свойства 'suggest_output' на 'name'.
	 *
	 * @param optionsArray - массив хэшей совпадений (см. fixtures/autocomplete-answer-api.json)
	 * @returns {Array}
	 */
	AutoComplete.prototype.getParsedAPIAnswer = function(optionsArray){
		var answer = [];
		_.forEach(optionsArray, function(item){
			answer.push(_.mapKeys(item, function(v, k){
				if(k === 'suggest_output'){
					k = 'name';
				}
				return k;
			}));
		});
		return answer;
	};

	/**
	 * Возвращает обработанный ответ Google Autocomplete. Берем оттуда только description и place_id
	 *
	 * @param array {Array<AutocompletePrediction>} - массив совпадений от гугла (https://developers.google.com/maps/documentation/javascript/3.exp/reference#AutocompletePrediction)
	 * @returns {Array}
	 */
	AutoComplete.prototype.getParsedPlacesArray = function (array){
		var parsedArray = [];
		_.forEach(array, function(value){
			parsedArray.push(
				{
					name    : value.description,
					place_id: value.place_id
				}
			);
		});
		return parsedArray;
	};

	/**
	 * Запрашивает Google maps place
	 * https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlaceDetailsRequest
	 *
	 * @param placeId - google place_id
	 * @returns {Promise}
	 */
	AutoComplete.prototype.getPlaceDetails = function (placeId){
		var deferred = $.Deferred();
		this.placeService.getDetails({placeId: placeId}, function(result, status){
			status === "OK" ? deferred.resolve(result) : deferred.reject(status); // jshint ignore:line, strict:true
		});
		return deferred.promise();
	};

	/**
	 * Вытягивает нужные для фильтрации и карты параметры из GooglePlace объекта.
	 * Пример:
	 * {
	 *   location_shape: {
	 *      country_code: 'XX',
	 *      bottom_left: {
	 *        lat: 50.08783340000001,
	 *        lon: 14.42104889999996
	 *      }
	 *      top_right: {
	 *        lat: 50.08783340000001,
	 *        lon: 14.42104889999996
	 *      }
	 *   },
	 *   location_point: {
	 *      country_code: 'XX',
	 *      lat: 50.08783340000001,
	 *      lon: 14.42104889999996
	 *   },
	 *   place_id: 'ChIJi3lwCZyTC0cRIKgUZg-vAAE'
	 * }
	 *
	 * @param place - https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlaceResult
	 * @returns {*}
	 */
	AutoComplete.prototype.getCoordinatesFromGooglePlace = function(place){
		var systemPlace = {},
		    countryComponent = _.find(place.address_components, function(v){
			    return _.includes(v.types, "country");
		    });

		if(place && place.geometry){
			if(place.geometry.location){
				systemPlace.location_point = {
					lat: place.geometry.location.lat(),
					lon: place.geometry.location.lng()
				};
				if(countryComponent && countryComponent.short_name){
					systemPlace.location_point.country_code = countryComponent.short_name;
				}
			}

			if(place.geometry.viewport && !_.includes(place.types, "point_of_interest")){
				var ne = place.geometry.viewport.getNorthEast(),
				    se = place.geometry.viewport.getSouthWest();

				systemPlace.location_shape = {
					top_right: {
						lat: ne.lat(),
						lon: ne.lng()
					},
					bottom_left: {
						lat: se.lat(),
						lon: se.lng()
					}
				};

				if(countryComponent && countryComponent.short_name){
					systemPlace.location_shape.country_code = countryComponent.short_name;
				}

				if(!this.isValidLocationShape(systemPlace.location_shape)){
					systemPlace.location_shape = null;
				}
			}

			systemPlace.place_id = place.place_id;
		}
		return _.keys(systemPlace).length > 0 ? systemPlace : null;
	};

	/**
	 * Проверяет наличие нужных параметров и проверяет размер шейпа до сотых
	 * @param locationShape
	 * @returns {boolean}
	 */
	AutoComplete.prototype.isValidLocationShape = function(locationShape){
		if(
			!locationShape ||
			!(_.has(locationShape, 'top_right.lat') && _.has(locationShape, 'bottom_left.lat'))
		){
			return false;
		}

		try {
			// compare values with 2 digits after comma
			// both lat and lon should be unequal
			return locationShape.top_right.lat.toFixed(2) !== locationShape.bottom_left.lat.toFixed(2) && locationShape.top_right.lon.toFixed(2) !== locationShape.bottom_left.lon.toFixed(2);
		}catch(error){
			return false;
		}
	};

	/**
	 * Setter для autocompleteSelected. Так же ложит текст в инпут автокомплита.
	 *
	 * @param item {Object} - объект с google координатами или ответом апи на автокомплит, подготовленные для запроса объектов (см. вызовы метода setSelected)
	 * @param inputText {string} - строка для показа в инпуте
	 * @param ignoreCallback {Boolean} - используется при сбросе всех тэгов, т.к. делаем триггер на form submit
	 */
	AutoComplete.prototype.setSelected = function(item, inputText, ignoreCallback) {
		this.autocompleteSelected = item;
		if(this.category === 'invest') {
			this.invest.onClusterSelect({
				location: item,
				text: inputText
			});
		} else {
			this.jqInput.val(inputText || '').change();
			this.callback(item, ignoreCallback);
		}
	};

	/**
	 * Возвращает отформатированный и сокращенный адрес, который содержит только компоненты в var order;
	 *
	 * @param place - https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlaceResult
	 * @returns {string}
	 */
	AutoComplete.prototype.getPlaceText = function (place){
		var order = [
			    "country",
			    "administrative_area_level_1",
			    "locality"
		    ],
		    parts = [];
		_.forEach(
			order, function(target){
				var piece = _.find(
					place.address_components, function(component){
						return _.includes(component.types, target);
					}
				);

				if(piece && piece.long_name){
					parts.push(piece.long_name);
				}
			}
		);
		return parts.join(", ") || place.formatted_address;
	};

	/**
	 * Success Callback for getMatches
	 * @param query
	 * @param processCallback
	 * @param {Array} apiAnswer - jquery ajax answer
	 * @see http://api.jquery.com/jQuery.ajax/ success/error
	 * @param {Array} dataGoogle
	 */
	AutoComplete.prototype.getMatchesSuccess = function(query, processCallback, apiAnswer, dataGoogle){
		var items = [];

		console.debug('getMatchesSuccess', apiAnswer, dataGoogle);
		var apiData = (apiAnswer[0]) ? JSON.parse(apiAnswer[0]) : false;
		if(apiData.length > 0){
			items = items.concat(this.getParsedAPIAnswer(apiData));
		}
		if(dataGoogle){
			items = items.concat(dataGoogle);
		}

		this.toggleNoResultsMessage(items.length === 0);
		processCallback(items);
	};

	/**
	 * Error Callback for getMatches
	 * @param query
	 * @param processCallback
	 */
	AutoComplete.prototype.getMatchesError = function(query, processCallback) {
		this.toggleNoResultsMessage(true);
		processCallback([]);
	};

	/**
	 * Success Callback для askAPI.
	 * Если есть ответ с совпадениями, то отдаем в Typeahead. Если нет - делаем запрос к GoogleAPI
	 *
	 * @param query - значение из инпута автокомплита
	 * @param processCallback - суперр-пупер ресолвер плагина typeahead, он получает массив готовых итемов-совпадений
	 * @param data - ответ
	 * @param textStatus - см. аргументы jQuery.ajax.success
	 * @param jqXHR - см. аргументы jQuery.ajax.success
	 *
	 * @deprecated
	 */
	AutoComplete.prototype.askAPISuccess = function(query, processCallback, data, textStatus, jqXHR){
		var jsonData = (data) ? JSON.parse(data) : false;
		if(jsonData.length > 0){
			var items = this.getParsedAPIAnswer(jsonData);
			processCallback(items);
		}else{
			this.askGoogleAPI(query)
				.done(this.askGoogleAPISuccess.bind(this, query, processCallback))
				.fail(this.askGoogleAPIError.bind(this, query, processCallback));
		}
	};

	/**
	 * Error Callback для askAPI
	 * Если попали сюда - делаем запрос к GoogleAPI
	 *
	 * @param query - значение из инпута автокомплита
	 * @param processCallback - суперр-пупер ресолвер плагина typeahead, он получает массив готовых итемов-совпадений
	 * @param jqXHR - см. аргументы jQuery.ajax.error
	 * @param textStatus - см. аргументы jQuery.ajax.error
	 * @param errorThrown - см. аргументы jQuery.ajax.error
	 *
	 * @deprecated
	 */
	AutoComplete.prototype.askAPIError = function(query, processCallback, jqXHR, textStatus, errorThrown){
		this.askGoogleAPI(query)
			.done(this.askGoogleAPISuccess.bind(this, query, processCallback))
			.fail(this.askGoogleAPIError.bind(this, query, processCallback));
	};

	/**
	 * Success Callback для askGoogleAPI
	 *
	 * @param query - значение из инпута автокомплита
	 * @param processCallback - суперр-пупер ресолвер плагина typeahead, он получает массив готовых итемов-совпадений
	 * @param array - массив готовых совпадений для typeahead
	 *
	 * @deprecated
	 */
	AutoComplete.prototype.askGoogleAPISuccess = function(query, processCallback, array){
		processCallback(array); //отдаем итемы в typeahead
	};

	/**
	 * Error Callback для askGoogleAPI.
	 * Если мы попали сюда - нужно выводить Not Found
	 *
	 * @param query - значение из инпута автокомплита
	 * @param processCallback - суперр-пупер ресолвер плагина typeahead, он получает массив готовых итемов-совпадений
	 * @param statusString - строка статуса ответа от гугла (https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlacesServiceStatus)
	 *
	 * @deprecated
	 */
	AutoComplete.prototype.askGoogleAPIError = function(query, processCallback, statusString){
		console.debug('askGoogleAPIError', query, processCallback, statusString);
	};

	/**
	 * Callback для метода afterSelect typeahead (см. метод attachTypeAheadPlugin)
	 *
	 * @param item
	 */
	AutoComplete.prototype.afterSelect = function(item) {
		if(item && item.parent_id){ //ответ от АПИ
			this.setSelected({
				l_id: item.parent_id,
				l_type: "PropertyObject"
			}, item.code);
		}
		else if(item && item.place_id){ // ответ от гугла
			this.getPlaceDetails(item.place_id)
				.done(this.getPlaceDetailsSuccess.bind(this))
				.fail(this.getPlaceDetailsError.bind(this));
		}
	};

	/**
	 * Success Callback для getPlaceDetails
	 *
	 * https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlaceResult
	 * @param place - https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlaceResult
	 */
	AutoComplete.prototype.getPlaceDetailsSuccess = function(place) {
		this.setSelected(this.getCoordinatesFromGooglePlace(place), this.getPlaceText(place));
	};

	/**
	 * Error Callback для getPlaceDetails
	 *
	 * @param status - https://developers.google.com/maps/documentation/javascript/3.exp/reference#PlacesServiceStatus
	 */
	AutoComplete.prototype.getPlaceDetailsError = function(status) {
		console.error('getPlaceDetailsError');
	};

	/**
	 * Scrolls dropdown on down/up arrow keys press
	 * @param e - keydown event
	 */
	AutoComplete.prototype.scrollOnKeydown = function(e) {
		var dropdown = $(this).siblings('.sp-search-dropdown'),
		    active = dropdown.find('.active'),
		    activeTop,
		    dropdownScrolltop,
		    dropdownHeight = dropdown.height();

		if(active.length > 0){
			if(e.keyCode === 40 || e.keyCode === 38){
				dropdownScrolltop = dropdown.scrollTop();
				activeTop = active.position().top;

				if(activeTop > dropdownHeight){
					if(dropdownScrolltop > dropdownHeight){//out of viewport - down
						dropdown.scrollTop(activeTop + dropdownScrolltop);
					}else {
						dropdown.scrollTop(activeTop);
					}
				}else if(activeTop < 0){//out of viewport - top
					if(e.keyCode === 38){//down arrow
						dropdown.scrollTop(dropdownScrolltop - (activeTop * -1));
					}else {
						dropdown.scrollTop(0);
					}
				}
			}

		}
	};

	/**
	 * Hides No results message
	 */
	AutoComplete.prototype.onBlur = function() {
		this.toggleNoResultsMessage(false);
	};

	/**
	 * Toggles No results message visibility
	 * @param {Boolean} bool
	 */
	AutoComplete.prototype.toggleNoResultsMessage = function(bool) {
		$('#autocomplete-no-results').toggle(bool);
	};

	/**
	 * Overrides `render` method of Typeahed plugin to add css class for google items.
	 * It's copy+paste version of Typeahed.render method, because `render` method inserts items directly in DOM
	 * and returns only `this` reference.
	 *
	 *
	 * @param {Array} items
	 * @returns {Object} - Typeahead instance
	 *
	 * @see bower_components/bootstrap3-typeahead/bootstrap3-typeahead.js:259 (original render method)
	 */
	AutoComplete.prototype.renderForTypeahead = function(items) {
		var that = this;
		var self = this;
		var activeFound = false;
		var data = [];
		var _category = that.options.separator;
		var hasApiItems = false;

		$.each(items, function (key,value) {
			// inject separator
			if (key > 0 && value[_category] !== items[key - 1][_category]){
				data.push({
					__type: 'divider'
				});
			}

			// inject category header
			if (value[_category] && (key === 0 || value[_category] !== items[key - 1][_category])){
				data.push({
					__type: 'category',
					name: value[_category]
				});
			}
			data.push(value);
		});

		items = $(data).map(function (i, item) {
			if ((item.__type || false) == 'category'){
				return $(that.options.headerHtml).text(item.name)[0];
			}

			if ((item.__type || false) == 'divider'){
				return $(that.options.headerDivider)[0];
			}

			var text = self.displayText(item);
			i = $(that.options.item).data('value', item);
			i.find('a').html(that.highlighter(text, item));
			if (text == self.$element.val()) {
				i.addClass('active');
				self.$element.data('active', item);
				activeFound = true;
			}

			// set hasApiItems to true once
			if(!hasApiItems && item.parent_id){
				hasApiItems = true;
			}
			// add pbgoogle class for google matches
			if(item.place_id){
				i.addClass('pbgoogle');
			}
			return i[0];
		});

		//add item-divider class
		if(hasApiItems){
			items.filter('.pbgoogle').first().addClass('item-divider');
		}

		if (this.autoSelect && !activeFound) {
			items.filter(':not(.dropdown-header)').first().addClass('active');
			this.$element.data('active', items.first().data('value'));
		}
		this.$menu.html(items);
		return this;

	};

	window.lpw = window.lpw || {};
	window.lpw.AutoComplete = AutoComplete;

})(jQuery);