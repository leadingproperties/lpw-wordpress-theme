<?php

add_action('wp_head', 'lpw_custom_styles');

function lpw_custom_styles() {
	$ptBg = get_field('page_title_bg', 'option');
	$ptColor = get_field('page_title_color', 'option');
	$customCss = get_field('custom_css', 'option');
	echo '<style type="text/css">';
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

