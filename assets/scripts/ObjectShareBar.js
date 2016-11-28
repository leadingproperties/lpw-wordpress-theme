(function($){
    "use strict";

    /*Object Share Bar*/

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
            shEmail.attr('href','mailto:?Subject=' + LpData.siteTitle + '&body=' + encodeURIComponent(url + "\n\n" + 'Property selection powered by ' + LpData.siteTitle + ' . Best hand-picked properties with beautiful photos. Want to see properties selected just for you? Visit our website and get your personal recommendation today.'));
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

    window.lpw = window.lpw || {};
    window.lpw.ObjectShareBar = ObjectShareBar;
})(jQuery);