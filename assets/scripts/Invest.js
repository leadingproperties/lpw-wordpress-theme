(function($) {
    "use strict";

    window.lpw = window.lpw || {};

    function Invest() {
        var $this = this;
        this.investMap = new window.lpw.Map(
            '#invest-map',
            'invest',
            new window.lpw.AutoComplete(null, null, 'invest', this)
        );
        this.setTotalCounter = function(count) {
            if(!_.isNumber(count)) {
                count = LpData.totalInvest;
            }
          $('#total-counter').text(count);
        };

        this.setEventListeners();
    }
    Invest.prototype.setEventListeners = function() {
      var $this = this;

    };
    Invest.prototype.onClusterSelect = function(data) {
        var title = $('<h3 class="location-title">' +
            '<span class="location-title-wrapper">' +
            '<span>' + data.text + '</span>&nbsp;<span class="tag-remove text-red"></span>' +
            '</span>' +
            '</h3>');
        title.insertAfter('.invest-title');

        $('html, body').stop().animate({
            scrollTop: 0
        }, 1000);
        if (data.location) {
            this.getSubTypes(data.location)
                .done(this.getSubTypesSuccess.bind(this))
                .fail(this.getSubTypesError.bind(this));
        }
    };
    Invest.prototype.getSubTypes = function(location) {
        var data = {
            action: 'do_ajax',
            fn: 'get_subtypes',
            subtype_parent_id: 3
        };
        if(_.isObject(location)) {
            _.forEach(location, function (v, k) {
                data[k] = v;
            });
        }
        return $.ajax({
            url: LpData.ajaxUrl,
            method: 'post',
            data: data
        });
    };
    Invest.prototype.getSubTypesSuccess = function(answer) {
         console.log(answer);
    };
    Invest.prototype.getSubTypesError = function(error) {
        console.debug('getSTError', error.responseText);
    };
    window.lpw.Invest = Invest;
})(jQuery);
