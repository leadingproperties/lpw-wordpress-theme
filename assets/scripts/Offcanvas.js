(function($){
    "use strict";

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
                if( !window.lpw.Helpers.hasParentClass( evt.target, 'side-menu' ) ) {
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

    window.lpw = window.lpw || {};
    window.lpw.SidebarMenuEffects = SidebarMenuEffects;
})(jQuery);