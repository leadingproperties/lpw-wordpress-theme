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
          $('#total-counter').text(count);
        };

        this.setEventListeners();
    }
    Invest.prototype.setEventListeners = function() {
        var $this = this;
        $('body').on('click.lprop', '.tag-remove', function(ev) {
            ev.preventDefault();
            $this.investMap.mapReset();
            $this.onClusterSelect(null, true);
        });
        $('.btn-map-open').on('click', function(ev) {
            $this.resizeMap(ev, $this);
        });
    };
    Invest.prototype.onClusterSelect = function(data, remove) {
        var locations = (data && data.location) ? data.location : null;
        if(remove) {
            $('.location-title').remove();
        } else {
            var locationTitle = $('.location-title-wrapper');
            if (locationTitle.length > 0) {
                locationTitle.html('<span>' + data.text + '</span>&nbsp;<span class="tag-remove text-red"></span>');
            } else {
                var title = $('<h3 class="location-title">' +
                    '<span class="location-title-wrapper">' +
                    '<span>' + data.text + '</span>&nbsp;<span class="tag-remove text-red"></span>' +
                    '</span>' +
                    '</h3>');
                title.insertAfter('.invest-title');
            }
            $('html, body').stop().animate({
                scrollTop: 0
            }, 1000);
        }
        this.getSubTypes(locations)
            .done(this.getSubTypesSuccess.bind(this))
            .fail(this.getSubTypesError.bind(this));

    };
    Invest.prototype.getSubTypes = function(location) {
        var data = {
            action: 'do_ajax',
            fn: 'get_subtypes',
            subtype_parent_id: 3
        };
        if(location) {
            if(!location.location_shape && location.location_point) {
                data.location_point = location.location_point;
            } else if(location.location_shape) {
                data.location_shape = location.location_shape;
            }
        }
        return $.ajax({
            url: LpData.ajaxUrl,
            method: 'post',
            dataType: 'json',
            data: data
        });
    };
    Invest.prototype.getSubTypesSuccess = function(data) {
        var html = '',
            answer = [],
            rawTypesArray = data.counters,
            tagsList = $('.comm-tags'),
            TypesOrder = [
                'hotel',
                'residential',
                'office_building',
                'retail',
                'shopping_centre',
                'cbr',
                'mixed',
                'other'
            ];

        _.forEach(TypesOrder, function(typeName){
            var target = _.find(rawTypesArray, ['name', typeName]);
            if(target && target.count){
                answer.push(target);
            }
        });

        this.setTotalCounter(data.total);
        _.forEach(answer, function(value) {
            html += '<li><span>' + value.title + ' <sup class="text-red">' + value.count + '</sup></span></li>';
        });
        if(html !== '') {
            if(tagsList.length > 0 ) {
                tagsList.html(html);
            } else {
                $('<ul class="comm-tags">' + html + '</ul>').appendTo('.comm-header');
            }
        } else {
            tagsList.remove();
        }

        this.tagWrap.html(html);
    };
    Invest.prototype.getSubTypesError = function(error) {
        console.debug('getSTError', error.responseText);
    };
    Invest.prototype.resizeMap = function(ev, $this) {
        ev.preventDefault();
        var maxHeight = 1000,
            map = $('#invest-map'),
            mapOffset = map.offset().top,
            mapCenter = $this.investMap.map.getCenter(),
            windowHeight = $(window).height(),
            btn = $(ev.currentTarget),
            action = btn.data('action'),
            normalHeight = 255,
            height = windowHeight < maxHeight ? windowHeight : maxHeight;
        if(action === 'open') {
            btn.addClass('opened').data('action', 'close');
            map.animate({height:height}, 600, 'linear', function() {
                google.maps.event.trigger($this.investMap.map, "resize");
                $this.investMap.map.setCenter(mapCenter);
            });
            $('html, body').stop().animate({
                scrollTop: mapOffset
            }, 600);
        } else {
            btn.removeClass('opened').data('action', 'open');
            map.css('height', normalHeight);
            google.maps.event.trigger($this.investMap.map, "resize");
            $this.investMap.map.setCenter(mapCenter);
        }
    };
    window.lpw.Invest = Invest;
})(jQuery);
