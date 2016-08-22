<?php
$api_url = get_field('api_url', 'option');
$token = get_field('api_key', 'option');
if(!$api_url || !$token) {
	die('No API data set');
}
$query = (isset($_GET['query']) && !empty($_GET['query'])) ? $_GET['query'] : false;
if($query) {
	$url = $api_url . '/suggest?q=' . urlencode($query);
	$curl_options = [
		CURLOPT_URL => $url,
		CURLOPT_HTTPHEADER => [
			'Authorization: Token token=' . $token
		],
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HEADER => true,
		CURLOPT_VERBOSE => true
	];
	$ch = curl_init();

	curl_setopt_array($ch, $curl_options);
	$resp = curl_exec($ch);
	if(!$resp) {
		curl_close($ch);
		die("No connection to API");
	}

	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($resp, 0, $header_size);
	$body = substr($resp, $header_size);

	curl_close($ch);
	if(!LP_ObjectList::isJson($body)) {
		die("Service returns wrong format");
	}
	echo $body;
}
die();