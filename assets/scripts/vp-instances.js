(function($){
	var autoComplete = new window.lpw.AutoComplete(
		'http://localhost:8000/fixtures/autocomplete-answer-api.json',
		'#sp-search'
	);

	var lpwGoogleMap = new window.lpw.Map(
		'http://localhost:8000/fixtures/geo-points-api.json',
		false,
		autoComplete
	);
})(jQuery);