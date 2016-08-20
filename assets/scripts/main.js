/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {
    // Helper functions
    var Helpers = {
        // Check if element is visible in screen
        isElementIntoView: function(elem) {
            if(_.isEmpty(elem)) {
                return false;
            }
            var $window = $(window);

            var docViewTop = $window.scrollTop();
            var docViewBottom = docViewTop + $window.height(); //document.body.clientHeight;

            var elemTop = elem.offset().top;
            var elemBottom = elemTop + elem.height();

            return (elemBottom <= docViewBottom) && (elemTop >= docViewTop);
        },
        goToLocation: function(location) {
            window.location.href = location;
        },
        isHhistoryApiAvailable: function() {
            return !!(window.history && history.pushState);
        },
        isLocalStorage: function(){
            var test = '_test';
            try {
                localStorage.setItem(test, test);
                localStorage.removeItem(test);
                return true;
            } catch(e) {
                console.log('Local storage is not supported');
                return false;
            }
        },
        preventDefault: function(ev) {
            ev.preventDefault();
        },
        getFavorites: function(category) {
            var name = (category === 'sale') ? 'lpw_web_favorites' : 'lpw_web_rent_favorites';
            if(this.isLocalStorage()) {
                var idx_str = localStorage.getItem(name);
                if (!_.isNull(idx_str)) {
                    var idx_arr = _.uniq(idx_str.split(','));
                    return _.filter(
                        _.map(idx_arr, function (s) {
                            return parseInt(s);
                        }), function (i) {
                            return !isNaN(i);
                        }
                    );
                } else {
                    return [];
                }
            } else {
                return false;
            }
        },
    // Check if parent element has class
        hasParentClass: function( e, classname ) {
            if(e === document) { return false; }
            if( $(e).hasClass( classname ) ) {
                return true;
            }
            return e.parentNode && this.hasParentClass( e.parentNode, classname );
        },
        equalheight: function(container){

            var currentTallest = 0,
                currentRowStart = 0,
                rowDivs = [],
                $el,
                topPosition = 0;
            $(container).each(function() {

                $el = $(this);
                $($el).height('auto');
                topPostion = $el.position().top;

                if (currentRowStart !== topPostion) {
                    for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                        rowDivs[currentDiv].height(currentTallest);
                    }
                    rowDivs.length = 0; // empty the array
                    currentRowStart = topPostion;
                    currentTallest = $el.height();
                    rowDivs.push($el);
                } else {
                    rowDivs.push($el);
                    currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
                }
                for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                    rowDivs[currentDiv].height(currentTallest);
                }
            });
        }
    };
    /* Off canvas Menu */
    var SidebarMenuEffects = {
        container: $( 'html' ),
        toggleBtn: $( '.menu-toggle' ),
        closeBtn: $('.menu-close'),
        resetMenu: function() {
            this.container.removeClass( 'side-menu-open' );
        },
        init: function() {
            var that = this,
                eventtype = 'click';
            function bodyClickFn(evt) {
                if( !Helpers.hasParentClass( evt.target, 'side-menu' ) ) {
                    that.resetMenu();
                    $(document).off( eventtype, bodyClickFn );
                }
            }
            this.closeBtn.on(eventtype, function(ev) {
                ev.preventDefault();
                that.resetMenu();
            });
            this.toggleBtn.on(eventtype, function(ev) {
                ev.stopPropagation();
                ev.preventDefault();
                that.container.addClass('slide-left');
                setTimeout( function() {
                    that.container.addClass( 'side-menu-open' );
                }, 25 );
                $(document).on(eventtype, bodyClickFn);
            });
        }
    };
    /* Filter toggle */
    function FilterMenu(type, catogory) {
        var $this = this,
            container = $('.sp-filters'),
            filterToggleBtn = $('.filter-toggle'),
            filterCloseBtn = $('.filter-close'),
            filterCurrency = $('#price-currency'),
            filterPeriod = $('#price-period'),
            tooltipOpt = {
                template: '<div class="tooltip tooltip-search" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                delay: { "show": 200, "hide": 300 }
            };
        this.filterForm = $('#filter-form');
        this.getValues = function() {
            var values = {
                area: {
                    min: $('#area-min').val(),
                    max: $('#area-max').val()
                },
                hd_photos: $('#quality').is(':checked'),
                price: {
                    currency: $('#price-currency').val(),
                    min: $('#price-min').val(),
                    max: $('#price-max').val()
                }
            };
            values.property_types = $('.property_type:checked').map(function() {
                                        return this.value;
                                    }).get();
            values.rooms = $('.filter-room:checked').map(function() {
                return this.value;
            }).get();
            if( catogory === 'rent' ) {
                values.long_rent = $('#long-term').is(':checked');
                values.short_rent = $('#short-term').is(':checked');
                values.persons = $('#persons-max');
                values.child_friendly = $('#child-friendly').is(':checked');
                values.pets_allowed = $('#pets-allowed').is(':checked');
                values.price.period = $('#price-period').val();
            }
            return values;
        };
        function isFilterActive() {
            return container.hasClass('open');
        }
        this.closeFilter = function() {
            filterToggleBtn.removeClass('active');
            container.removeClass('open');
        };
        this.openFilter = function() {
            filterToggleBtn.addClass('active').tooltip('destroy');
            container.addClass('open');
        };
        function toggleFilter() {
            if(!isFilterActive()) {
                $this.openFilter();
            } else {
                $this.closeFilter();
                filterToggleBtn.tooltip(tooltipOpt);
            }
        }

        this.init = function() {
            if( type === 'list' ) {
                filterCurrency.select2({
                    minimumResultsForSearch: Infinity,
                    containerCssClass: "price-select",
                    dropdownCssClass: "price-select-dropdown"
                });
                filterPeriod.select2({
                    minimumResultsForSearch: Infinity,
                    containerCssClass: "period-select",
                    dropdownCssClass: "period-select-dropdown"
                });
                filterToggleBtn.on('click', function (ev) {
                    ev.preventDefault();
                    toggleFilter();
                });
                filterCloseBtn.on('click', function (ev) {
                    ev.preventDefault();
                    closeFilter();
                });
            }
        };
    }
    /* Floating Bar */
    var FloatingBar = {
        scrollContainer: $(window),
        bar: $('.floating-bar'),
        startPos: $('.site-header').height(),
        toTopLink: $('.to-top-link'),
        searchLink: $('.search-link'),
        scrollTo: function(el, delay, offset) {
            if( el !== 0 ) {
                el = el.offset().top - offset;
            }
            $('html, body').stop().animate({
                scrollTop: el
            }, delay);
        },
        toggleBar: function() {
            if(this.scrollContainer.scrollTop() > this.startPos) {
                this.bar.addClass('bar-visible');
            } else {
                this.bar.removeClass('bar-visible');
            }
        },
        init: function() {
            var that = this;
            this.scrollContainer.on('scroll', function() {
                that.toggleBar();
            });
            this.toTopLink.on('click', function(ev) {
                ev.preventDefault();
                that.scrollTo(0, 1200);
            });
            this.searchLink.on('click', function(ev) {
                ev.preventDefault();
                FilterMenu.openFilter();
                that.scrollTo(0, 1000, 200);
            });
        }
    };

    /*Object Hsare Bar*/

    function ObjectShareBar() {
        var shareLinks = $('.favorites-sharing a'),
            shEmail = $('.obj-share-email'),
            shFb = $('.obj-share-fb'),
            shTw = $('.obj-share-tw'),
            shLn = $('.obj-share-ln'),
            shGp = $('.obj-share-gplus'),
            shInput = $('.fav-link-input'),
            baseUrl = LpData.homeUrl + 'sharer/?ids=';
        function setValues(url) {
            var urlEnc = encodeURIComponent(url);
            shInput = $('.fav-link-input').val(url);
            shEmail.attr('href','mailto:?Subject=' + LpData.siteTitle + '&body=' + encodeURIComponent(url + "\n\n" + 'Property selection powered by The Leading Properties of the World. Best hand-picked properties with beautiful photos. Want to see properties selected just for you? Visit our website and get your personal recommendation today.'));
            shFb.attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + urlEnc);
            shTw.attr('href', 'https://twitter.com/intent/tweet?url=' + urlEnc);
            shLn.attr('href', 'https://www.linkedin.com/shareArticle?mini=true&url=' + urlEnc);
            shGp.attr('href', 'https://plus.google.com/share?url=' + urlEnc);
        }
        this.setUrls = function(ids) {
            if( ids.length === 0 ) {
                shareLinks.addClass('half-opaque').removeAttr('href');
                shInput.val('');
                return false;
            }
            if( shareLinks.hasClass('half-opaque') ) {
                shareLinks.removeClass('half-opaque');
            }
            var idsString = ids.join('.');
            var url = baseUrl + idsString;
            if(LpData.useShortener) {
                $.ajax({
                    url: LpData.ajaxUrl,
                    dataType: 'json',
                    method: 'post',
                    data: {
                        url: url,
                        action: 'do_ajax',
                        fn: 'get_shorten_url'
                    },
                    success: function (data) {
                        if( !_.isEmpty(data.id) ) {
                            url = data.id;
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    },
                    complete: function () {
                        setValues(url);
                    }
                });
            } else {
                setValues(url);
            }

        };
    }

    /* Favorites */
    function Favorites(type, category) {
        var $this = this,
            favRemoveAll = ( type === 'favorites' ) ? $('.fav-remove') : false,
            storageName = (category === 'sale') ? 'lpw_web_favorites' : 'lpw_web_rent_favorites';

        this.type = type;
        this.mMenulink = $('.menu-favorites');
        this.dtlink = $('.favorites-menu a');
        this.fbLink = $('.favorites-link');
        this.fpCounter = $('.favorite_objects_count');
        this.favoritesIds = Helpers.getFavorites(category);
        this.sharing =  ( type === 'favorites' ) ? new ObjectShareBar() : false;
        this.setCounters = function(favorites) {
            if($this.type === 'favorites') {
                $this.fpCounter.text(favorites.length);
            } else {
                if (favorites.length > 0) {

                    $this.mMenulink
                        .off('click.lprop', Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                    $this.dtlink
                        .off('click.lprop', Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                    $this.fbLink
                        .off('click.lprop', Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                } else {
                    $this.mMenulink
                        .on('click.lprop', Helpers.preventDefault)
                        .addClass('half-opaque')
                        .find('sup')
                        .text('');
                    $this.dtlink
                        .on('click.lprop', Helpers.preventDefault)
                        .addClass('half-opaque')
                        .find('sup')
                        .text('');
                    $this.fbLink
                        .on('click.lprop', Helpers.preventDefault)
                        .addClass('half-opaque')
                        .find('sup')
                        .text('');
                }
            }

        };
        this.favObjectToogle = function(id, action) {
            var favorites = $this.favoritesIds;
            if( favorites === false ) { return false;  }
            if(  action === 'add' ) {
                favorites.push(id);
            } else {
                _.pull(favorites, id);
            }
            localStorage.setItem(storageName, favorites.join());

            $this.setCounters(favorites);

            if($this.sharing) {
                $this.sharing.setUrls(favorites);
            }

            if( favorites.length === 0 ) {
                localStorage.removeItem(storageName);
                if($this.type === 'favorites') {
                    window.location.href = 'http://wp-lp.loc/rent/';
                    if( category === 'sale' ) {
                        Helpers.goToLocation(LpData.salePage);
                    } else {
                        Helpers.goToLocation(LpData.rentPage);
                    }
                }
            }
        };
        this.favObjectToogleEvent = function(e) {
            e.preventDefault();
            var target = $(e.currentTarget),
                action = target.data('action'),
                id = target.data('id');
                if(!_.isNull(id)) {
                    $this.favObjectToogle(id, action);
                    if (action === 'add') {
                        if(Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
                            $('#object-' + id).find('.add-favorite-button')
                                .data('action', 'remove')
                                .addClass('in-favorites');
                        }
                        target.data('action', 'remove')
                            .addClass('in-favorites');
                    } else if (action === 'remove') {
                        if(Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
                            $('#object-' + id).find('.add-favorite-button')
                                .data('action', 'add')
                                .removeClass('in-favorites');
                        }
                        if($this.type === 'favorites') {
                            if(Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
                                target.data('action', 'add')
                                    .removeClass('in-favorites');
                                $('#object-' + id)
                                    .remove();
                            } else {
                                target.closest('.object-item')
                                    .remove();
                            }
                        } else {
                            target.data('action', 'add')
                                .removeClass('in-favorites');
                        }

                    }
                }
        };
        this.init = function() {
            $(window).on('load.lprop', function() {
                var favorites = Helpers.getFavorites(category);
                $this.setCounters(favorites);

                if( $this.type === 'favorites' ) {
                    if(favorites.length === 0) {
                        if( category === 'sale' ) {
                            Helpers.goToLocation(LpData.salePage);
                        } else {
                            Helpers.goToLocation(LpData.rentPage);
                        }
                    } else if( $this.sharing ) {
                        $this.sharing.setUrls(favorites);
                    }
                }
            });
            $('body').on('click.lprop', '.add-favorite-button', $this.favObjectToogleEvent);
            if(favRemoveAll) {
                var msg = $('.favorites-confirmation-message ');
                favRemoveAll.on('click.lprop', function (ev) {
                    ev.preventDefault();
                    if (!msg.hasClass('open')) {
                        msg.addClass('open');
                    }
                });
                $('.btn-fav-confirm').on('click.lprop', function(ev) {
                    if($(this).data('action') === 'close') {
                        msg.removeClass('open');
                    } else {
                        localStorage.removeItem(storageName);
                        $('#object-list').html('');
                        $this.setCounters([]);
                        if( category === 'sale' ) {
                            Helpers.goToLocation(LpData.salePage);
                        } else {
                            Helpers.goToLocation(LpData.rentPage);
                        }
                    }

                });
            }
        };
    }

    /* Single Object */

    function SingleObject(objectlist) {

        var $this = this;

        function isModalExists() {
            return $('.single-object-modal').length > 0;
        }

        function closeModal(ev) {
            ev.preventDefault();
            $('.single-object-modal').remove();
            $('html').removeClass('overflow-height');
            if( Helpers.isHhistoryApiAvailable()) {
                window.history.pushState(null, null, $this.location);
            }
        }

        function prevObjectLink(id) {
            var prevObject = $('#object-' + id).prev();

            if(prevObject.length > 0) {
                return prevObject.find('.object-link').attr('href');
            } else {
                return false;
            }
        }
        function nextObjectLink(id) {
            var nextObject = $('#object-' + id).next();

            if(nextObject.length > 0) {
                return nextObject.find('.object-link').attr('href');
            } else {
                return false;
            }
        }



        this.renderSingleHtml = function(data)  {
            $('html').addClass('overflow-height');

            var type = (data.slug_type === 'PropertyObject') ? 'sale' : 'rent',
                title = (data.slug_type === 'PropertyObject') ? data.description.title : data.description.rent_title,
                favs = objectlist.favoritesIds,
                a2f_class,
                a2f_action,
                prevBtn = ($this.prevLink) ? '<a class="icon open-object-modal" href="' + $this.prevLink + '"><span class="direction-text">Previous property</span></a>' : '<a class="icon disabled"><span class="direction-text">Previous property</span></a>',
                nextBtn = ($this.nextLink) ? '<a class="icon open-object-modal" href="' + $this.nextLink + '"><span class="direction-text">Next property</span></a>' : '<a class="icon disabled"><span class="direction-text">Next property</span></a>';

            if( objectlist.type === 'favorites' || _.includes(favs, data.id) ) {
                a2f_class = ' in-favorites';
                a2f_action = 'remove';
            } else {
                a2f_class = '';
                a2f_action = 'add';
            }

            if( isModalExists() ) {
                $('.single-object-modal').remove();
            }
            try {
                var objectHtml = '<div class="single-object-container single-object-modal">' +
                    '<header class="single-object-header">' +
                    '<div class="single-object-wrap">' +
                    '<div class="detailed-link-wrap">' +
                    '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request"><span>Get detailed information</span></a>' +
                    '</div>' +
                    '<button type="button" class="btn btn-single-close">Close</button>' +
                    '<ul class="single-object-menu">' +
                    '<li><a href="#" class="pdf-link"><sup class="text-red">PDF</sup></a></li>' +
                    '<li><a href="#" class="add-favorite-button' + a2f_class + '"  data-action="' + a2f_action + '"  data-id="' + data.id + '"></a></li>' +
                    '</ul>' +
                    '</div>' +
                    '</header><!-- /.single-object-header -->' +
                    '<div class="single-object-content">' +
                    '<div class="single-object-content-inner">' +
                    '<div id="gallery-' + data.id + '" class="single-slider carousel slide" data-ride="carousel">';
                if (_.isArray(data.parameters.images)) {
                    objectHtml += '<ol class="carousel-indicators">';
                    var indClass = '';
                    for (var i = 0; i < data.parameters.images.length; i++) {
                        indClass = ( i === 0 ) ? 'active' : '';
                        objectHtml += '<li data-target="#gallery-' + data.id + '" data-slide-to="' + i + '" class="' + indClass + '"></li>';
                    }
                    objectHtml += '</ol>';
                    objectHtml += '<div class="carousel-inner" role="listbox">';
                    for (i = 0; i < data.parameters.images.length; i++) {
                        indClass = ( i === 0 ) ? 'active' : '';
                        objectHtml += '<div class="item ' + indClass + '">' +
                            '<img src="' + data.parameters.images[i] + '" alt="...">' +
                            '</div>';
                    }
                    objectHtml += '</div>';
                    objectHtml += '<a class="left carousel-control" href="#gallery-' + data.id + '" role="button" data-slide="prev">' +
                        '<span class="sr-only">Previous</span>' +
                        '</a>' +
                        '<a class="right carousel-control" href="#gallery-' + data.id + '" role="button" data-slide="next">' +
                        '<span class="sr-only">Next</span>' +
                        '</a>';

                }
                objectHtml += '</div>' +
                    '<div class="single-object-details">' +
                    '<ul class="single-object-locations">' +
                    '<li><a class="icon cursor-default"><span>';
                if ('country' in data && !_.isNull(data.country.title)) {
                    objectHtml += data.country.title;
                }
                if ('region' in data && !_.isNull(data.region.title)) {
                    objectHtml += ', ' + data.region.title;
                }
                if ('city' in data && !_.isNull(data.city.title)) {
                    objectHtml += ', ' + data.city.title;
                }
                if ('district' in data && !_.isNull(data.district.title)) {
                    objectHtml += ', ' + data.district.title;
                }
                objectHtml += '</span></a></li></ul>' +
                    '<h1 class="single-object-title">';
                if(data.sold_state === true) {
                    objectHtml += '<span class="object-sold">sold</span>';
                }
                objectHtml +=  title + '</h1>';

                if( type === 'rent') {
                    if ( (!_.isNull(data.features.bedrooms.min) || !_.isNull(data.features.bedrooms.max)) || (!_.isNull(data.features.bathrooms.min) || !_.isNull(data.features.bathrooms.max)) || !_.isNull(data.property_rent.persons_max) || !_.isNull(data.property_rent.child_friendly) || !_.isNull(data.property_rent.pets_allowed) ) {
                        objectHtml += '<ul class="object-short-info">';
                        if(!_.isNull(data.features.bedrooms.min) || !_.isNull(data.features.bedrooms.max)) {
                            objectHtml += '<li class="icon icon-bedroom">';
                            if(!_.isNull(data.features.bedrooms.min)) {
                                objectHtml += data.features.bedrooms.min;
                            }
                            if(!_.isNull(data.features.bedrooms.max)) {
                                objectHtml += ' - ' + data.features.bedrooms.max;
                            }
                            objectHtml += '</li>';
                        }
                        if(!_.isNull(data.features.bathrooms.min) || !_.isNull(data.features.bathrooms.max)) {
                            objectHtml += '<li class="icon icon-bathroom">';
                            if(!_.isNull(data.features.bathrooms.min)) {
                                objectHtml += data.features.bathrooms.min;
                            }
                            if(!_.isNull(data.features.bathrooms.max)) {
                                objectHtml += ' - ' + data.features.bathrooms.max;
                            }
                            objectHtml += '</li>';
                        }
                        if(!_.isNull(data.property_rent.persons_max)) {
                            objectHtml += '<li class="icon icon-person">' + data.property_rent.persons_max + '</li>';
                        }
                        if(data.property_rent.child_friendly === true) {
                            objectHtml += '<li class="icon icon-child"></li>';
                        }
                        if(data.property_rent.pets_allowed === true) {
                            objectHtml += '<li class="icon icon-pet"></li>';
                        }
                        objectHtml += '</ul>';
                    }
                }

                objectHtml += '<div class="single-object-description">';

                objectHtml += '<p>' + data.description.main_text + '</p>' +
                    '</div><!-- /.single-object-description -->';

                if( type === 'sale') {
                    objectHtml += '<div class="single-object-info">' +
                        '<p class="object-price icon">';
                    if (data.parameters.price.on_demand === true) {
                        objectHtml += 'Price on demand';
                    } else {
                        if (!_.isNull(data.parameters.price.min)) {
                            objectHtml += data.parameters.price.min;
                        }
                        if (!_.isNull(data.parameters.price.max)) {
                            objectHtml += '&nbsp;-&nbsp;' + data.parameters.price.max;
                        }
                    }
                    objectHtml += '&nbsp;' + data.parameters.price.currency;
                    objectHtml += '<p class="object-code icon">' + data.code + '</p>' +
                        '</div>';
                } else if( type === 'rent' ) {
                    objectHtml += '<div class="rent-rate">';

                    if( data.property_rent.long_rent === true ) {
                        objectHtml += '<p class="icon icon-month">Long term rental, monthly rate:</p>';

                    }

                    objectHtml += '</div><!-- /.rent-rate -->>';
                }
                objectHtml += '<ul class="object-properties">' +
                    '<li>property type</li>' +
                    '<li>' + data.parameters.property_object_type + '</li>';
                if (!_.isNull(data.parameters.rooms.min) || !_.isNull(data.parameters.rooms.max)) {
                    objectHtml += '<li>number of rooms</li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.parameters.rooms.min)) {
                        objectHtml += data.parameters.rooms.min;
                    }
                    if (!_.isNull(data.parameters.rooms.max)) {
                        objectHtml += ' - ' + data.parameters.rooms.max;
                    }
                    objectHtml += '</li>';
                }
                if (!_.isNull(data.parameters.area.min) || !_.isNull(data.parameters.area.max)) {
                    objectHtml += '<li>area, m<sup>2</sup></li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.parameters.area.min)) {
                        objectHtml += data.parameters.area.min;
                    }
                    if (!_.isNull(data.parameters.area.max)) {
                        objectHtml += ' - ' + data.parameters.area.max;
                    }
                    objectHtml += '</li>';
                }
                if (!_.isNull(data.features.bedrooms.min) || !_.isNull(data.features.bedrooms.max)) {
                    objectHtml += '<li>bedrooms</li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.features.bedrooms.min)) {
                        objectHtml += data.features.bedrooms.min;
                    }
                    if (!_.isNull(data.features.bedrooms.max)) {
                        objectHtml += ' - ' + data.features.bedrooms.max;
                    }
                    objectHtml += '</li>';
                }
                if (!_.isNull(data.features.bathrooms.min) || !_.isNull(data.features.bathrooms.max)) {
                    objectHtml += '<li>bathrooms</li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.features.bathrooms.min)) {
                        objectHtml += data.features.bathrooms.min;
                    }
                    if (!_.isNull(data.features.bathrooms.max)) {
                        objectHtml += ' - ' + data.features.bathrooms.max;
                    }
                    objectHtml += '</li>';
                }
                if (!_.isNull(data.features.land_area.min) || !_.isNull(data.features.land_area.max)) {
                    objectHtml += '<li>land area</li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.features.land_area.min)) {
                        objectHtml += data.features.land_area.min;
                    }
                    if (!_.isNull(data.features.land_area.max)) {
                        objectHtml += ' - ' + data.features.land_area.max;
                    }
                    objectHtml += '</li>';
                }
                if (!_.isNull(data.features.building_storeys) && (!_.isNull(data.features.building_storeys.min) || !_.isNull(data.features.building_storeys.max))) {
                    objectHtml += '<li>building storeys</li>';
                    objectHtml += '<li>';
                    if (!_.isNull(data.features.building_storeys.min)) {
                        objectHtml += data.features.building_storeys.min;
                    }
                    if (!_.isNull(data.features.building_storeys.max)) {
                        objectHtml += ' - ' + data.features.building_storeys.max;
                    }
                    objectHtml += '</li>';
                }
                if ('present' in data.features.terrace_balcony && data.features.terrace_balcony.present === true) {
                    objectHtml += '<li>terrace/balcony</li><li><i class="icon icon-checkmark-circle"></i></li>';
                }
                if ('present' in data.features.pool && data.features.pool.present === true) {
                    objectHtml += '<li>pool</li><li><i class="icon icon-checkmark-circle"></i></li>';
                }
                if ('present' in data.features.garage_parking && data.features.garage_parking.present === true) {
                    objectHtml += '<li>parking/garage</li><li><i class="icon icon-checkmark-circle"></i></li>';
                }
                if ('present' in data.features.utility_rooms && data.features.utility_rooms.present === true) {
                    objectHtml += '<li>cellars</li><li><i class="icon icon-checkmark-circle"></i></li>';
                }
                objectHtml += '</ul><!-- /.object-properties -->' +
                    '</div><!-- /.single-object-details -->' +
                    '</div><!-- /.single-object-content-inner -->' +
                    '<div class="object-navigation">' +
                    '<div class="object-prev">' +
                     prevBtn +
                    '</div>' +
                    '<div class="object-next">' +
                     nextBtn +
                    '</div>' +
                    '</div><!-- /.object-navigation -->' +
                    '<div class="similar-object-search">' +
                    '<div class="similar-search-container">' +
                    '<i class="icon icon-radar"></i>' +
                    '<p>Find similar properties in the same area</p>' +
                    '<ul class="similar-locations">' +
                    '<li><a href="#">1 km</a></li>' +
                    '<li><a href="#">5 km</a></li>' +
                    '<li><a href="#">10 km</a></li>' +
                    '</ul>' +
                    '</div>' +
                    '</div><!-- /.similar-object-search -->' +
                    '<footer class="single-object-footer">' +
                    '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request"><span>Get detailed information</span></a>' +
                    '</footer>' +
                     '<div class="single-object-backdrop"></div>' +
                    '</div><!-- /.single-object-container -->';
                objectlist.objectContainer.append(objectHtml);
            } catch(e) {
                console.log(e);
            }

        };
        this.getSingleObject = function(ev) {
            ev.preventDefault();
            var url;
            if( 'click' === ev.type ) {
                url = $(this).attr('href');
            } else {
                url = window.location.href;
            }

            var data = {
                'action' : 'do_ajax',
                'fn' : 'get_objects',
                'lang': objectlist.args.lang,
                'slug': _.replace(url, LpData.propertyPage, '')
            };
            $this.showLoader(true);

            $.ajax({
                url: LpData.ajaxUrl,
                dataType : 'json',
                method: 'post',
                data : data,
                success : function(data){
                    if( typeof data === 'object' ) {
                        $this.prevLink = prevObjectLink(data.id);
                        $this.nextLink = nextObjectLink(data.id);
                        if(data.id === objectlist.triggerId ) {
                            objectlist.getObjects(function() {
                                $this.renderSingleHtml(data);
                                if (url !== $this.location) {
                                    if (Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                        window.history.pushState(null, null, url);
                                    }
                                }
                                $this.showLoader(false);
                            });
                        } else {
                            $this.renderSingleHtml(data);
                            if (url !== $this.location) {
                                if (Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                    window.history.pushState(null, null, url);
                                }
                            }
                            $this.showLoader(false);

                        }

                    }
                },
                error : function (error){
                    console.error(error);
                },
                complete: function() {
                }
            });

        };
        this.testPopstste = function(ev) {
            if( isModalExists() && window.location.pathname === $this.location) {
                $('.btn-single-close').trigger('click.lprop');
            } else {
                $this.getSingleObject(ev);
            }
        };
        this.showLoader = function(state) {
            if(state) {
                $('<div class="post-overlay loader"><span class="spin"></span></div>').appendTo(objectlist.objectContainer);
            } else {
                $('.post-overlay').remove();
            }
        };
        this.eventListeners = function() {
            objectlist.objectContainer.on('click.lprop', '.open-object-modal', $this.getSingleObject);
            $(window).on('popstate', $this.testPopstste);
            objectlist.objectContainer.on('click.lprop', '.btn-single-close', closeModal);
            objectlist.objectContainer.on('click.lprop', '.single-object-backdrop', closeModal);
        };

        this.init = function() {
            this.location = window.location.pathname;
            this.eventListeners();
        };
    }

    /* Object List */
    function ObjectList(type, category) {
        var $this = this,
            loader = $('.loader'),
            filter = new FilterMenu(type, category),
            singleObject = new SingleObject(this);

        this.lastItem = function() {
            return $('.object-item').last();
        };
        this.type = type;
        this.favorites = new Favorites(type, category);
        this.favoritesIds = $this.favorites.favoritesIds;
        this.objectContainer = $('#object-list');
        this.onPage = 0;
        this.totalObjects = 99999; /*todo get total objects */
        this.didScroll = false;
        this.triggerId = 0;
        this.args = {
            lang: 'en',
            page: 1,
            per_page: 9,
            for_sale: ( category === 'sale' ),
            for_rent: ( category === 'rent' )
        };
        function resetObjects() {
            $this.objectContainer.html('');
            $this.args.page = 1;
        }
        this.renderHTML = function(objects) {
            var objectHtml = '',
                title,
                slug,
                url,
                minStay,
                rentPrice,
                favs = $this.favoritesIds,
                a2f_class,
                a2f_action,
                objectClass = ( category === 'rent' ) ? 'object-item rent-item' : 'object-item',
                r = $.Deferred();

            _.forEach(objects, function(object, key) {


                if( category === 'sale' ) {
                    title = object.description.title;
                    slug = object.slug;
                } else {
                    title = ( !_.isNull(object.description.rent_title) ) ? object.description.rent_title : object.description.title;
                    slug = ( !_.isNull(object.rent_slug) ) ? object.rent_slug : object.slug;
                    if(object.property_rent.long_rent === true && object.property_rent.short_rent === false) {
                        minStay = 'on request';
                    } else if( object.property_rent.short_rent === true ) {
                        if ( object.property_rent.rent_short.min_day === true ) {
                            minStay = '1 day';
                        } else if ( object.property_rent.rent_short.min_week === true ) {
                            minStay = '1 week';
                        } else {
                            minStay = '1 month';
                        }
                    }
                    if( object.property_rent.short_rent === true ) {
                        if(!_.isNull(object.property_rent.rent_short.sort_price.day)) {
                            rentPrice = '<span class="price-num">' + parseInt(object.property_rent.rent_short.sort_price.day) + ' </span>' + object.property_rent.rent_short.sort_price.currency_code + '&nbsp;/&nbsp;day';
                        } else if(!_.isNull(object.property_rent.rent_short.sort_price.month)) {
                            rentPrice = '<span class="price-num">' + parseInt(object.property_rent.rent_short.sort_price.month) + ' </span>' + object.property_rent.rent_short.sort_price.currency_code + '&nbsp;/&nbsp;month';
                        }
                    } else if( object.property_rent.long_rent === true ) {
                        if(!_.isNull(object.property_rent.rent_long.sort_price.month)) {
                            rentPrice = '<span class="price-num">' + parseInt(object.property_rent.rent_long.sort_price.month) + ' </span>' + object.property_rent.rent_long.sort_price.currency_code + '&nbsp;/&nbsp;month';
                        } else if(!_.isNull(object.property_rent.rent_long.sort_price.day)) {
                            rentPrice = '<span class="price-num">' + parseInt(object.property_rent.rent_long.sort_price.day) + ' </span>' + object.property_rent.rent_long.sort_price.currency_code + '&nbsp;/&nbsp;day';
                        }
                    }
                }
                url = LpData.propertyPage + slug;
                try {
                    if( type === 'favorites' || _.includes(favs, object.id) ) {
                        a2f_class = ' in-favorites';
                        a2f_action = 'remove';
                    } else {
                        a2f_class = '';
                        a2f_action = 'add';
                    }
                    objectHtml += '<article class="' + objectClass + '" id="object-' + object.id + '" data-object-id="' + object.id + '">' +
                                  '<div class="object-inner-wrapper">' +
                                  '<div class="object-thumbnail">' +
                                  '<a href="' + url + '" class="open-object-modal object-thumbnail-holder" title="' + title + '">' +
                                  '<img class="img-responsive" src="' + object.image + '"  alt="' + title + '">' +
                                  '</a>' +
                                  '<span class="add-favorite-button' + a2f_class + '" data-action="' + a2f_action + '" data-id="' + object.id + '"></span>';
                    if( category === 'rent' && 'property_rent') {
                        objectHtml += '<div class="rent-price"><span>' + rentPrice + '</span></div>';
                    }
                    objectHtml += '</div>' +
                                  '<div class="object-info-holder">' +
                                  '<div class="info-address-holder">' +
                                  '<div class="info-address">';
                    if (('country' in object) && ('title' in object.country) && !_.isNull(object.country.title)) {
                        objectHtml += '<a>' + object.country.title + '</a>';
                    }
                    if (('region' in object) && ('title' in object.region) && !_.isNull(object.region.title)) {
                        objectHtml += '<a>' + object.region.title + '</a>';
                    }
                    if (('city' in object) && ('title' in object.city) && !_.isNull(object.city.title)) {
                        objectHtml += '<a>' + object.city.title + '</a>';
                    }
                    objectHtml += '</div>' +
                                  '</div>' +
                                  '<h2 class="info-title">' +
                                  '<a class="open-object-modal object-link" href="' + url + '">' + title + '</a>' +
                                  '</h2>';
                    if( category === 'sale') {
                        objectHtml +=  '<p class="info-details"><span>';
                        if (object.parameters.price.on_demand) {
                            objectHtml += '<span>Price on demand</span>&nbsp;';
                        } else {
                            if (!_.isNull(object.parameters.price.min)) {
                                objectHtml += '<span>' + object.parameters.price.min + '</span>';
                            }
                            if (!_.isNull(object.parameters.price.max)) {
                                objectHtml += '&nbsp;&mdash;&nbsp;<span>' + object.parameters.price.max + '</span>';
                            }
                            objectHtml += '&nbsp;<span>' + object.parameters.price.currency + '</span></span>';
                        }
                        if (!_.isNull(object.parameters.rooms.min) || !_.isNull(object.parameters.rooms.max)) {
                            objectHtml += ',&nbsp;';
                        }
                        if (!_.isNull(object.parameters.rooms.min)) {
                            objectHtml += '<span>' + object.parameters.rooms.min + '</span>';
                        }
                        if (!_.isNull(object.parameters.rooms.max)) {
                            objectHtml += '&nbsp;&mdash;&nbsp;<span>' + object.parameters.rooms.max + '</span>';
                        }
                        if (!_.isNull(object.parameters.rooms.min) || !_.isNull(object.parameters.rooms.max)) {
                            objectHtml += '&nbsp;rooms';
                        }
                        if (!_.isNull(object.parameters.area.min) || !_.isNull(object.parameters.area.max)) {
                            objectHtml += ',&nbsp;';
                        }
                        if (!_.isNull(object.parameters.area.min)) {
                            objectHtml += '<span>' + object.parameters.area.min + '</span>';
                        }
                        if (!_.isNull(object.parameters.area.max)) {
                            objectHtml += '&nbsp;&mdash;&nbsp;<span>' + object.parameters.area.max + '</span>';
                        }
                        if (!_.isNull(object.parameters.area.min) || !_.isNull(object.parameters.area.max)) {
                            objectHtml += '&nbsp;m<sup>2</sup>';
                        }
                        objectHtml += '</p>';
                    } else {
                        objectHtml += '<p class="min-days">Minimum stay: ' + minStay + '</p>';
                        objectHtml +=  '<ul class="rent-details">';
                        if (!_.isNull(object.parameters.area.min)) {
                            objectHtml += '<li class="area">' + object.parameters.area.min + ' m<sup>2</sup></li>';
                        }
                        if (!_.isNull(object.parameters.bedrooms.min)) {
                            objectHtml += '<li class="icon icon-bedroom">' + object.parameters.bedrooms.min + '</li>';
                        }
                        if (!_.isNull(object.parameters.bathrooms.min)) {
                            objectHtml += '<li class="icon icon-bathroom">' + object.parameters.bedrooms.min + '</li>';
                        }
                        if (!_.isNull(object.property_rent.persons_max)) {
                            objectHtml += '<li class="icon icon-person">' + object.property_rent.persons_max + '</li>';
                        }
                        objectHtml += '</ul>';
                    }
                    objectHtml += '</div></div></article>';
                } catch(e) {
                    console.log(e);
                }
            });
           $this.objectContainer.append(objectHtml);
            r.resolve();
            return r;

        };
        this.getObjects = function (callback) {

            loader.show();
            data = $this.args;

            data.action = 'do_ajax';
            data.fn = 'get_objects';
                $.ajax({
                    url: LpData.ajaxUrl,
                    dataType: 'json',
                    method: 'post',
                    data: data,
                    success: function (data) {
                        if( !_.isEmpty(data) ) {
                            if (_.isArray(data.property_objects)) {
                                $this.args.page++;
                                $this.onPage += data.property_objects.length;
                                $this.totalObjects = data.total;
                                $this.renderHTML(data.property_objects)
                                    .done(function(){
                                        if( typeof callback === 'function') {
                                            if( category === 'sale' ) {
                                                singleObject.nextLink = (!_.isNull(data.property_objects[0].slug)) ? LpData.propertyPage + data.property_objects[0].slug : false;
                                            } else if( category === 'rent' ) {
                                                singleObject.nextLink = (!_.isNull(data.property_objects[0].rent_slug)) ? LpData.propertyPage + data.property_objects[0].rent_slug : false;
                                            }

                                            callback(data);
                                        }
                                    });

                            }
                            else if (data.error) {
                                console.log(data.errorMessage);
                            }
                        }
                    },
                    error: function (error) {
                        console.error(error);
                    },
                    complete: function () {
                        loader.hide();

                        var limit = (_.isEmpty($this.args.ids)) ? $this.totalObjects : $this.args.ids.length;

                        if( ( limit > $this.onPage )) {
                            $this.triggerId = $this.lastItem().data('object-id');
                            if( Helpers.isElementIntoView($this.lastItem()) ) {
                                $this.getObjects();
                            }
                            $this.didScroll = false;
                        } else {
                            $this.triggerId = 0;
                            $(window).off('scroll.lprop', $this.getObjects);
                            $(window).off('load.lprop', $this.getObjects);
                            $(window).off('resize.lprop', $this.getObjects);
                        }
                    }
                });

        };
        this.scrollPage = function() {
            if ( _.isEmpty($this.lastItem()) || !$this.didScroll && Helpers.isElementIntoView($this.lastItem()) ) {
                $this.didScroll = true;
                $this.getObjects();
            }
        };
        this.setEventListeners = function () {
            if( this.onPage < this.totalObjects) {
                $(window).on('scroll.lprop', $this.scrollPage);

                if (_.isEmpty($this.lastItem()) || Helpers.isElementIntoView($this.lastItem())) {
                    $(window).on('load.lprop', $this.getObjects);
                    $(window).on('resize.lprop', $this.getObjects);
                }
            }

            filter.filterForm.on('submit', function(ev) {
                ev.preventDefault();
                filter.closeFilter();
                _.forEach(filter.getValues(), function(value, key) {
                    $this.args[key] = value;
                });
                resetObjects();
                $this.getObjects();
            });
        };
        this.init = function () {
            $this.favorites.init();
            if( type === 'favorites') {
                this.args.ids = $this.favoritesIds;
            }
            filter.init();
            singleObject.init();
            $this.setEventListeners();
        };
    }


  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Lprops = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
          SidebarMenuEffects.init();

          $('#sorting').select2({
              minimumResultsForSearch: Infinity,
              containerCssClass : "sorting-select",
              dropdownCssClass: "sorting-select-dropdown",
              width: "100%"
          });
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page



      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    'page_sales': {
        init: function() {
            FloatingBar.init();
            var objects = new ObjectList('list', 'sale');
            objects.init();
        }
    },
    'page_rent': {
        init: function() {
            FloatingBar.init();
            var objects = new ObjectList('list', 'rent');
            objects.init();
        }
    },
    'page_favorites': {
      init: function() {
          var objects = new ObjectList('favorites', 'sale');
          objects.init();
          FloatingBar.init();
      }
    },
    'page_favorites_rent': {
      init: function() {
          var objects = new ObjectList('favorites', 'rent');
          objects.init();
          FloatingBar.init();
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Lprops;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
