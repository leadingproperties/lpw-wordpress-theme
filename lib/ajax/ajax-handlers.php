<?php

add_action('wp_ajax_do_ajax', 'ajax_handler');
add_action('wp_ajax_nopriv_do_ajax', 'ajax_handler');

function ajax_handler() {

	switch($_REQUEST['fn']) {
		case 'get_objects':
			$output = ajax_get_objects($_REQUEST);
			break;
		case 'get_shorten_url':
			$output = get_shorten_url($_REQUEST['url']);
			break;
		default :
			$output = 'No function specified, check your jQuery.ajax() call';
			break;
	}
	if ( is_array( $output ) ) {
		print_r( $output );
	} else {
		echo $output;
	}
	die;
}

function ajax_get_objects($args) {
	if(isset($args['fn'])) {
		unset($args['fn']);
	}
	if(isset($args['action'])) {
		unset($args['action']);
	}
	$objects = new LP_ObjectList($args);
	return $objects->get_json_objects();
}

function get_shorten_url($url) {
	$api_key = get_field('google_shortener_api', 'option');


	if( $api_key ) :
		$api_url = 'https://www.googleapis.com/urlshortener/v1/url?key=' . $api_key;
		$content = json_encode([
			'longUrl' => $url
		]);

		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
			["Content-type: application/json"]);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$response = curl_exec($curl);
		$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$body = substr($response, $header_size);
		curl_close($curl);

		echo $response;

		return $body;

	else :
		return json_encode(['error' => true, 'error_message' => 'No API key']);
	endif;
}