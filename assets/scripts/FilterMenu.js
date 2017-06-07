(function($){
    "use strict";

    /* Filter toggle */
    function FilterMenu(type, catogory, rent_category) {

        this.filterCurrency = $('#price-currency');
        this.rentLongBtn = $('#selector-rent-long');
        this.rentShortBtn = $('#selector-rent-short');
        this.filterPeriod = $('#price-period');
        this.rentCategory = rent_category;
        this.filterInp = {
            price: {
                min: $('#price-min'),
                max: $('#price-max'),
                currency: this.filterCurrency,
                period: this.filterPeriod
            },
            area:  {
                min: $('#area-min'),
                max: $('#area-max')
            },
            property_types: $('.property_type'),
            rooms: $('.filter-room'),
            hd_photos: $('#quality'),
            persons: $('#persons-max'),
            child_friendly: $('#child-friendly'),
            pets_allowed: $('#pets-allowed')

        };
        var $this = this,
            container = $('.sp-filters'),
            filterToggleBtn = $('.filter-toggle'),
            filterCloseBtn = $('.filter-close'),
            tooltipOpt = {
                template: '<div class="tooltip tooltip-search" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                delay: { "show": 200, "hide": 300 }
            };

        this.setValues = function(values) {
            if(values.price) {
                if(values.price.min) {
                    this.filterInp.price.min.val(values.price.min);
                } else {
                    this.filterInp.price.min.val(undefined);
                }
                if(values.price.max) {
                    this.filterInp.price.max.val(values.price.max);
                } else {
                    this.filterInp.price.max.val(undefined);
                }
                if(values.price.currency) {
                    this.filterInp.price.currency.val(values.price.currency).trigger("change");
                } else {
                    //this.filterInp.price.currency.val(1).trigger("change");
                }
                if(values.price.period) {
                    this.filterInp.price.period.val(values.price.period).trigger("change");
                } else {
                    this.filterInp.price.period.val("day").trigger("change");
                }
            } else {
                this.filterInp.price.min.val(undefined);
                this.filterInp.price.max.val(undefined);
                //this.filterInp.price.currency.val(1).trigger("change");
                this.filterInp.price.period.val("day").trigger("change");
            }
            if(values.area) {
                if(values.area.min) {
                    this.filterInp.area.min.val(values.area.min);
                } else {
                    this.filterInp.area.min.val(undefined);
                }
                if(values.area.max) {
                    this.filterInp.area.max.val(values.area.max);
                } else {
                    this.filterInp.area.max.val(undefined);
                }
            } else {
                this.filterInp.area.min.val(undefined);
                this.filterInp.area.max.val(undefined);
            }
            if(values.property_types && _.isArray(values.property_types)) {
                _.forEach(values.property_types, function(value) {
                    this.filterInp.property_types.filter(function() {
                        return this.value === value;
                    }).prop("checked", true);
                });
            } else {
                this.filterInp.property_types.prop("checked", false);
            }
            if(values.rooms && _.isArray(values.rooms)) {
                _.forEach(values.rooms, function(value) {
                    this.filterInp.rooms.filter(function() {
                        return this.value === value;
                    }).prop("checked", true);
                });
            } else {
                this.filterInp.rooms.prop("checked", false);
            }
            if(values.hd_photos) {
                this.filterInp.hd_photos.prop('checked', true);
            } else {
                this.filterInp.hd_photos.prop('checked', false);
            }
            if(values.persons) {
                this.filterInp.persons.val(values.persons);
            } else {
                this.filterInp.persons.val(undefined);
            }
            if(values.child_friendly) {
                this.filterInp.child_friendly.prop('checked', true);
            } else {
                this.filterInp.child_friendly.prop('checked', false);
            }
            if(values.pets_allowed) {
                this.filterInp.pets_allowed.prop('checked', true);
            } else {
                this.filterInp.pets_allowed.prop('checked', false);
            }

        };
        this.filterSorting = $('.sorting-select');
        this.filterForm = $('#filter-form');
        this.getValues = function() {
            var price = {
                    min: this.filterInp.price.min.val(),
                    max: this.filterInp.price.max.val()
                },
                area = {
                    min: this.filterInp.area.min.val(),
                    max: this.filterInp.area.max.val()
                },
                property_types = this.filterInp.property_types.filter(':checked').map(function() {
                    return this.value;
                }).get(),
                rooms = this.filterInp.rooms.filter(':checked').map(function() {
                    return this.value;
                }).get(),
                values = {};
            values.hd_photos = this.filterInp.hd_photos.is(':checked');
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
            values.price = {
                currency: this.filterInp.price.currency.val()
            };
            if(price.min || price.max) {
                if(price.min) {
                    values.price.min = price.min;
                }
                if(price.max) {
                    values.price.max = price.max;
                }
            }
            values.property_types = _.isEmpty(property_types) ? null : property_types;
            values.rooms = _.isEmpty(rooms) ? null : rooms;
            if( catogory === 'rent' ) {
                values.persons = this.filterInp.persons.val() || null;
                values.child_friendly = this.filterInp.child_friendly.is(':checked');
                values.pets_allowed = this.filterInp.pets_allowed.is(':checked');
                if(price.min || price.max) {
                    values.price.period = this.filterInp.price.period.val();
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

            //Fix select2 issue with empty value

            if(this.rentCategory === 'long_rent') {
                this.filterPeriod.val('month').trigger('change');
            }

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
                this.filterCurrency.select2({
                    minimumResultsForSearch: Infinity,
                    containerCssClass: "price-select",
                    dropdownCssClass: "price-select-dropdown"
                });

              this.periodFilterInit();

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

    FilterMenu.prototype.periodFilterInit = function(data) {
      var options = {
          minimumResultsForSearch: Infinity,
          containerCssClass: "period-select",
          dropdownCssClass: "period-select-dropdown",
          width: '105px'
      };
      if(data) {
          options.data = data;
          this.filterPeriod.empty();
      }
        this.filterPeriod.select2(options);
    };

    window.lpw = window.lpw || {};
    window.lpw.FilterMenu = FilterMenu;
})(jQuery);