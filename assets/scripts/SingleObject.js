(function($){
    "use strict";

    /* Single Object */

    function SingleObject(objectlist) {

        var $this = this,
            objectWrapper = $('.objects-list-wrapper');


        function isModalExists() {
            return $('.single-object-modal').length > 0;
        }

        function closeModal(ev) {
            ev.preventDefault();
            $('.single-object-modal').remove();
            $('body').removeClass('overflow-height');
            if( window.lpw.Helpers.isHhistoryApiAvailable()) {
                window.history.pushState({ action: 'object-close' }, null, $this.location);
                /*  if(objectlist.type === 'list') {
                 objectlist.setUrls(objectlist.args);
                 }
                 */
            }
        }

        function prevObjectLink(id) {
            var prevObject = $('#object-' + id).prev(),
                prevLink = prevObject.find('.object-link');

            if(prevObject.length > 0) {
                return prevLink.attr('href');

            } else {
                return false;
            }
        }
        function nextObjectLink(id) {
            var nextObject = $('#object-' + id).next(),
                nextLink = nextObject.find('.object-link');

            if(nextObject.length > 0) {
                return  nextLink.attr('href');

            } else {
                return false;
            }
        }

        this.renderSingleHtml = function(data, id)  {
            var favs = objectlist.favoritesIds;
            if( isModalExists() ) {
                $('.single-object-modal').remove();
            }
            objectWrapper.append(data);
            $('body').addClass('overflow-height');
            if($this.prevLink) {
                $('.object-prev a').attr('href', $this.prevLink);
            } else {
                $('.object-prev a').removeClass('open-object-modal').addClass('disabled');
            }
            if($this.nextLink) {
                $('.object-next a').attr('href', $this.nextLink);
            } else {
                $('.object-next a').removeClass('open-object-modal').addClass('disabled');
            }

            if( objectlist.type === 'favorites' || _.includes(favs, id) ) {

                $('.single-object-menu .add-favorite-button').addClass('in-favorites').data('action', 'remove');
            }

        };
        this.getSingleObject = function(ev) {
            ev.preventDefault();
            var url;

            if( 'click' === ev.type ) {
                url = $(this).attr('href');
                if(ev.currentTarget.className.indexOf('object-thumbnail-holder') !== -1) {
                    $this.location = window.location.href;
                }

            } else {
                url = window.location.href;
            }
            var data = {
                action: 'do_ajax',
                fn: 'get_object',
                lang: objectlist.args.lang,
                price: {
                    currency: objectlist.args.price.currency
                },
                slug: _.replace(url, LpData.propertyPage, '')
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
                        } else if(!_.isEmpty(data.html)) {
                            $this.prevLink = prevObjectLink(data.id);
                            $this.nextLink = nextObjectLink(data.id);
                            if (data.id === objectlist.triggerId) {
                                objectlist.getObjects(function () {
                                    $this.renderSingleHtml(data.html, data.id);
                                    if (url !== $this.location) {
                                        if (window.lpw.Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
                                            window.history.pushState(null, null, url);
                                        }
                                    }
                                }, 'single');
                            } else {
                                $this.renderSingleHtml(data.html, data.id);
                                if (url !== $this.location) {
                                    if (window.lpw.Helpers.isHhistoryApiAvailable() && 'click' === ev.type) {
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

            if ( window.location.href === $this.location && isModalExists()) {
                $('.btn-single-close').trigger('click.lprop');
            }
            if(window.location.href.search(LpData.propertyPage) !== -1) {
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
            objectWrapper.on('click.lprop', '.open-object-modal', $this.getSingleObject);
            $(window).on('popstate', $this.testPopstste);
            objectWrapper.on('click.lprop', '.btn-single-close', closeModal);
            objectWrapper.on('click.lprop', '.single-object-backdrop', closeModal);
        };

        this.init = function() {
            this.location = window.location.href;
            this.eventListeners();
        };
    }

    window.lpw = window.lpw || {};
    window.lpw.SingleObject = SingleObject;
})(jQuery);