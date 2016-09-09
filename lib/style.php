<?php

add_action('wp_head', 'lpw_custom_styles');

function lpw_custom_styles() {
	$ptBg = get_field('page_title_bg', 'option');
	$ptColor = get_field('page_title_color', 'option');
	$customCss = get_field('custom_css', 'option');
	echo '<style type="text/css" media="screen">';
	if($ptBg || $ptColor) {
		echo '.page-header {';
		if($ptBg) {
			echo 'background: ' . $ptBg . ';';
		}
		if($ptColor) {
			echo 'color: ' . $ptColor . ';';
		}
		echo '}';
		if($customCss) {
			echo $customCss;
		}
	}
	echo '</style>';
}

function hex2rgba($hex, $opacity) {
	$hex = str_replace("#", "", $hex);

	if(strlen($hex) == 3) {
		$r = hexdec(substr($hex,0,1).substr($hex,0,1));
		$g = hexdec(substr($hex,1,1).substr($hex,1,1));
		$b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
	}
	$rgb = array($r, $g, $b);
	//return implode(",", $rgb); // returns the rgb values separated by commas
	return $rgb; // returns an array with the rgb values
}