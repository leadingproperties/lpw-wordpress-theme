(function($){
	"use strict";
	/**
	 * AutoComplete
	 * @param inputSelectorString - строка с селектором для jQuery
	 * @param callback - callback function
	 * @param category -ex. sale, rent, invest
	 * @param invest - if it is Invest object
	 * @param rent_category - long or short rent
	 * @constructor
	 */
	function AutoComplete(
		inputSelectorString,
		callback, category, invest, rent_category
	) {
		this.jqInput = $(inputSelectorString);
		this.callback = callback;
		this.category = category;
		this.autocompleteSelected = null;
		this.invest = invest;
		this.rentCategory = rent_category;
		this.errors = {
			google: false
		};
		this.tipsRequestConfig = {
			url: LpData.ajaxUrl,
			data: {
				action: 'do_ajax',
				fn: 'get_tips',
				scope: category === 'rent' ? 'for_rent' : 'for_sale'
			}
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
			new window.lpw.TypeaheadOverrides();
			this.attachTypeAheadPlugin();
			this.setupTips();
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
				minLength: 0,
				showHintOnFocus: 'all',
				// override default sorter (causes WP-109)
				sorter: function(items) {
					return items;
				},
				source: function(query, process){
					if(query && query.length > 0){
						$this.getMatches(query)
							.done($this.getMatchesSuccess.bind($this, query, process))
							.fail($this.getMatchesError.bind($this, query, process));
					}else{
						$this.getTips().then($this.getTipsSuccess.bind($this, process), $this.getTipsError.bind($this, process));
					}
				}
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
			data.scope = (this.rentCategory) ? this.rentCategory : 'long_rent';
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
				var answer = [];
				if(status === "OK"){
					answer = $this.getParsedPlacesArray(array);
				}
				deferred.resolve(answer);
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
					place_id: value.place_id,
					_cssClass: 'pbgoogle'
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
			    "locality",
			    "administrative_area_level_3",
			    "sublocality_level_1",
			    "sublocality_level_2",
			    "neighborhood"
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

		var apiData = (apiAnswer[0]) ? JSON.parse(apiAnswer[0]) : false;
		if(apiData.length > 0){
			items = items.concat(this.getParsedAPIAnswer(apiData));
		}
		if(dataGoogle){
			items = items.concat(dataGoogle);
		}

		if(items.length === 0){
			items = this.getNoResultsItems();
		}
		processCallback(items);
	};

	/**
	 * Error Callback for getMatches
	 * @param query
	 * @param processCallback
	 */
	AutoComplete.prototype.getMatchesError = function(query, processCallback) {
		processCallback(this.getNoResultsItems());
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
				.fail(this.getPlaceDetailsError.bind(this, status, item));
		}else if(item && item._type === 'tip'){ //подсказка
			var params = {};
			if (item.location_shape){
				params.location_shape = item.location_shape;
			}else if(item.location_point){
				params.location_point = item.location_point;
			}
			this.setSelected(params, item.name);
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
	 * @param item - выбранный объект из списка typehead
	 */
	AutoComplete.prototype.getPlaceDetailsError = function(status, item) {
		var name = item.name ? item.name : null;
		item.place_error = true;
		this.setSelected(item, name);
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
	 * Returns already loaded tips (resolves as promise) or makes request to server
	 * @returns {Promise}
	 */
	AutoComplete.prototype.getTips = function() {
		var defer = $.Deferred(),
		    $this = this;
		if(this.tips && this.tips.length > 0){
			defer.resolve(this.tips);
		}else{
			$.getJSON(
				this.tipsRequestConfig.url,
				this.tipsRequestConfig.data,
				function(answer){
					defer.resolve($this.getParsedTips(answer));
				}
			);
		}
		return 	defer.promise();
	};

	/**
	 * Retrieves tips. Used in constructor.
	 */
	AutoComplete.prototype.setupTips = function() {
		this.getTips().then(this.getTipsSuccess.bind(this, null), this.getTipsError.bind(this, null));
	};

	/**
	 * Parses raw server answer and returns array of tips as items for Typeahead
	 * @param {Object} rawTips
	 * @returns {Array}
	 */
	AutoComplete.prototype.getParsedTips = function(rawTips) {
		var tips = [],
		    $this = this;
		tips.push({
			name: rawTips.search_string,
			_type: 'dropdownHeader',
			_cssClass: 'dropdown-header high-dropdown-header'
		});
		_.forEach(rawTips.tips, function(tip) {
			var structuredTip = {
				name: tip.query_text,
				counter: tip.property_objects_total,
				_type: 'tip'
			};
			if($this.tipIsShape(tip.query_geo_shape.coordinates)){
				structuredTip.location_shape = $this.elasticSearchGeoShapeToLocationShape(tip.query_geo_shape.coordinates);
			}else{
				structuredTip.location_point = $this.elasticSearchGeoShapeToLocationPoint(tip.query_geo_shape);
			}
			tips.push(structuredTip);
		});

		this.tips = tips;
		return tips;
	};

	/**
	 * Converts elasticSearch geoShape to API location_shape request format
	 * @param geoShape
	 * @returns {*}
	 */
	AutoComplete.prototype.elasticSearchGeoShapeToLocationShape = function (geoShape){
		if(!(_.isArray(geoShape) && geoShape[1] && geoShape[1].length === 2)){
			return null;
		}
		return {
			top_right: {
				lat: geoShape[1][1],
				lon: geoShape[0][0]
			},
			bottom_left: {
				lat: geoShape[0][1],
				lon: geoShape[1][0]
			}
		};
	};

	/**
	 * Converts elasticSearch geoShape to API location_point request format
	 * @param geoShape
	 * @returns {*}
	 */
	AutoComplete.prototype.elasticSearchGeoShapeToLocationPoint = function (geoShape) {
		if ( !(geoShape && (_.isArray(geoShape.coordinates) && geoShape.type === 'circle')) ) {
			return null;
		}
		return {
			lat : geoShape.coordinates[1],
			lon : geoShape.coordinates[0],
			radius: geoShape.radius.replace(/\D/g, '')
		};
	};

	/**
	 * Check geoShape is shape indeed
	 * @param geoShape
	 * @returns {*|boolean}
	 */
	AutoComplete.prototype.tipIsShape = function (geoShape){
		return _.isArray(geoShape) && _.isArray(geoShape[0]);
	};

	/**
	 * Success callback for getTips.
	 * Calls Typeahead process callback (if present) with prepared items.
	 * @param process - Typeahead plugin callback
	 * @param tips
	 */
	AutoComplete.prototype.getTipsSuccess = function(process, tips) {
		if(process && _.isFunction(process)){
			process(tips);
		}
	};

	/**
	 * Error callback for getTips
	 * Calls Typeahead process callback (if present) with empty array.
	 * @param process - Typeahead plugin callback
	 * @param error
	 */
	AutoComplete.prototype.getTipsError = function(process, error) {
		console.error('getTipsError', error);
		if(process && _.isFunction(process)){
			process([]);
		}
	};

	/**
	 * Returns array of items for Typeahead in case of no results
	 * @returns {Array}
	 */
	AutoComplete.prototype.getNoResultsItems = function() {
		var answer = [];
		answer.push({
			name: 'No results',
			_type: 'noResults',
			_cssClass: 'dropdown-header text-red'
		});
		_.forEach(this.tips, function(tip) {
			if(tip._type === 'tip'){
				answer.push(tip);
			}
		});
		return answer;
	};

	window.lpw = window.lpw || {};
	window.lpw.AutoComplete = AutoComplete;

})(jQuery);