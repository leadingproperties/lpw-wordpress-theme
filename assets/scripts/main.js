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

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Lprops = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
          window.lpw.SidebarMenuEffects.init();

          $('.menu .menu-buy > a').append(' <sup class="text-red">' + LpData.totalSale + '</sup>');
          $('.menu .menu-rent > a').append(' <sup class="text-red">' + LpData.totalRent + '</sup>');
          $('.menu .menu-invest > a').append(' <sup class="text-red">' + LpData.totalInvest + '</sup>');

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

          /* Global currency switcher select2 init */
           var glCurrencySwitcher = $('#global-currency-switcher');
          glCurrencySwitcher.select2({
              minimumResultsForSearch: Infinity,
              containerCssClass : "price-select",
              dropdownCssClass: "price-select-dropdown currency-switcher-dropdown",
              width: "100%"
          });



          var pdf = new window.lpw.Pdf();

          /* Favorites copy link to clipboard functionality */
          $('.fav-link-input').on('click', function() {
              this.select();
          });
          function copyToClipboard(elem) {

              elem.select();
              try {
                  document.execCommand('copy');
              } catch (err) {

              }
          }
          $('.fav-copy').on('click', function(ev) {
              ev.preventDefault();
              copyToClipboard($(this).siblings('.fav-link-input'));
          });
          window.lpw.FloatingBar.init();
          var contactForms = new window.lpw.ContactForm();
          contactForms.init();

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

            var objects = new window.lpw.ObjectList('list', 'sale');

            if(!window.lpw.Helpers.getParameterByName('filter') && LpData.defaultLocation) {
                objects.args.location_point = {
                    country_code: LpData.saleDefaultCoords.location_point.country_code,
                    lat: LpData.saleDefaultCoords.location_point.lat,
                    lon: LpData.saleDefaultCoords.location_point.lon
                };
                objects.args.location_shape = {
                    country_code: LpData.saleDefaultCoords.location_shape.country_code,
                    bottom_left: {
                        lat: LpData.saleDefaultCoords.location_shape.bottom_left.lat,
                        lon: LpData.saleDefaultCoords.location_shape.bottom_left.lon
                    },
                    top_right: {
                        lat: LpData.saleDefaultCoords.location_shape.top_right.lat,
                        lon: LpData.saleDefaultCoords.location_shape.top_right.lon
                    }
                };
                objects.args.autocomplete = {
                    text: LpData.saleDefault
                };
            }

            if(LpData.isLocationPage) {
                if(LpData.defaultCoords) {
                    objects.args.location_point = {
                        country_code: LpData.defaultCoords.location_point.country_code || null,
                        lat: LpData.defaultCoords.location_point.lat || null,
                        lon: LpData.defaultCoords.location_point.lon || null
                    };
                    objects.args.location_shape = {
                        country_code: LpData.defaultCoords.location_shape.country_code || null,
                        bottom_left: {
                            lat: LpData.defaultCoords.location_shape.bottom_left.lat || null,
                            lon: LpData.defaultCoords.location_shape.bottom_left.lon || null
                        },
                        top_right: {
                            lat: LpData.defaultCoords.location_shape.top_right.lat || null,
                            lon: LpData.defaultCoords.location_shape.top_right.lon || null
                        }
                    };
                }
                objects.args.autocomplete = {
                    text: LpData.defaultGeoTitle || null
                };
                if(objects.args.autocomplete.text) {
                    objects.autoComplete.jqInput.val(objects.args.autocomplete.text);
                }
                objects.args.property_types = LpData.propertyType || [];
                if(LpData.propertyType) {
                    objects.filter.setValues(objects.args);
                }

            }

            objects.init();

        }
    },
    'page_rent': {
        init: function() {
            var objects = new window.lpw.ObjectList('list', 'rent');

            if(!window.lpw.Helpers.getParameterByName('filter') && LpData.defaultLocation) {
                objects.args.location_point = {
                    country_code: LpData.rentDefaultCoords.location_point.country_code,
                    lat: LpData.rentDefaultCoords.location_point.lat,
                    lon: LpData.rentDefaultCoords.location_point.lon
                };
                objects.args.location_shape = {
                    country_code: LpData.rentDefaultCoords.location_shape.country_code,
                    bottom_left: {
                        lat: LpData.rentDefaultCoords.location_shape.bottom_left.lat,
                        lon: LpData.rentDefaultCoords.location_shape.bottom_left.lon
                    },
                    top_right: {
                        lat: LpData.rentDefaultCoords.location_shape.top_right.lat,
                        lon: LpData.rentDefaultCoords.location_shape.top_right.lon
                    }
                };
                objects.args.autocomplete = {
                    text: LpData.rentDefault
                };
            }

            if(LpData.isLocationPage) {
                if(LpData.defaultCoords) {
                    objects.args.location_point = {
                        country_code: LpData.defaultCoords.location_point.country_code || null,
                        lat: LpData.defaultCoords.location_point.lat || null,
                        lon: LpData.defaultCoords.location_point.lon || null
                    };
                    objects.args.location_shape = {
                        country_code: LpData.defaultCoords.location_shape.country_code || null,
                        bottom_left: {
                            lat: LpData.defaultCoords.location_shape.bottom_left.lat || null,
                            lon: LpData.defaultCoords.location_shape.bottom_left.lon || null
                        },
                        top_right: {
                            lat: LpData.defaultCoords.location_shape.top_right.lat || null,
                            lon: LpData.defaultCoords.location_shape.top_right.lon || null
                        }
                    };
                }
                objects.args.autocomplete = {
                    text: LpData.defaultGeoTitle || null
                };
                if(objects.args.autocomplete.text) {
                    objects.autoComplete.jqInput.val(objects.args.autocomplete.text);
                }
                objects.args.property_types = LpData.propertyType || [];

                if(LpData.propertyType) {
                    objects.filter.setValues(objects.args);
                }

            }

            objects.init();

        }
    },
    'page_favorites': {
      init: function() {
          var objects = new window.lpw.ObjectList('favorites', 'sale');
          objects.init();

      }
    },
    'page_favorites_rent': {
      init: function() {
          var objects = new window.lpw.ObjectList('favorites', 'rent');
          objects.init();
      }
    },
    'page_commercial': {
        init: function() {
           var invest = new window.lpw.Invest();
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

          //Set global currency cookie and reload page
          $('#global-currency-switcher').on('change', function() {
              window.lpw.Helpers.createCookie('lpw_currency_id', $(this).val());
              window.location.reload();
          });
          function getCategory() {
              var container = $('.single-object-container');
              if(container.hasClass('object-rent')) {
                  return 'rent';
              }
                  return 'sale';
          }
          var favorites = new window.lpw.Favorites('single', getCategory());
          favorites.init();
      }
    },
    'blog_list': {
      init: function() {
          var blog = new window.lpw.Blog(),
              single = new window.lpw.Single();
          blog.init();
          single.init();

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
                      shown = window.lpw.Helpers.isElementIntoView(sharingBottom);
                  });
                  $('body').on('click', '.tooltip-close', function(ev){
                      ev.preventDefault();
                      $(this).closest('.tooltip').siblings('.tooltip-sharing').tooltip('destroy');
                  });
                  if(!shown) {
                      $(window).on('scroll', function() {
                          if(!shown) {
                              shown = window.lpw.Helpers.isElementIntoView(sharingBottom);
                              if(shown) {
                                  showSharingTooltips(1000, 10000, $(sharingBottom));
                              }
                          }
                      });
                  }
              }

              init();
          }();

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

          if( window.lpw.Helpers.isHhistoryApiAvailable() ) {
              $(window).on('popstate', function(ev) {
                  window.location.reload();
              });
          }
      }
  },
    'page_static': {
        init: function() {
            $(window).on('load', window.lpw.Helpers.equalheight);
            $(window).on('resize', window.lpw.Helpers.equalheight);
        }
    },
    'page_sharer': {
        init: function() {
            var objects = new window.lpw.ObjectList('share', 'sale');
            objects.args.page = 2;
            objects.onPage = $('.object-item').length;
            objects.args.ids = LpData.ids;
            objects.init();
        }
    },
    'page_sharer_rent': {
        init: function() {
            var objects = new window.lpw.ObjectList('share', 'rent');
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
