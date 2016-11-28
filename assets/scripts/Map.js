(function($) {
	"use strict";

	/**
	 * Карта
	 * @param geoPointsAPIPath {String} - uri to geo points controller
	 * @param category {String} - category: rent/sale
	 * @param autoComplete - экземпляр класса AutoComplete
	 * @constructor
	 */
	function Map(
		mapModal,
	    category,
	    autoComplete
	){
		var $this = this;
		this.mapModal = $(mapModal);
		this.category = category;
		this.autoComplete = autoComplete;

		this.geopointsError = false;
		this.points = null;
		this.markerCluster = null;

		this.mapOptions = {
			zoom: 3,
			scrollwheel: false,
			center: new google.maps.LatLng(49.1651567,4.0557516),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		//опции для js-marker-clusterer
		this.markerClusterOptions = {
			gridSize: 40,
			imagePath: 'https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m',
			minimumClusterSize: 1,
			styles: [
				{
					textColor: '#fff',
					height: 53,
					width: 53,
					url: "https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m1.png"
				},
				{
					textColor: '#fff',
					height: 56,
					width: 56,
					url: "https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m2.png"
				},
				{
					textColor: '#fff',
					height: 66,
					width: 66,
					url: "https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m3.png"
				},
				{
					textColor: '#fff',
					height: 78,
					width: 78,
					url: "https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m4.png"
				},
				{
					textColor: '#fff',
					height: 90,
					width: 90,
					url: "https://s3-eu-west-1.amazonaws.com/test-website-v3/images/m5.png"
				}
			]
		};

		if(this.category === 'sale' || this.category === 'invest') {
			this.mapOptions.maxZoom = this.markerClusterOptions.maxZoom = 8;
		} else if(this.category === 'rent') {
			this.mapOptions.maxZoom = this.markerClusterOptions.maxZoom = 18;
		}
		if(this.category === 'invest' ) {
			this.map = new google.maps.Map(
				document.getElementById('invest-map'),
				$this.mapOptions
			);

		} else {
			this.mapModal.on('shown.bs.modal', function () {
				if (!$this.map) {
					//инит карты

					$this.map = new google.maps.Map(
						document.getElementById('map-canvas'),
						$this.mapOptions
					);
					if (!$this.geopointsError) {
						$this.getGeoPointsSuccess($this.geoPoints);
					}
				} else {
					google.maps.event.trigger($this.map, "idle");
				}
			});
		}
		//запрашиваем геопоинты
		if(this.category === 'invest') {
			this.getGeoPoints()
				.done(this.getGeoPointsSuccess.bind(this))
				.fail(this.getGeoPointsError.bind(this));
		} else {
			this.getGeoPoints()
				.fail(this.getGeoPointsError.bind(this));
		}

	}

	/**
	 *
	 */
	Map.prototype.setupMarkerCluster = function(){
		var markers = [],
		    $this = this;

		//собираем все поинты в один массив
		for (var i = 0; i < this.points.length; i++) {
			var latLng = new google.maps.LatLng(this.points[i].location.lat, this.points[i].location.lon);
			markers.push(
				new google.maps.Marker(
					{
						position: latLng,
						place: {
							location: latLng,
							placeId: this.points[i].place_id
						}
					}
				)
			);
		}
		//создаем инстанс MarkerClusterer
		this.markerCluster = new MarkerClusterer(this.map, markers, this.markerClusterOptions);

		//цепляем ивент на клик по кластеру на карте
		google.maps.event.addListener(this.markerCluster, 'clusterclick', this.onClusterClick.bind(this));
	};

	/**
	 * Вытягивает необходимые данные из кластера (place_id, bound), подготавливает и вызывает AutoComplete.setSelected
	 *
	 * @param cluster
	 */
	Map.prototype.onClusterClick = function(cluster){
		var $this = this;
		if(this.map.getZoom() === $this.mapOptions.maxZoom){//когда достигли максимально допустимого зума
			var place = cluster.markers_[0].getPlace();
			if(place && place.placeId){
				var shape = this.getShapeFromBound(cluster.getBounds());
				this.autoComplete.getPlaceDetails(place.placeId).done(function(place){
					var coordinates = $this.autoComplete.getCoordinatesFromGooglePlace(place);
					if(shape && $this.autoComplete.isValidLocationShape(shape)){
						coordinates.location_shape = shape;
					}
					$this.autoComplete.setSelected(coordinates, $this.autoComplete.getPlaceText(place));
					//google.maps.event.trigger($this.map, "idle"); //иногда пропадают кластеры - так что пинаем чтобы перерендерить // Moved to modal event listentr
				});
			}
			if(this.category === 'invest') {

			} else {
				this.mapModal.modal('hide');
			}

		}
	};

	/**
	 * Возвращает bound в нужном нам формате
	 *
	 * @param bound - https://developers.google.com/maps/documentation/javascript/reference#LatLngBounds
	 * @returns {{top_right: {lat: *, lon: *}, bottom_left: {lat: *, lon: *}}}
	 */
	Map.prototype.getShapeFromBound = function(bound) {
		var ne = bound.getNorthEast(),
		    se = bound.getSouthWest();
		return {
			top_right: {
				lat: ne.lat(),
				lon: ne.lng()
			},
			bottom_left: {
				lat: se.lat(),
				lon: se.lng()
			}
		};
	};

	/**
	 * Запрашивает геопоинты
	 *
	 * @returns {Promise}
	 */
	Map.prototype.getGeoPoints = function() {
		var $this = this;
		var data = {
			action: 'do_ajax',
			fn: 'get_geopoints',
			type: this.category
		};
		return $.get(LpData.ajaxUrl, data, function(data) {
			$this.geoPoints = data;
		});
	};

	/**
	 * Success Callback для getGeoPoints
	 * сторит в поинты в this.points и инициализирует MarkerClusterer
	 *
	 * @param answer
	 *
	 * Пример answer (массив точек):
	 * "points": [
	 * {
	 *  "id": 85292,
	 *  "location": {
	 *      "lat": 43.2629288,
	 *      "lon": 11.820809
     *   },
	  * "place_id": "ChIJhU6y6vXjKxMRmov6RP_auuc"
	 * },
	 * ...
	 * ]
	 */
	Map.prototype.getGeoPointsSuccess = function(answer) {
		var jsonData = (answer) ? JSON.parse(answer) : false;
		if(jsonData.points && jsonData.points.length > 0){
			this.points = jsonData.points;
			this.setupMarkerCluster();
		}
	};

	/**
	 * Error Callback для getGeoPoints
	 * @param error
	 */
	Map.prototype.getGeoPointsError = function(error) {
		this.geopointsError = true;
		console.log('getGeoPointsError', error);
	};

	Map.prototype.mapReset = function() {
		this.map.setCenter(this.mapOptions.center);
		this.map.setZoom(this.mapOptions.zoom);
	};

	//для доступности в глобальном скоупе
	window.lpw = window.lpw || {};
	window.lpw.Map = Map;

})(jQuery);