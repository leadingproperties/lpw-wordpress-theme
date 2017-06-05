(function($){
    "use strict";

    /**
     * ObjectList
     * @param type - строка c типом листинга list - обычный листинг, favorites - листинг обьектов favorites, share - листинг обьектов share
     * @param category - sale, rent
     * @param rent_category - 'long_rent', 'short_rent'
     */

    function ObjectList(type, category, rent_category) {
        var $this = this,
            loader = $('.loader'),
            singleObject = new window.lpw.SingleObject(this);

        function resetObjects() {
            //this.objectContainer.html('');
            $('.object-item').remove();
            $this.args.page = 1;
            $this.onPage = 0;
            $this.totalObjects = parseInt(LpData.totalObjects);
            $(window).on('scroll.lprop', $this.scrollPage);
        }

        function setOffmarker(count) {
            var omLink = $('.off-market-menu a'),
                omMenu = $('.menu-offmarket'),
                omFb = $('.off-market-link'),
                omPanel = $('.off-marker-alert');
            if(count > 0) {
                omLink
                    .off('click', window.lpw.Helpers.preventDefault)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                omMenu
                    .on('click', window.lpw.Helpers.showOffmarketModal)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                omFb
                    .on('click', window.lpw.Helpers.showOffmarketModal)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                setTimeout(function() {
                    omPanel.show();
                }, 2500);
            } else {
                omLink
                    .on('click', window.lpw.Helpers.preventDefault)
                    .addClass('half-opaque')
                    .find('sup')
                    .text('');
                omMenu
                    .off('click', window.lpw.Helpers.showOffmarketModal)
                    .addClass('half-opaque')
                    .find('sup')
                    .text('');
                omFb
                    .off('click', window.lpw.Helpers.showOffmarketModal)
                    .addClass('half-opaque')
                    .find('sup')
                    .text('');
                omPanel.hide();
            }

        }

        function clearAutoSearch() {
            if($this.args.ids) {
                delete $this.args.ids;
            }
            if($this.args.location_point) {
                delete $this.args.location_point;
            }
            if($this.args.location_shape) {
                delete $this.args.location_shape;
            }
	        if($this.args.place_id) {
		        delete $this.args.place_id;
	        }
            if($this.args.similar && ! ( $this.args.location_point && $this.args.location_point.radius ) ) {
                delete $this.args.similar;
            }
            $this.usedFilters.location = false;
        }
        function clearFilters() {
            if($this.args.price.min) {
                delete $this.args.price.min;
            }
            if($this.args.price.max) {
                delete $this.args.price.max;
            }
            if($this.args.rooms) {
                delete $this.args.rooms;
            }
            if($this.args.area) {
                delete $this.args.area;
            }
            if($this.args.property_types) {
                delete $this.args.property_types;
            }
            if($this.args.rooms) {
                delete $this.args.rooms;
            }
            if($this.args.hd_photos) {
                delete $this.args.hd_photos;
            }
            if($this.args.persons) {
                delete $this.args.persons;
            }

            if($this.args.child_friendly) {
                delete $this.args.child_friendly;
            }
            if($this.args.pets_allowed) {
                delete $this.args.pets_allowed;
            }
            if($this.args.order_by) {
                delete $this.args.order_by;
            }
        }
        function clearAllFilters() {
            clearAutoSearch();
            clearFilters();
            if($this.args.autocomplete) {
                delete $this.args.autocomplete;
            }
            $this.tags.autoComplete.autocompleteSelected = null;
            $this.tags.autoComplete.jqInput.val(undefined);
            filter.setValues($this.args);
            $this.setUrls($this.args);
        }

        this.filter = new window.lpw.FilterMenu(type, category, rent_category);

        this.autoSearch = function(data, silent) {
            resetObjects();
            clearAutoSearch();
            if(data) {
                if (data.l_id) {
                    $this.args.ids = [data.l_id];
                    $this.usedFilters.location = false;
                } else {
                    if (data.location_point || data.location_shape) {
                        $this.usedFilters.location = true;
                    }
                    if (data && data.location_point) {
                        $this.args.location_point = {};
                        if (data.location_point.country_code) {
                            $this.args.location_point.country_code = data.location_point.country_code;
                        }
                        if (data.location_point.lat) {
                            $this.args.location_point.lat = data.location_point.lat;
                        }
                        if (data.location_point.lon) {
                            $this.args.location_point.lon = data.location_point.lon;
                        }
	                    if (data.place_id) {
		                    $this.args.place_id = data.place_id;
	                    }

                        if ($this.lpwGoogleMap.map && $this.lpwGoogleMap.map instanceof google.maps.Map) {
                            $this.lpwGoogleMap.map.setCenter({
                                lat: data.location_point.lat,
                                lng: data.location_point.lon
                            });
                            $this.lpwGoogleMap.map.setZoom(9);
                        } else if ($this.lpwGoogleMap.mapOptions) {
                            $this.lpwGoogleMap.mapOptions.center = new google.maps.LatLng(data.location_point.lat, data.location_point.lon);
                            $this.lpwGoogleMap.mapOptions.zoom = 10;
                        }
                    }
                    if (data && data.location_shape) {
                        $this.args.location_shape = {};
                        if (data.location_shape.country_code) {
                            $this.args.location_shape.country_code = data.location_shape.country_code;
                        }
                        if (data.location_shape.bottom_left && data.location_shape.bottom_left.lat && data.location_shape.bottom_left.lon) {
                            $this.args.location_shape.bottom_left = {
                                lat: data.location_shape.bottom_left.lat,
                                lon: data.location_shape.bottom_left.lon
                            };
                        }
                        if (data.location_shape.top_right && data.location_shape.top_right.lat && data.location_shape.top_right.lon) {
                            $this.args.location_shape.top_right = {
                                lat: data.location_shape.top_right.lat,
                                lon: data.location_shape.top_right.lon
                            };
                        }
                    }
                }

                // Если Google API не вернул координаты
                $this.place_error = !!(data.place_error);
            }

            if(!silent){
                $this.getObjects();
            }
        };
        if(type === 'list') {
            this.autoComplete = new window.lpw.AutoComplete(
                '#sp-search',
                $this.autoSearch,
                category,
                null,
                rent_category
            );
            this.lpwGoogleMap = new window.lpw.Map(
                '#map-modal',
                category,
                $this.autoComplete,
                rent_category
            );
            this.tags = new window.lpw.Tags(
                LpData.ajaxUrl,
                $this.autoComplete,
                this.filter.filterForm,
                this.filter.filterSorting
            );
        }
        this.lastItem = function() {
            return $('.object-item').last();
        };
        this.globalCurrencySwitcher = $('#global-currency-switcher');
        this.type = type;
        this.favorites = new window.lpw.Favorites(type, category);
        this.favoritesIds = $this.favorites.favoritesIds;
        this.objectContainer = $('#object-list');
        this.onPage = 0;
        this.totalObjects = parseInt(LpData.totalObjects);
        this.didScroll = false;
        this.triggerId = 0;
        this.usedFilters = {
            location: false,
            filter: false
        };
        this.place_error = false;
        this.args = {
            lang: LpData.lang,
            page: 1,
            per_page: 9,
            price: {
               currency: LpData.currency_id
            },
            for_sale: ( category === 'sale' ),
            for_rent: ( category === 'rent' )
        };

        this.renderHTML = function(objects) {
            var r = $.Deferred(),
                $objects = $(objects);
            var noMatches = $('.no-matches');
            if(noMatches.length > 0) {
                noMatches.remove();
            }
            if($this.totalObjects > 0 ) {

                $this.objectContainer.append($objects);
                $this.favorites.markButtons($objects, $this.favorites.favoritesIds);

            } else if ($this.usedFilters.location || $this.usedFilters.filter ) {
                $objects.insertBefore($this.objectContainer);
            }
            r.resolve();
            return r;
        };
        this.getObjects = function (callback, eventType) {
            loader.show();
            // Check if we use filters and set flag if any

            $this.isFiltersActive();

            if($this.didScroll === true) {
                return;
            }
            $this.didScroll = true;

            if(_.has($this.args, 'autocomplete') && !(_.has($this.args, 'location_point') || _.has($this.args, 'location_shape') || _.has($this.args, 'ids'))) {
                delete $this.args.autocomplete;
            }

            var data = {},
                dataUrl = {},
                autocomplete = $this.args.autocomplete || null;
            for (var key in $this.args) {
                if($this.args.hasOwnProperty(key)) {
                    data[key] = $this.args[key];
                    dataUrl[key] = $this.args[key];
                }
            }

            data.action = 'do_ajax';

            if( type === 'list' && $this.args.page === 1 && eventType !== 'single') {

                if( dataUrl.price && !( dataUrl.price.min || dataUrl.price.max ) ) {
                    delete dataUrl.price;
                }

                if($this.place_error) {
                    dataUrl.place_error = true;
                }

                $this.tags.buildTags(dataUrl);

                if($this.tags.autoComplete.autocompleteSelected) {
                    autocomplete = $this.tags.getAutocompleteData(data);
                }
                if(autocomplete) {
                    if (autocomplete.text) {
                        dataUrl.autocomplete = {
                            text: autocomplete.text
                        };
                    }
                    if (autocomplete.data && autocomplete.data.l_id) {
                        dataUrl.autocomplete = {
                            data: {
                                l_id: autocomplete.data.l_id
                            }
                        };
                    }
                }
                if(! (eventType === 'load' && LpData.defaultLocation) && ! LpData.isLocationPage) {
                    $this.setUrls(dataUrl, eventType);
                }
            }

            data.fn = 'get_objects';
            $.ajax({
                url: LpData.ajaxUrl,
                dataType: 'json',
                method: 'post',
                data: data,
                success: function (data) {
                    if (data.error) {
                        console.log(data.errorMessage);
                    } else if( !_.isEmpty(data.html) ) {
                        if($this.usedFilters.location === true) {
                            setOffmarker(data.offmarket);
                        } else {
                            setOffmarker(0);
                        }
                        $this.args.page++;
                        $this.onPage += data.count;
                        $this.totalObjects = data.total;
                        $this.triggerId = data.triggerID;
                        $this.renderHTML(data.html)
                            .done(function(){
                                if( typeof callback === 'function') {
                                    singleObject.nextLink = (!_.isNull(data.firstObject.slug)) ? LpData.propertyPage + data.firstObject.slug : false;
                                    callback(data);
                                }
                                $this.setContentBox();

                            });
                    }
                },
                error: function (error) {
                    console.error(error);
                },
                complete: function () {
                    loader.hide();

                    var limit = (_.isEmpty($this.args.ids)) ? $this.totalObjects : $this.args.ids.length;

                    if( ( limit > $this.onPage )) {
                        if( window.lpw.Helpers.isElementIntoView($this.lastItem()) ) {
                            $this.didScroll = false;
                            $this.getObjects();
                        }
                    } else {
                        $this.triggerId = 0;
                        $(window).off('scroll.lprop', $this.scrollPage);
                        $(window).off('load.lprop', $this.onLoadCheck);
                        //  $(window).off('resize.lprop', $this.onLoadCheck);
                    }
                    $this.didScroll = false;

                }
            });

        };
        this.scrollPage = function() {

            if ( _.isEmpty($this.lastItem()) || !$this.didScroll && window.lpw.Helpers.isElementIntoView($this.lastItem()) ) {
                // $this.didScroll = true;
                $this.getObjects(null, 'scroll');
            }
        };
        this.onLoadCheck = function(ev) {
            var query = window.lpw.Helpers.getParameterByName('filter'),
                eventtype = ev.type;
            if(eventtype === 'popstate') {
                if(window.location.href.search(LpData.propertyPage) !== -1) {
                    return false;
                }
                if(window.history.state && window.history.state.action && window.history.state.action === "object-close") {
                    return false;
                }
            }

            if(query) {
                try {
                    query = JSON.parse(query);

                    if(!_.isEmpty(query)) {
                        _.forEach(query, function (value, key) {
                            $this.args[key] = value;
                        });
                        $this.args = _.pickBy($this.args);
                        if(query.autocomplete) {
                            $this.args.autocomplete = query.autocomplete;

                            //Set value to autocomplete input if any
                            if(query.autocomplete.text) {
                                $this.autoComplete.jqInput.val(query.autocomplete.text);
                            }

                        }
                        if($this.args.short_rent) {
                            $this.filter.rentLongBtn.removeClass('active');
                            $this.filter.rentShortBtn.addClass('active');
                            $this.args.long_rent = false;
                            $this.args.short_rent = true;
                        } else {
                            $this.filter.rentShortBtn.removeClass('active');
                            $this.filter.rentLongBtn.addClass('active');
                            $this.args.long_rent = true;
                            $this.args.short_rent = false;
                        }
                        resetObjects();
                        $this.getObjects(null, eventtype);
                        $this.filter.setValues(query);

                    }
                } catch(e) {
                    console.log(e);
                }
            } else {
                if(eventtype === 'popstate' && (window.location.href.search(LpData.propertyPage) === -1)) {
                    clearAllFilters();
                }
                // Set filter to long rent
                if($this.args.long_rent) {
                    $this.filter.rentLongBtn.addClass('active');
                    $this.filter.rentShortBtn.removeClass('active');
                    $this.args.short_rent = false;
                }
                resetObjects();
                $this.usedFilters.location = false;
                $this.getObjects(null, eventtype);
            }
        };
        this.setEventListeners = function () {
            // remove window.history.state.action = object-close is set
            $(window).on('load', function() {
                if(window.history.state && window.history.state.action && window.history.state.action === "object-close") {
                    delete window.history.state.action;
                }
               // $this.filterPeriod.val('month').trigger('change');
            });

            //Clear Filters button
            $('body').on('click.lpropr', '.clear-filters-btn', function(ev) {
                ev.preventDefault();
                clearAllFilters();
                $('.no-matches').remove();
                resetObjects();
                $this.getObjects();
            });

            $('.off-market-menu a').on('click.lprop', function(ev) {
                ev.preventDefault();
                if(!$(this).hasClass('half-opaque')) {
                    $('.off-marker-alert').show();
                }
            });

            if( this.onPage < this.totalObjects) {
                $(window).on('scroll.lprop', $this.scrollPage);
                $(window).on('popstate.lprop', $this.onLoadCheck);
                if (_.isEmpty($this.lastItem()) || window.lpw.Helpers.isElementIntoView($this.lastItem())) {
                    if(type !== 'share') {
                        $(window).on('load.lprop', $this.onLoadCheck);
                    }
                    //   $(window).on('resize.lprop', $this.onLoadCheck);
                }
            }
            if( type === 'list' ) {
                this.filter.filterForm.on('submit', function (ev) {
                    ev.preventDefault();
                    var args = $this.filter.getValues();
                    // Change global currency switcher if value was changed in filter menu
                    if(args.price && args.price.currency && args.price.currency !== $this.args.price.currency) {
                        $this.globalCurrencySwitcher.val(args.price.currency).trigger('change');
                        window.lpw.Helpers.createCookie('lpw_currency_id', args.price.currency);
                    }

                    if (!_.isEmpty(args)) {
                        $this.filter.closeFilter();
                        _.forEach(args, function (value, key) {
                            $this.args[key] = value;
                        });
                        $this.args = _.pickBy($this.args);
                        resetObjects();
                        $this.getObjects();
                    }
                });
            }
            this.filter.filterSorting.on('select2:select', function() {
                var val = $(this).val();
                if(val === 'false') {
                    if($this.args.order_by) {
                        delete $this.args.order_by;
                    }
                } else {
                    $this.args.order_by = {
                        order: val
                    };
                }
                resetObjects();
                $this.getObjects();
            });
            this.filter.filterSorting.on('change', function() {
                var val = $(this).val();
                if(val === 'false') {
                    if($this.args.order_by) {
                        delete $this.args.order_by;
                    }
                } else {
                    $this.args.order_by = {
                        order: val
                    };
                }
            });

            //Get objects when global currency is changed, set cookie, and change filter currency
            this.globalCurrencySwitcher.on('select2:select', function(ev) {
                var value = $(this).val();
                window.lpw.Helpers.createCookie('lpw_currency_id', value);
                $this.args.price = $this.args.price || {};
                $this.args.price.currency = value;
                if( type === 'list' ) {
                    $this.filter.filterCurrency.val(value).trigger('change');
                }
                resetObjects();
                $this.getObjects();
            });

            // Rent term selectors action
            $('.btn-term-selector').on('click.lprop', function(ev) {
                ev.preventDefault();
                // Do nothing if clicked on active button
                if( $(this).hasClass('active') ) { return false; }

                //Clear price filter
                if($this.args.price) {
                    if($this.args.price.min) {
                        delete $this.args.price.min;
                        $this.filter.filterInp.price.min.val(undefined);
                    }
                    if($this.args.price.max) {
                        delete $this.args.price.max;
                        $this.filter.filterInp.price.max.val(undefined);
                    }
                    if($this.args.price.period) {
                        delete $this.args.price.period;
                    }
                }

                //Period select Array
                var periodSelect = [];

                $('.btn-term-selector').removeClass('active');
                $(this).addClass('active');

                var term = $(this).data('rent');

                $this.args.long_rent = ( term === 'long' );
                $this.args.short_rent = ( term === 'short' );
                resetObjects();
                $this.totalObjects = ( term === 'long' ) ? parseInt(LpData.totalLongRent) : parseInt(LpData.totalShortRent);
                if(term === 'long') {
                    $this.autoComplete.rentCategory = 'long_rent';
                    $this.filter.rentCategory = 'long_rent';
                    periodSelect.push(LpData.filterPeriod[2]);
                } else {
                    $this.autoComplete.rentCategory = 'short_rent';
                    $this.filter.rentCategory = 'short_rent';
                    periodSelect = LpData.filterPeriod;
                }
                $this.filter.periodFilterInit(periodSelect);
                $this.lpwGoogleMap.getNewGeopoints(term);

                $this.getObjects();
            });

            $(window).on('resize.lprop', this.fixContentBoxPosition.bind(this));
        };
        this.init = function () {
            $this.favorites.init();
            if( type === 'favorites') {
                this.args.ids = $this.favoritesIds;
            }
            $this.filter.init();
            singleObject.init();
            $this.setEventListeners();

        };
    }
    ObjectList.prototype.setUrls = function(data, eventtype) {
        //var url = window.location.origin ? window.location.origin :  (window.location.protocol + '//' + window.location.hostname + window.location.pathname + window.location.port),
        var url = window.location.protocol + '//' + window.location.hostname + window.location.pathname,
            excluded = ['action', 'fn', 'page', 'per_page', 'for_sale', 'for_rent', 'lang', 'place_id', 'place_error'];
        data = _.omit(data, excluded);
        // Unset data.price if no min or max values
        if(data.price && !( data.price.min || data.price.max )) {
            delete data.price;
        }
        // Unset data.similar if no data.location_point.radius
        if(data.similar && ! ( data.location_point && data.location_point.radius) ) {
            delete data.similar;
        }


        if(!_.isEmpty(data)) {
            data = JSON.stringify(data);
            if(eventtype !== 'popstate' && window.lpw.Helpers.isHhistoryApiAvailable()) {
                window.history.pushState(null, null, url + '?filter=' + encodeURIComponent(data));
            }

        } else {
            if(window.lpw.Helpers.isHhistoryApiAvailable()){
	            window.history.replaceState(null, null, url);
            }
        }
    };

    ObjectList.prototype.isFiltersActive = function() {
        if((this.args.price && (this.args.price.min || this.args.price.max)) || this.args.rooms || this.args.area || this.args.property_types || this.args.rooms || this.args.hd_photos || this.args.persons || this.args.long_rent || this.args.short_rent || this.args.child_friendly || this.args.pets_allowed) {
            this.usedFilters.filter = true;
        }
        if(this.args.location_point || this.args.location_shape || this.args.ids) {
            this.usedFilters.location = true;
        }

    };

    ObjectList.prototype.setContentBox = function() {
        var contentBox = $('.seo-block-wrap');
        if(contentBox.length === 0 || this.onPage === 0) {
            return false;
        }
        var cbCloned = contentBox.remove(),
            objectItem = $('.object-item');
        if(this.onPage <= 3) {
            cbCloned.insertAfter(objectItem.last());
        } else {
            var inRow = window.lpw.Helpers.inRow('#object-list', '.object-item');
            if(inRow === 3 ) {
                if(this.onPage >= 6 ) {
                    cbCloned.insertAfter(objectItem.eq(5));
                } else {
                    cbCloned.insertAfter(objectItem.last());
                }
            } else {
                if(this.onPage >= 4 ) {
                    cbCloned.insertAfter(objectItem.eq(3));
                } else {
                    cbCloned.insertAfter(objectItem.last());
                }
            }
        }
    };

    ObjectList.prototype.fixContentBoxPosition = function() {
        var contentBox = $('.seo-block-wrap'),
            inRow = window.lpw.Helpers.inRow('#object-list', '.object-item'),
            objectItem = $('.object-item'),
            cloned;
        if(contentBox.length === 0 || this.onPage <= 3) {
            return false;
        }
        switch(inRow) {
            case 1:
            case 2:
                if(this.onPage > 4) {
                    cloned = contentBox.remove();
                    cloned.insertAfter(objectItem.eq(3));
                }
                break;
            case 3:
                if(this.onPage >= 6) {
                    cloned = contentBox.remove();
                    cloned.insertAfter(objectItem.eq(5));
                }
                break;
        }
    };

    window.lpw = window.lpw || {};
    window.lpw.ObjectList = ObjectList;
})(jQuery);