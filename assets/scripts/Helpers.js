(function($){
    "use strict";

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
        },
        createCookie:  function(name, value, days) {
            var expires;
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toGMTString();
            } else {
                expires = "";
            }
            document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
        },
        readCookie: function(name) {
            var nameEQ = encodeURIComponent(name) + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') { c = c.substring(1, c.length); }
                if (c.indexOf(nameEQ) === 0) { return decodeURIComponent(c.substring(nameEQ.length, c.length)); }
            }
            return null;
        },
        eraseCookie: function(name) {
            this.createCookie(name, "", -1);
        },
        inRow: function (parentSelector, childSelector) {
            var count = 0, theTop = undefined;
            $(parentSelector + " > " + childSelector).each(function(){
                var thisTop = $(this).offset().top;
                if(theTop === undefined){
                    theTop = thisTop;
                }
                if(thisTop != theTop){
                    return false;
                }
                count++;
            });
            return count;
        }
    };

    window.lpw = window.lpw || {};
    window.lpw.Helpers = Helpers;
})(jQuery);