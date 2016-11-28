(function($){
    "use strict";

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
                    filterInp.price.max.val(undefined);
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
                filterInp.price.max.val(undefined);
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

    window.lpw = window.lpw || {};
    window.lpw.FilterMenu = FilterMenu;
})(jQuery);