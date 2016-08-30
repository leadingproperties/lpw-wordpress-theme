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

Number.prototype.formatMoney = function(c, d, t){
    var n = this,
        c = (isNaN(c = Math.abs(c))) ? 2 : c,
        d = d === undefined ? " " : d,
        t = t === undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};


(function($) {

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
            if(container) {
                container = '.eqh';
            }

            var currentTallest = 0,
                currentRowStart = 0,
                rowDivs = [],
                $el,
                topPosition = 0;
            $(container).each(function() {

                $el = $(this);
                $($el).height('auto');
                var topPostion = $el.position().top,
                    currentDiv;

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
        },
        showOffmarketModal: function(ev) {
            ev.preventDefault();

            $('.offmarket-request').modal('show');
        },
        getParameterByName: function(name, url) {
            if (!url) { url = window.location.href; }
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) { return null ; }
            if (!results[2]) { return ''; }
            return decodeURIComponent(results[2].replace(/\+/g, " "));
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
            },
            filterInp = {
                price: {
                    min: $('#price-min'),
                    max: $('#price-max'),
                    currency: filterCurrency,
                    period: filterPeriod
                },
                area:  {
                    min: $('#area-min'),
                    max: $('#area-max')
                },
                property_types: $('.property_type'),
                rooms: $('.filter-room'),
                hd_photos: $('#quality'),
                persons: $('#persons-max'),
                short_rent: $('#short-term'),
                long_rent: $('#long-term'),
                child_friendly: $('#child-friendly'),
                pets_allowed: $('#pets-allowed')

            };
        this.setValues = function(values) {
            if(values.price) {
                if(values.price.min) {
                    filterInp.price.min.val(values.price.min);
                } else {
                    filterInp.price.min.val(undefined);
                }
                if(values.price.max) {
                    filterInp.price.max.val(values.price.max);
                } else {
                    filterInp.price.min.val(undefined);
                }
                if(values.price.currency) {
                    filterInp.price.currency.val(values.price.currency).trigger("change");
                } else {
                    filterInp.price.currency.val(1).trigger("change");
                }
                if(values.price.period) {
                    filterInp.price.period.val(values.price.period).trigger("change");
                } else {
                    filterInp.price.period.val("day").trigger("change");
                }
            } else {
                filterInp.price.min.val(undefined);
                filterInp.price.min.val(undefined);
                filterInp.price.currency.val(1).trigger("change");
                filterInp.price.period.val("day").trigger("change");
            }
            if(values.area) {
                if(values.area.min) {
                    filterInp.area.min.val(values.area.min);
                } else {
                    filterInp.area.min.val(undefined);
                }
                if(values.area.max) {
                    filterInp.area.max.val(values.area.max);
                } else {
                    filterInp.area.max.val(undefined);
                }
            } else {
                filterInp.area.min.val(undefined);
                filterInp.area.max.val(undefined);
            }
            if(values.property_types && _.isArray(values.property_types)) {
                _.forEach(values.property_types, function(value) {
                    filterInp.property_types.filter(function() {
                        return this.value === value;
                    }).prop("checked", true);
                });
            } else {
                filterInp.property_types.prop("checked", false);
            }
            if(values.rooms && _.isArray(values.rooms)) {
                _.forEach(values.rooms, function(value) {
                    filterInp.rooms.filter(function() {
                        return this.value === value;
                    }).prop("checked", true);
                });
            } else {
                filterInp.rooms.prop("checked", false);
            }
            if(values.hd_photos) {
                filterInp.hd_photos.prop('checked', true);
            } else {
                filterInp.hd_photos.prop('checked', false);
            }
            if(values.persons) {
                filterInp.persons.val(values.persons);
            } else {
                filterInp.persons.val(undefined);
            }
            if(values.long_rent) {
                filterInp.long_rent.prop('checked', true);
            } else {
                filterInp.long_rent.prop('checked', false);
            }
            if(values.short_rent) {
                filterInp.long_rent.prop('checked', true);
            } else {
                filterInp.long_rent.prop('checked', false);
            }
            if(values.child_friendly) {
                filterInp.child_friendly.prop('checked', true);
            } else {
                filterInp.child_friendly.prop('checked', false);
            }
            if(values.pets_allowed) {
                filterInp.pets_allowed.prop('checked', true);
            } else {
                filterInp.pets_allowed.prop('checked', false);
            }

        };
        this.filterSorting = $('.sorting-select');
        this.filterForm = $('#filter-form');
        this.getValues = function() {
            var price = {
                  min: filterInp.price.min.val(),
                  max: filterInp.price.max.val()
                },
                area = {
                    min: filterInp.area.min.val(),
                    max: filterInp.area.max.val()
                },
                property_types = filterInp.property_types.filter(':checked').map(function() {
                    return this.value;
                }).get(),
                rooms = filterInp.rooms.filter(':checked').map(function() {
                    return this.value;
                }).get(),
                values = {};
            values.hd_photos = filterInp.hd_photos.is(':checked');
            if(area.min || area.max) {
                values.area = {};
                if (area.min) {
                    values.area.min = area.min;
                }
                if (area.max) {
                    values.area.max = area.max;
                }
            }else{
                values.area = null;
            }
            if(price.min || price.max) {
                values.price = {};
                if(price.min) {
                    values.price.min = price.min;
                }
                if(price.max) {
                    values.price.max = price.max;
                }
                values.price.currency = filterInp.price.currency.val();
            }else{
                values.price = null;
            }
            values.property_types = _.isEmpty(property_types) ? null : property_types;
            values.rooms = _.isEmpty(rooms) ? null : rooms;
            if( catogory === 'rent' ) {
                values.persons = filterInp.persons.val() || null;
                values.short_rent = filterInp.short_rent.is(':checked');
                values.long_rent = filterInp.long_rent.is(':checked');
                values.child_friendly = filterInp.child_friendly.is(':checked');
                values.pets_allowed = filterInp.pets_allowed.is(':checked');
                if(price.min || price.max) {
                    values.price.period = filterInp.price.period.val();
                }
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
                this.filterSorting.select2({
                    minimumResultsForSearch: Infinity,
                    containerCssClass : "sorting-select",
                    dropdownCssClass: "sorting-select-dropdown",
                    width: "100%"
                });
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
                    $this.closeFilter();
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
                $('.filter-toggle').addClass('active').tooltip('destroy');
                $('.sp-filters').addClass('open');
                that.scrollTo(0, 1000, 200);
            });
        }
    };

    function Single() {
        var $this = this;
        this.postContainer = $('.blog-list-wrapper');
        this.isModalExists = function() {
            return $('.single-post-modal').length;
        };
        this.closeModal = function(ev) {
            ev.preventDefault();
            $('.single-post-modal').remove();
            $('html').removeClass('overflow-height');
            if( Helpers.isHhistoryApiAvailable()) {
                window.history.pushState(null, null, $this.location);
            }
        };
        this.renderHtml = function(postData) {
            $('html').addClass('overflow-height');

            if( $this.isModalExists() === 0 ) {
                var postHtml = '<div class="single-post-container single-post-modal">' +
                    '<header class="single-post-header">' +
                    '<div class="single-post-wrap">' +
                    '<button type="button" class="btn btn-single-close"><span>Close</span></button>' +
                    '<div class="social-sharing"><ul>' +
                    '<li class="label">Share</li>' +
                    '<li><a href="mailto:?subject=' + postData.post.title + '&body=' + postData.post.link + '" class="soc-icon email-icon"></a></li>' +
                    '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' + postData.post.url + '" target="_blank" class="soc-icon fb-icon"></a></li>' +
                    '<li><a href="https://twitter.com/intent/tweet?text=' + postData.post.share_title + '&url=' + postData.post.url + '&via=leadingpro" target="_blank" class="soc-icon twitter-icon"></a></li>' +
                    '<li><a href="https://www.linkedin.com/shareArticle?mini=true&url=' + postData.post.url + '&title=' + postData.post.share_title + '&summary=' + postData.post.excerpt + '" target="_blank" class="soc-icon ln-icon"></a></li>' +
                    '<li><a href="https://plus.google.com/share?url=' + postData.post.url + '" target="_blank" class="soc-icon gplus-icon"></a></li>' +
                    '</ul>' +
                    '</div>' +
                    '</div>' +
                    '</header><!-- /.single-post-header -->' +
                    '<div class="single-post-content">' +
                    '<div class="single-post-content-inner">' +
                    '<div class="single-post-thumbnail">' +
                    '<img src="' + postData.post.image + '" alt="' + postData.post.title + '" class="img-responsive">' +
                    '</div>' +
                    '<div class="single-post-details"><h1 class="single-post-title" itemprop="headline">' + postData.post.title + '</h1>' +
                    '<div class="entry-meta main-entry-meta">' +
                    '<time class="updated" datetime="' + postData.post.dates.format + '">' + postData.post.dates.view + '</time>' +
                    '<span class="post-tags">' + postData.post.tag + '</span></div>' +
                    '<div class="entry-content" itemprop="articleBody">' + postData.post.content + '</div><!-- /.entry-content -->' +
                    '</div><!-- /.single-post-details --></div><!-- /.single-post-content-inner --></div><!-- /.single-post-content -->';
                if(postData.adj.length > 0 ) {
                    postHtml += '<div class="adjacent-posts-container blog-list-wrapper"><div class="container"><div class="row">';
                    $.each(postData.adj, function(idx, val) {
                        postHtml += '<article id="post-' + val.id + '" class="blog-item adjacent-item" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">' +
                            '<div class="blog-inner-wrapper">' +
                            '<div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">' +
                            '<a href="' + val.link + '" class="blog-thumbnail-holder open-post-modal" data-id="' + val.id + '">' +
                            '<img src="' + val.image + '" alt="' + val.title + '" class="img-responsive">' +
                            '</a></div>' +
                            '<div class="blog-info-holder">' +
                            '<h2 class="info-title" itemprop="headline"><a href="' + val.link + '">' + val.title + '</a></h2>' +
                            '<div class="entry-meta">' +
                            '<time class="updated" datetime="' + val.dates.format + '">' + val.dates.view + '</time>' +
                            '<span class="post-tags">' + val.tag + '</span></div>' +
                            '</div></div></article><!-- /.blog-item -->';
                    });
                    postHtml += '</div></div></div>';
                }
                postHtml += '<div class="single-object-backdrop"></div></article><!-- /.single-post-container -->';

                $this.postContainer.append(postHtml);
            } else {
                $('.single-post-modal .email-icon').attr("href", 'mailto:?subject=' + postData.post.title + '&body=' + postData.post.link);
                $('.single-post-modal .fb-icon').attr("href", 'https://www.facebook.com/sharer/sharer.php?u=' + postData.post.url);
                $('.single-post-modal .twitter-icon').attr("href", 'https://twitter.com/intent/tweet?text=' + postData.post.share_title + '&url=' + postData.post.url + '&via=leadingpro');
                $('.single-post-modal .ln-icon').attr("href", 'https://www.linkedin.com/shareArticle?mini=true&url=' + postData.post.url + '&title=' + postData.post.share_title + '&summary=' + postData.post.excerpt);
                $('.single-post-modal .gplus-icon').attr("href", 'https://plus.google.com/share?url=' + postData.post.url);
                $('.single-post-thumbnail img').attr("src", postData.post.image)
                    .attr("alt", postData.post.title);
                $('.single-post-thumbnail a').attr("src", postData.post.link);
                $('.single-post-title').text(postData.post.title);
                $('.main-entry-meta .updated').attr("datetime", postData.post.dates.format).text(postData.post.dates.view);
                $('.main-entry-meta .post-tags').html(postData.post.tag);
                $('entry-content').html(postData.post.content);
                $('.adjacent-item').each(function(idx) {
                    if(postData.adj[idx] !== 'undefined') {
                        $(this).attr("id", 'post-' + postData.adj[idx].id)
                            .find('.blog-thumbnail-holder')
                            .attr("href", postData.adj[idx].link)
                            .data("id", postData.adj[idx].id)
                            .find("img")
                            .attr("src", postData.adj[idx].image)
                            .attr("alt", postData.adj[idx].title);
                        $(this).find('.info-title a')
                            .attr("href", postData.adj[idx].link)
                            .data("id", postData.adj[idx].id)
                            .text(postData.adj[idx].title);
                        $(this).find('.updated').attr("datetime", postData.adj[idx].dates.format).text(postData.adj[idx].dates.view);
                        $(this).find('.post-tags').html(postData.adj[idx].tag);

                    }
                });
            }
        };
        this.getSinglePost = function(ev) {
            ev.preventDefault();
            var data = {
                'action' : 'do_ajax',
                'fn' : 'get_single_post'
            };
            $this.showLoader(true);
            if(ev.type === 'click') {
                var url = $(this).attr('href');
                data.type = 'id';
                data.id = $(this).data('id');
            } else {
                data.type = 'slug';
                data.id = window.location.pathname;
            }
            $.ajax({
                url: LpData.ajaxUrl,
                dataType : 'json',
                method: 'post',
                data : data,
                success : function(data){
                    if(typeof data === 'object' ){
                        $this.renderHtml(data);
                        if(url !== $this.location){
                            if( Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                window.history.pushState(null, null, url);
                            }
                        }
                    } else {}
                },
                error : function (error){
                    console.error(error);
                },
                complete: function() {
                    $this.showLoader(false);
                }
            });

        };
        this.testPopstste = function(ev) {
            if( $this.isModalExists && window.location.pathname === $this.location) {
                $('.btn-single-close').trigger('click.lprop');
            } else if(window.location.pathname ) {
                $this.getSinglePost(ev);
            }
        };
        this.showLoader = function(state) {
            if(state) {
                $('<div class="post-overlay loader"><span class="spin"></span></div>').appendTo($this.postContainer);
            } else {
                $('.post-overlay').remove();
            }
        };
        this.eventListeners = function() {
            this.postContainer.on('click.lprop', '.open-post-modal', $this.getSinglePost);
            $(window).on('popstate', $this.testPopstste);
            this.postContainer.on('click.lprop', '.btn-single-close', $this.closeModal);
            this.postContainer.on('click.lprop', '.single-object-backdrop', $this.closeModal);
        };
        this.init = function() {
            this.location = window.location.pathname;
            this.eventListeners();
        };
    }
    function Blog() {
        var $this = this;
        this.postContainer = $('.blog-list-wrapper > .container > .row');
        this.perPage = LpData.perPage;
        this.totalPosts = LpData.totalPost;
        this.loader = $('.loader');
        this.lastItem = function() {
            return $('.blog-item').last();
        };
        this.tag = LpData.tag;
        this.didScroll = false;
        this.onPage =  $('.blog-item').length;
        this.renderHtml = function(postdata) {
            var postHtml = '';
            $.each(postdata, function(idx, val) {
                postHtml += '<article id="post-' + val.id + '" class="blog-item" itemprop="blogPost" itemscope itemtype="http://schema.org/BlogPosting">' +
                    '<div class="blog-inner-wrapper">' +
                    '<div class="blog-thumbnail" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">' +
                    '<meta itemprop="contentUrl" content="' + val.image + '">' +
                    '<a href="' + val.link + '" class="blog-thumbnail-holder open-post-modal" data-id="' + val.id + '">' +
                    '<img src="' + val.image + '" alt="' + val.title + '" class="img-responsive" itemprop="contentUrl">' +
                    '</a></div>' +
                    '<div class="blog-info-holder">' +
                    '<h2 class="info-title" itemprop="headline"><a href="' + val.link + '" class="open-post-modal" data-id="' + val.id + '">' + val.title + '</a></h2>' +
                    '<div class="entry-meta">' +
                    '<time class="updated" datetime="' + val.dates.format + '">' + val.dates.view + '</time>' +
                    val.tag + '</div>' +
                    '</div></div></article><!-- /.blog-item -->';
            });
            $this.postContainer.append(postHtml);
        };
        this.getPosts = function() {
            $this.loader.show();
            var data = {
                'action' : 'do_ajax',
                'fn' : 'get_blog_posts',
                'offset' : $this.onPage,
                'posts_per_page': $this.perPage
            };
            if( $this.tag ) {
                data.tag = $this.tag;
            }
            $.when(
                $.ajax({
                    url: LpData.ajaxUrl,
                    dataType : 'json',
                    method: 'post',
                    data: data,
                    success : function(data){
                        if(data.length !== 'undefined' && data.length > 0 ){
                            $this.onPage += data.length;
                            $this.renderHtml(data);
                        } else {

                        }

                    },
                    error : function (error){
                        console.error(error);
                    },
                    complete: function() {
                        $this.loader.hide();
                    }
                })
            ).then(function(){
                    if(  $this.onPage < $this.totalPosts ) {
                        if(Helpers.isElementIntoView($this.lastItem())) {
                            $this.getPosts();
                        }
                        $this.didScroll = false;
                    } else {
                        $(window).off('scroll.lprop', $this.getPosts);
                        $(window).off('load.lprop', $this.getPosts);
                        $(window).off('resize.lprop', $this.getPosts);
                    }
                });
        };
        this.scrollPage = function() {
            if (Helpers.isElementIntoView($this.lastItem()) && !$this.didScroll) {
                $this.didScroll = true;
                $this.getPosts();
            }
        };
        this.eventListeners = function() {
            if( this.onPage < this.totalPosts) {
                $(window).on('scroll.lprop', $this.scrollPage);

                if (Helpers.isElementIntoView($this.lastItem())) {
                    $(window).on('load.lprop', $this.getPosts);
                    $(window).on('resize.lprop', $this.getPosts);
                }
            }
        };
        this.init = function() {
            this.eventListeners();
        };
    }


    /*Object Shaare Bar*/

    function ObjectShareBar(type, category) {
        var shareLinks = $('.favorites-sharing a'),
            shEmail = $('.obj-share-email'),
            shFb = $('.obj-share-fb'),
            shTw = $('.obj-share-tw'),
            shLn = $('.obj-share-ln'),
            shGp = $('.obj-share-gplus'),
            shInput = $('.fav-link-input'),
            baseUrl = category === 'sale' ? LpData.saleSharer + '?ids=' : LpData.rentSharer + '?ids=';
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
        this.sharing =  ( type === 'favorites' || type === 'share' ) ? new ObjectShareBar(type, category) : false;
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
            if( category !== 'single' ) {
                $this.setCounters(favorites);
            }

            if($this.sharing && $this.type !== 'share' ) {
                $this.sharing.setUrls(favorites);
            }

            if( favorites.length === 0 ) {
                localStorage.removeItem(storageName);
                if($this.type === 'favorites') {
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
        this.markButton = function(favorites) {
            var btn = $('.add-favorite-button'),
                id = btn.data('id');
            if(_.includes(favorites, id)) {
                btn.addClass('in-favorites').data('action', 'remove');
            } else {
                btn.data('action', 'add');
            }
        };
        this.init = function() {
            $(window).on('load.lprop', function() {
                var favorites = Helpers.getFavorites(category);
                if($this.type !== 'share') {
                    $this.setCounters(favorites);
                }

                if( $this.type === 'single' || $this.type === 'share' ) {
                    $this.markButton(favorites);
                }


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
              /*  if(objectlist.type === 'list') {
                    objectlist.setUrls(objectlist.args);
                }
                */
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
            var dataType = type === 'rent' ? ' data-is_rent="true"' : '';

            if( isModalExists() ) {
                $('.single-object-modal').remove();
            }
            try {
                var objectHtml = '<div class="single-object-container single-object-modal">' +
                    '<header class="single-object-header">' +
                    '<div class="single-object-wrap">' +
                    '<div class="detailed-link-wrap">' +
                    '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-object-type="' + type + '" data-type="single_property" data-id="' + data.id + '" data-code="' + data.code + '" data-target=".single-object-request"><span>Get detailed information</span></a>' +
                    '</div>' +
                    '<button type="button" class="btn btn-single-close">Close</button>' +
                    '<ul class="single-object-menu">' +
                    '<li><a href="#" data-id="' + data.id + '"' + dataType + ' class="pdf-link"><sup class="text-red">PDF</sup></a></li>' +
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
                            '<img src="' + data.parameters.images[i] + '" alt="' + title + '">' +
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
                            objectHtml += data.parameters.price.min.formatMoney(0, ',', ' ');
                        }
                        if (!_.isNull(data.parameters.price.max)) {
                            objectHtml += '&nbsp;-&nbsp;' + data.parameters.price.max.formatMoney(0, ',', ' ');
                        }
                    }
                    objectHtml += '&nbsp;' + data.parameters.price.currency + '</p>';
                    objectHtml += '<p class="object-code icon">' + data.code + '</p>' +
                        '</div>';
                } else if( type === 'rent' ) {

                    objectHtml += '<div class="rent-rate">';

                    if( data.property_rent.long_rent === true ) {
                        objectHtml += '<p class="icon icon-month">Long term rental, monthly rate: ';
                        if(data.property_rent.rent_long.on_demand === true || _.isNull(data.property_rent.rent_long.monthly_rate)) {
                            objectHtml += 'on request*';
                        } else if(!_.isNull(data.property_rent.rent_long.monthly_rate)) {
                            objectHtml += data.property_rent.rent_long.monthly_rate.formatMoney(0, ',', ' ') + '&nbsp;' + data.property_rent.rent_long.currency_code + '*';
                        }
                        objectHtml += '</p>';
                        objectHtml += '<hr>';
                        objectHtml += '<p class="footnote">* ';
                        if(data.property_rent.rent_long.vat_in_price === false) {
                            if( !_.isNull(data.property_rent.rent_long.vat) ) {
                                objectHtml += 'VAT ' + data.property_rent.rent_long.vat + '% is not included; ';
                            }
                        } else {
                            objectHtml += 'VAT is included; ';
                        }

                        objectHtml += 'deposit: ';
                        if(data.property_rent.rent_long.deposit_on_demand === true) {
                            objectHtml += 'on request; ';
                        } else {
                            if (data.property_rent.rent_long.deposit_type === 1) {
                                if(!_.isNull(data.property_rent.rent_long.deposit)) {
                                    objectHtml += data.property_rent.rent_long.deposit + '%; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            } else {
                                if(!_.isNull(data.property_rent.rent_long.deposit)) {
                                    objectHtml += data.property_rent.rent_long.deposit.formatMoney(0, ',', ' ') + '&nbsp;' + data.property_rent.rent_long.currency_code + '; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            }
                        }

                        objectHtml += 'commission: ';
                        if(data.property_rent.rent_long.commission_on_demand === true) {
                            objectHtml += 'on request; ';
                        } else {
                            if (data.property_rent.rent_long.commission_type === 1) {
                                if(!_.isNull(data.property_rent.rent_long.commission)) {
                                    objectHtml += data.property_rent.rent_long.commission + '%; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            } else {
                                if(!_.isNull(data.property_rent.rent_long.commission)) {
                                    objectHtml += data.property_rent.rent_long.commission + ' ' + data.property_rent.rent_long.currency_code + '; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            }
                        }
                        objectHtml += '</p>';
                    }

                    if( data.property_rent.short_rent === true ) {
                        objectHtml += '<p class="icon icon-day">Short term rental, daily rate, ' + data.property_rent.rent_short.currency_code + '*</p>';
                        objectHtml += '<div class="rent-price-wrap">' +
                                      '<ul class="rent-price-list">' +
                                      '<li class="heading">min. stay</li>' +
                                      '<li class="heading">low season</li>' +
                                      '<li>1 day</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ls_daily_rate)) ? data.property_rent.rent_short.ls_daily_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li>1 week</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ls_weekly_rate)) ? data.property_rent.rent_short.ls_weekly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li>1 month</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ls_monthly_rate)) ? data.property_rent.rent_short.ls_monthly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '</ul>';

                        objectHtml += '<ul class="rent-price-list">' +
                                      '<li class="heading hidden-760">min. stay</li>' +
                                      '<li class="heading">medium season</li>' +
                                      '<li class="hidden-760">1 day</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ms_daily_rate)) ? data.property_rent.rent_short.ms_daily_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li class="hidden-760">1 week</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ms_weekly_rate)) ? data.property_rent.rent_short.ms_weekly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li class="hidden-760">1 month</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.ms_monthly_rate)) ? data.property_rent.rent_short.ms_monthly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '</ul>';

                        objectHtml += '<ul class="rent-price-list">' +
                                      '<li class="heading hidden-760">min. stay</li>' +
                                      '<li class="heading">high season</li>' +
                                      '<li class="hidden-760">1 day</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.hs_daily_rate)) ? data.property_rent.rent_short.hs_daily_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li class="hidden-760">1 week</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.hs_weekly_rate)) ? data.property_rent.rent_short.hs_weekly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '<li class="hidden-760">1 month</li>';
                        objectHtml += '<li>';
                        objectHtml += (!_.isNull(data.property_rent.rent_short.hs_monthly_rate)) ? data.property_rent.rent_short.hs_monthly_rate.formatMoney(0, ',', ' ') : '&mdash;';
                        objectHtml += '</li>';
                        objectHtml += '</ul>';
                        objectHtml += '</div>' +
                                      '<p class="footnote">*';
                        if( data.property_rent.rent_short.vat_in_price === false ) {
                            if( !_.isNull(data.property_rent.rent_short.vat) ) {
                                objectHtml += 'VAT ' + data.property_rent.rent_short.vat + '% is not included; ';
                            }
                        } else {
                            objectHtml += 'VAT is included; ';
                        }
                        objectHtml += 'deposit: ';
                        if(data.property_rent.rent_short.deposit_on_demand === true) {
                            objectHtml += 'on request; ';
                        } else {
                            if (data.property_rent.rent_short.deposit_type === 1) {
                                if(!_.isNull(data.property_rent.rent_short.deposit)) {
                                    objectHtml += data.property_rent.rent_short.deposit + '%; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            } else {
                                if(!_.isNull(data.property_rent.rent_short.deposit)) {
                                    objectHtml += data.property_rent.rent_short.deposit + ' ' + data.property_rent.rent_short.currency_code + '; ';
                                } else {
                                    objectHtml += 'on request; ';
                                }
                            }
                        }

                        objectHtml += 'commission: ';
                        if(data.property_rent.rent_short.commission_on_demand === true) {
                            objectHtml += 'on request; ';
                        } else {
                            if (data.property_rent.rent_short.commission_type === 1) {
                                if(!_.isNull(data.property_rent.rent_short.commission)) {
                                    objectHtml += data.property_rent.rent_short.commission + '%';
                                } else {
                                    objectHtml += 'on request';
                                }
                            } else {
                                if(!_.isNull(data.property_rent.rent_short.commission)) {
                                    objectHtml += data.property_rent.rent_short.commission + ' ' + data.property_rent.rent_short.currency_code;
                                } else {
                                    objectHtml += 'on request';
                                }
                            }
                        }
                        objectHtml += '</p>';
                    }

                    objectHtml += '</div><!-- /.rent-rate -->';

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

                if(  type === 'rent' ) {
                    if( !_.isNull(data.property_rent.persons_max) ) {
                        objectHtml += '<li>max. persons</li><li>' + data.property_rent.persons_max + '</i></li>';
                    }
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
                if(  type === 'rent' ) {
                    if( data.property_rent.child_friendly === true ) {
                        objectHtml += '<li>child friendly</li><li><i class="icon icon-checkmark-circle"></i></li>';
                    }
                    if( data.property_rent.pets_allowed === true ) {
                        objectHtml += '<li>pets allowed</li><li><i class="icon icon-checkmark-circle"></i></li>';
                    }
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
                    '</div><!-- /.single-object-content -->' +
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
                    '<a href="#" class="btn btn-green btn-detailed-link" data-toggle="modal" data-target=".single-object-request"  data-object-type="' + type + '" data-type="single_property" data-id="' + data.id + '" data-code="' + data.code + '"><span>Get detailed information</span></a>' +
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
                    if( !_.isEmpty(data) ) {
                        if(data.error) {
                            console.log(data.error);
                        } else {
                            $this.prevLink = prevObjectLink(data.id);
                            $this.nextLink = nextObjectLink(data.id);
                            if (data.id === objectlist.triggerId) {
                                objectlist.getObjects(function () {
                                    $this.renderSingleHtml(data);
                                    if (url !== $this.location) {
                                        if (Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                            window.history.pushState(null, null, url);
                                        }
                                    }
                                });
                            } else {
                                $this.renderSingleHtml(data);
                                if (url !== $this.location) {
                                    if (Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                        window.history.pushState(null, null, url);
                                    }
                                }
                            }
                        }
                    }

                },
                error : function (error){
                    console.error(error);
                },
                complete: function() {
                    $this.showLoader(false);
                }
            });

        };
        this.testPopstste = function(ev) {
            if(window.location.href.search(LpData.propertyPage) !== -1) {
                if (isModalExists() && window.location.pathname === $this.location) {
                    $('.btn-single-close').trigger('click.lprop');
                } else {
                    $this.getSingleObject(ev);
                }
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
            this.location = window.location.href;
            this.eventListeners();
        };
    }

    /* Object List */
    function ObjectList(type, category) {
        var $this = this,
            loader = $('.loader'),
            filter = new FilterMenu(type, category),
            singleObject = new SingleObject(this);

        function resetObjects() {
            $this.objectContainer.html('');
            $this.args.page = 1;
            $this.onPage = 0;
        }

        function setOffmarker(count) {
            var omLink = $('.off-market-menu a'),
                omMenu = $('.menu-offmarket'),
                omFb = $('.off-market-link'),
                omPanel = $('.off-marker-alert');
            if(count > 0) {
                omLink
                    .off('click', Helpers.preventDefault)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                omMenu
                    .on('click', Helpers.showOffmarketModal)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                omFb
                    .on('click', Helpers.showOffmarketModal)
                    .removeClass('half-opaque')
                    .find('sup')
                    .text(count);
                setTimeout(function() {
                    omPanel.show();
                }, 2500);
            } else {
                omLink
                    .on('click', Helpers.preventDefault)
                    .addClass('half-opaque')
                    .find('sup')
                    .text('');
                omMenu
                    .off('click', Helpers.showOffmarketModal)
                    .addClass('half-opaque')
                    .find('sup')
                    .text('');
                omFb
                    .off('click', Helpers.showOffmarketModal)
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
            $this.usedFilters.location = false;
        }
        function clearFilters() {
            if($this.args.price) {
                delete $this.args.price;
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
            if($this.args.long_rent) {
                delete $this.args.long_rent;
            }
            if($this.args.short_rent) {
                delete $this.args.short_rent;
            }
            if($this.args.child_friendly) {
                delete $this.args.child_friendly;
            }
            if($this.args.pets_allowed) {
                delete $this.args.pets_allowed;
            }
        }
        function clearAllFilters() {
            clearAutoSearch();
            clearFilters();
        }

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
            }

            if(!silent){
                $this.getObjects();
            }
        };
        if(type === 'list') {
            this.autoComplete = new window.lpw.AutoComplete(
                '#sp-search',
                $this.autoSearch
            );
            this.lpwGoogleMap = new window.lpw.Map(
                '#map-modal',
                category,
                $this.autoComplete
            );
            this.tags = new window.lpw.Tags(
                LpData.ajaxUrl,
                $this.autoComplete,
                filter.filterForm
            );
        }
        this.lastItem = function() {
            return $('.object-item').last();
        };
        this.type = type;
        this.favorites = new Favorites(type, category);
        this.favoritesIds = $this.favorites.favoritesIds;
        this.objectContainer = $('#object-list');
        this.onPage = 0;
        this.totalObjects = parseInt(LpData.totalObjects);
        this.didScroll = false;
        this.triggerId = 0;
        this.usedFilters = {
          location: false
        };
        this.args = {
            lang: LpData.lang,
            page: 1,
            per_page: 9,
            for_sale: ( category === 'sale' ),
            for_rent: ( category === 'rent' )
        };

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
                            rentPrice = '<span class="price-num">' + object.property_rent.rent_short.sort_price.day.formatMoney(0, ',', ' ') + ' </span>' + object.property_rent.rent_short.sort_price.currency_code + '&nbsp;/&nbsp;day';
                        } else if(!_.isNull(object.property_rent.rent_short.sort_price.month)) {
                            rentPrice = '<span class="price-num">' + object.property_rent.rent_short.sort_price.month.formatMoney(0, ',', ' ') + ' </span>' + object.property_rent.rent_short.sort_price.currency_code + '&nbsp;/&nbsp;month';
                        }
                    } else if( object.property_rent.long_rent === true ) {
                        if(!_.isNull(object.property_rent.rent_long.sort_price.month)) {
                            rentPrice = '<span class="price-num">' + object.property_rent.rent_long.sort_price.month.formatMoney(0, ',', ' ') + ' </span>' + object.property_rent.rent_long.sort_price.currency_code + '&nbsp;/&nbsp;month';
                        } else if(!_.isNull(object.property_rent.rent_long.sort_price.day)) {
                            rentPrice = '<span class="price-num">' + object.property_rent.rent_long.sort_price.day.formatMoney(0, ',', ' ') + ' </span>' + object.property_rent.rent_long.sort_price.currency_code + '&nbsp;/&nbsp;day';
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
                    if( category === 'rent' && object.property_rent) {
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
                                objectHtml += '<span>' + object.parameters.price.min.formatMoney(0, ', ', ' ') + '</span>';
                            }
                            if (!_.isNull(object.parameters.price.max)) {
                                objectHtml += '&nbsp;&ndash;&nbsp;<span>' + object.parameters.price.max.formatMoney(0, ',', ' ') + '</span>';
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
                            objectHtml += '&nbsp;&ndash;&nbsp;<span>' + object.parameters.rooms.max + '</span>';
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
                            objectHtml += '&nbsp;&ndash;&nbsp;<span>' + object.parameters.area.max + '</span>';
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
        this.getObjects = function (callback, eventType) {

            var autocomplete = $this.args.autocomplete || null,
                dataUrl;

            loader.show();
            data = $this.args;
            dataUrl = $this.args;
            data.action = 'do_ajax';


            if( type === 'list' && eventType !== 'scroll' ) {

                $this.tags.buildTags(data);

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


                $this.setUrls(dataUrl, eventType);
            }

            data.fn = 'get_objects';
                $.ajax({
                    url: LpData.ajaxUrl,
                    dataType: 'json',
                    method: 'post',
                    data: data,
                    success: function (data) {
                        if( !_.isEmpty(data) ) {
                            if (data.error) {
                                console.log(data.errorMessage);
                            } else if (_.isArray(data.property_objects)) {
                                if($this.usedFilters.location === true) {
                                    setOffmarker(data.offmarket);
                                } else {
                                    setOffmarker(0);
                                }
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
                            $(window).off('scroll.lprop', $this.scrollPage);
                            $(window).off('load.lprop', $this.onLoadCheck);
                            $(window).off('resize.lprop', $this.onLoadCheck);
                        }
                    }
                });

        };
        this.scrollPage = function() {

            if ( _.isEmpty($this.lastItem()) || !$this.didScroll && Helpers.isElementIntoView($this.lastItem()) ) {
                $this.didScroll = true;
                $this.getObjects(null, 'scroll');
            }
        };
        this.onLoadCheck = function(ev) {
            var query = Helpers.getParameterByName('filter'),
                eventtype = ev.type;
                if(eventtype === 'popstate' && (window.location.href.search(LpData.propertyPage) === -1)) {
                    clearAllFilters();
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

                        }

                        resetObjects();
                        $this.getObjects(null, eventtype);
                        filter.setValues(query);
                    }
                } catch(e) {
                    console.log(e);
                }
            } else {
                resetObjects();
                $this.usedFilters.location = false;
                $this.getObjects(null, eventtype);
            }
        };
        this.setEventListeners = function () {
            $('.off-market-menu a').on('click.lprop', function(ev) {
                ev.preventDefault();
                if(!$(this).hasClass('half-opaque')) {
                    $('.off-marker-alert').show();
                }
            });

            if( this.onPage < this.totalObjects) {
                $(window).on('scroll.lprop', $this.scrollPage);
                $(window).on('popstate', $this.onLoadCheck);
                if (_.isEmpty($this.lastItem()) || Helpers.isElementIntoView($this.lastItem())) {
                    if(type !== 'share') {
                        $(window).on('load.lprop', $this.onLoadCheck);
                    }
                    $(window).on('resize.lprop', $this.onLoadCheck);
                }
            }
            if( type === 'list' ) {
                filter.filterForm.on('submit', function (ev) {
                    ev.preventDefault();
                    var args = filter.getValues();

                    if (!_.isEmpty(args)) {
                        filter.closeFilter();
                        _.forEach(args, function (value, key) {
                            $this.args[key] = value;
                        });
                        $this.args = _.pickBy($this.args);
                        resetObjects();
                        $this.getObjects();
                    }
                });
            }
            filter.filterSorting.on('change', function() {
                var val = $(this).val();
                    $this.args.order_by = {
                        order: val
                    };
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
    ObjectList.prototype.setUrls = function(data, eventtype) {
      var url = window.location.protocol + '//' + window.location.hostname + window.location.pathname,
          excluded = ['action', 'fn', 'page', 'per_page', 'for_sale', 'for_rent', 'lang'];
        data = _.omit(data, excluded);
        if(!_.isEmpty(data)) {
            data = JSON.stringify(data);
            if(eventtype !== 'popstate') {
                window.history.pushState(null, null, url + '?filter=' + encodeURIComponent(data));
            }

        } else {
            window.history.pushState(null, null, url);
        }
    };



  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Lprops = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
          SidebarMenuEffects.init();

          $('.menu .menu-buy > a').append(' <sup class="text-red">' + LpData.totalSale + '</sup>');
          $('.menu .menu-rent > a').append(' <sup class="text-red">' + LpData.totalRent + '</sup>');

          $('body').on('click.lprop', '.alert-close', function(ev) {
              ev.preventDefault();
              var target = $(this).data('target');
              $(target).hide();
          });

          $('.tooltip-type-1').tooltip({
              html: true,
              delay: { "show": 200, "hide": 300 }
          });
          /*Search tooltip */
          $('.tooltip-type-search').tooltip({
              template: '<div class="tooltip tooltip-search" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
              delay: { "show": 200, "hide": 300 }
          });

          var pdf = new window.lpw.Pdf();

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

            var contactForms = new window.lpw.ContactForm();
            contactForms.init();
        }
    },
    'page_rent': {
        init: function() {
            FloatingBar.init();
            var objects = new ObjectList('list', 'rent');
            objects.init();

            var contactForms = new window.lpw.ContactForm();
            contactForms.init();
        }
    },
    'page_favorites': {
      init: function() {
          var objects = new ObjectList('favorites', 'sale');
          objects.init();
          FloatingBar.init();

          var contactForms = new window.lpw.ContactForm();
          contactForms.init();
      }
    },
    'page_favorites_rent': {
      init: function() {
          var objects = new ObjectList('favorites', 'rent');
          objects.init();
          FloatingBar.init();

          var contactForms = new window.lpw.ContactForm();
          contactForms.init();
      }
    },
    'page_single_object': {
      init: function() {
          var singleObjectStyles = (function() {
              var docElem = $('html'),
                  container = $( '.single-object-container' ),
                  didScroll = false,
                  siteFooter = $('.site-footer'),
                  changeFooterOn = siteFooter.offset().top,
                  changeHeaderOn = container.offset().top;
              function init() {
                  $(window).on('load', function (event) {
                      scrollPage();
                  });
                  $(window).on( 'scroll', function( event ) {
                      changeHeaderOn = container.offset().top;
                      changeFooterOn = siteFooter.offset().top;
                      if( !didScroll ) {
                          didScroll = true;
                          scrollPage();
                      }
                  });
              }
              function scrollPage() {
                  var sy = scrollY(),
                      syf = scrollYFooter();
                  if ( sy >= changeHeaderOn ) {
                      container.addClass( 'fixed-header' );
                  }
                  else {
                      container.removeClass( 'fixed-header' );
                  }
                  if($('.single-object-footer').is(":visible")) {
                      if (syf <= changeFooterOn) {
                          container.addClass('fixed-footer');
                      } else {
                          container.removeClass('fixed-footer');
                      }
                  }
                  didScroll = false;
              }
              function scrollY() {
                  return window.pageYOffset || docElem.scrollTop;
              }
              function scrollYFooter() {
                  return (window.pageYOffset + window.innerHeight);
              }
              init();
          })();
          function getCategory() {
              var container = $('.single-object-container');
              if(container.hasClass('object-rent')) {
                  return 'rent';
              }
                  return 'sale';
          }
          var favorites = new Favorites('single', getCategory());
          favorites.init();

          var contactForms = new window.lpw.ContactForm();
          contactForms.init();

      }
    },
    'blog_list': {
      init: function() {
          var blog = new Blog(),
              single = new Single();
          blog.init();
          single.init();
          FloatingBar.init();

          $('.tooltip-sharing').tooltip({
              html: true,
              placement: 'bottom',
              template: '<div class="tooltip tooltip-sharing" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div><a href="#" class="tooltip-close btn-close"></a></div>',
              trigger: 'manual'
          });
          var showSharingTooltips = function() {
              var shown = false,
                  sharingTop = $('.tooltip-sharing-top'),
                  sharingBottom = $('.tooltip-sharing-bottom');
              function showSharingTooltips(start, end, el) {
                  setTimeout(function(){
                      el.tooltip('show');
                      setTimeout(function() {
                          el.tooltip('destroy');
                      }, end);
                  }, start);
              }
              function init() {
                  if(sharingTop.length === 0 || sharingBottom.length === 0) { return false; }
                  /*show top sharing tooltip */
                  $(document).ready(function(){
                      showSharingTooltips(3000, 10000, sharingTop);
                      /* check if bottom in to view*/
                      shown = Helpers.isElementIntoView(sharingBottom);
                  });
                  $('body').on('click', '.tooltip-close', function(ev){
                      ev.preventDefault();
                      $(this).closest('.tooltip').siblings('.tooltip-sharing').tooltip('destroy');
                  });
                  if(!shown) {
                      $(window).on('scroll', function() {
                          if(!shown) {
                              shown = Helpers.isElementIntoView(sharingBottom);
                              if(shown) {
                                  showSharingTooltips(1000, 10000, $(sharingBottom));
                              }
                          }
                      });
                  }
              }

              init();
          }();

          var contactForms = new window.lpw.ContactForm();
          contactForms.init();
      }
  },
    'single_post': {
      init: function() {
          // JavaScript to be fired on the about us page
          var singleObjectStyles = (function() {
              var docElem = $('html'),
                  container = $( '.single-post-container' ),
                  didScroll = false,
                  changeHeaderOn = container.offset().top;
              function scrollY() {
                  return window.pageYOffset || docElem.scrollTop;
              }
              function scrollPage() {
                  var sy = scrollY();

                  if ( sy >= changeHeaderOn ) {
                      container.addClass( 'fixed-header' );
                  }
                  else {
                      container.removeClass( 'fixed-header' );
                  }

                  didScroll = false;
              }
              function init() {
                  $(window).on('load.lprop', function (event) {
                      scrollPage();
                  });
                  $(window).on( 'scroll.lprop', function( event ) {
                      changeHeaderOn = container.offset().top;
                      if( !didScroll ) {
                          didScroll = true;
                          scrollPage();
                      }
                  });
              }
              init();
          })();

          if( Helpers.isHhistoryApiAvailable() ) {
              $(window).on('popstate', function(ev) {
                  window.location.reload();
              });
          }
      }
  },
    'page_static': {
        init: function() {
            $(window).on('load', Helpers.equalheight);
            $(window).on('resize', Helpers.equalheight);
        }
    },
    'page_sharer': {
        init: function() {
            var objects = new ObjectList('share', 'sale');
            objects.args.page = 2;
            objects.onPage = $('.object-item').length;
            objects.args.ids = LpData.ids;
            objects.init();
        }
    },
    'page_sharer_rent': {
        init: function() {
            var objects = new ObjectList('share', 'rent');
            objects.args.page = 2;
            objects.onPage = $('.object-item').length;
            objects.args.ids = LpData.ids;
            objects.init();
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
