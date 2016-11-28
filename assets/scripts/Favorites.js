(function($){
    "use strict";

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
        this.favoritesIds = window.lpw.Helpers.getFavorites(category);
        this.sharing =  ( type === 'favorites' || type === 'share' ) ? new window.lpw.ObjectShareBar(type, category) : false;
        this.setCounters = function(favorites) {
            if($this.type === 'favorites') {
                $this.fpCounter.text(favorites.length);
            } else {
                if (favorites.length > 0) {

                    $this.mMenulink
                        .off('click.lprop', window.lpw.Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                    $this.dtlink
                        .off('click.lprop', window.lpw.Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                    $this.fbLink
                        .off('click.lprop', window.lpw.Helpers.preventDefault)
                        .removeClass('half-opaque')
                        .find('sup')
                        .text(favorites.length);
                } else {
                    $this.mMenulink
                        .on('click.lprop', window.lpw.Helpers.preventDefault)
                        .addClass('half-opaque')
                        .find('sup')
                        .text('');
                    $this.dtlink
                        .on('click.lprop', window.lpw.Helpers.preventDefault)
                        .addClass('half-opaque')
                        .find('sup')
                        .text('');
                    $this.fbLink
                        .on('click.lprop', window.lpw.Helpers.preventDefault)
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
                        window.lpw.Helpers.goToLocation(LpData.salePage);
                    } else {
                        window.lpw.Helpers.goToLocation(LpData.rentPage);
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
                    if(window.lpw.Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
                        $('#object-' + id).find('.add-favorite-button')
                            .data('action', 'remove')
                            .addClass('in-favorites');
                    }
                    target.data('action', 'remove')
                        .addClass('in-favorites');
                } else if (action === 'remove') {
                    if(window.lpw.Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
                        $('#object-' + id).find('.add-favorite-button')
                            .data('action', 'add')
                            .removeClass('in-favorites');
                    }
                    if($this.type === 'favorites') {
                        if(window.lpw.Helpers.hasParentClass(e.currentTarget, 'single-object-modal')) {
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
        this.markButtons = function(objects, favorites) {
            objects.each(function() {
                var btn = $(this).find('.add-favorite-button'),
                    id = btn.data('id');
                if(_.includes(favorites, id)) {
                    btn.addClass('in-favorites').data('action', 'remove');
                } else {
                    btn.data('action', 'add');
                }
            });
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
                var favorites = window.lpw.Helpers.getFavorites(category);
                if($this.type !== 'share') {
                    $this.setCounters(favorites);
                }

                if( $this.type === 'single' || $this.type === 'share' ) {
                    $this.markButton(favorites);
                }


                if( $this.type === 'favorites' ) {
                    if(favorites.length === 0) {
                        if( category === 'sale' ) {
                            window.lpw.Helpers.goToLocation(LpData.salePage);
                        } else {
                            window.lpw.Helpers.goToLocation(LpData.rentPage);
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
                            window.lpw.Helpers.goToLocation(LpData.salePage);
                        } else {
                            window.lpw.Helpers.goToLocation(LpData.rentPage);
                        }
                    }

                });
            }
        };
    }

    window.lpw = window.lpw || {};
    window.lpw.Favorites = Favorites;
})(jQuery);