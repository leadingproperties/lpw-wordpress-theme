(function($) {
    var sale = new window.lpw.AutoComplete('#sale_location', '#sale_location_geodata', setData),
        rent = new window.lpw.AutoComplete('#rent_location', '#rent_location_geodata', setData);
    function setData(data) {
        console.log(data);
    }
})(jQuery);