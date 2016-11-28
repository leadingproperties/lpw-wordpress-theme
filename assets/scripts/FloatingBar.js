(function($){
    "use strict";

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

    window.lpw = window.lpw || {};
    window.lpw.FloatingBar = FloatingBar;
})(jQuery);