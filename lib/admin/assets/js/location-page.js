(function($) {
    function setAutocomlete() {

        var autocompleteInput = $('#default_location').find('input'),
            autocompleteInputId = '#' + autocompleteInput.attr('id'),
            dataInput = '#' + $('#default_location_geodata').find('input').attr('id'),
            autocomplete = new window.lpw.AutoComplete(autocompleteInput, dataInput, setData);
            autocompleteInput.attr('autocomplete', false);

    }
    function setData(data) {
        console.log(data);
    }

    $(document).ready(setAutocomlete);
    $( document ).ajaxComplete(function( event, xhr, settings ) {
        if(settings.data && ( settings.data.indexOf('acf') !== -1 ) && (settings.data.indexOf('page-location') !== -1)) {
            setAutocomlete();
        }

    });

})(jQuery);