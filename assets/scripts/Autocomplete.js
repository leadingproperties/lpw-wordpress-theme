(function($){
	"use strict";
	window.lpw = window.lpw || {};
	window.lpw.AutoComplete = AutoComplete;

	/**
	 * AutoComplete
	 * @param apiPath - путь к контроллеру, который опрашивает апи на совпадения
	 * @param inputSelectorString - строка с селектором для jQuery
	 * @constructor
	 */
	function AutoComplete(
		apiPath,
		inputSelectorString,
		callback
	){
		this.apiPath = apiPath;
		this.jqInput = $(inputSelectorString);
		this.callback = callback;

		this.autocompleteSelected = null;
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
		this.attachTypeAheadPlugin();
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
				fitToElement: true,
				matcher: function(item){ //нам не нужна доп. фильтрация совпадений плагином, так что переназначаем нативный метод
					return true;
				},
				menu: '<ul class="sp-search-dropdown" role="listbox"></ul>',
				item: '<li><a href="#" role="option"></a></li>',
				afterSelect: $this.afterSelect.bind($this),
				source: function(query, process){
					$this.askAPI(query, process)
						.done($this.askAPISuccess.bind($this, query, process))
						.fail($this.askAPIError.bind($this, query, process));
				}
			}
		);
	};

	/**
	 * Запрашивает совпадения у API (тот что через вордпресс).
	 *
	 * @param query - значение из инпута автокомплита
	 * @returns {Promise} - jQuery promise (http://api.jquery.com/Types/#Deferred)
	 */
	AutoComplete.prototype.askAPI = function(query){
		return $.ajax(
			{
				url: this.apiPath,
				data: {
					query: query,
					dataType: 'json',
					action: 'do_ajax',
					fn: 'get_suggestions'
				}
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
	 * Меняет имя свойства 'text' на 'name'.
	 *
	 * @param optionsArray - массив хэшей совпадений (см. fixtures/autocomplete-answer-api.json)
	 * @returns {Array}
	 */
	AutoComplete.prototype.getParsedAPIAnswer = function(optionsArray){
		var answer = [];
		_.forEach(optionsArray, function(item){
			answer.push(_.mapKeys(item, function(v, k){
				if(k === 'text'){
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
			}

			systemPlace.place_id = place.place_id;
		}
		return _.keys(systemPlace).length > 0 ? systemPlace : null;
	};

	/**
	 * Setter для autocompleteSelected. Так же ложит текст в инпут автокомплита.
	 *
	 * @param item {Object} - объект с google координатами или ответом апи на автокомплит, подготовленные для запроса объектов (см. вызовы метода setSelected)
	 * @param inputText {string} - строка для показа в инпуте
	 */
	AutoComplete.prototype.setSelected = function(item, inputText) {
		this.autocompleteSelected = item;
		if(inputText){
			this.jqInput.val(inputText).change();
		}
		if(item) {
			this.callback(item);
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
		return parts.join(", ");
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
	 */
	AutoComplete.prototype.askAPISuccess = function(query, processCallback, data, textStatus, jqXHR){
		//console.debug(typeof jqXHR.responseText);
		var jsonData = (data) ? JSON.parse(data) : false;
		if(jsonData.options && jsonData.options.length > 0){
			var items = this.getParsedAPIAnswer(jsonData.options);
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
		if(item && item.payload){ //ответ от АПИ
			this.setSelected({
				l_id: item.payload.property_object._id,
				l_type: item.payload.property_object.type
			}, item.payload.property_object.name);
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
})(jQuery);