<?php

add_action('wp_head', 'lpw_header_scripts', 99);
function lpw_header_scripts() {
	the_field('header_scripts', 'option');
}
add_action('wp_footer', 'lpw_footer_scripts', 99);
function lpw_footer_scripts() {
	the_field('footer_scripts', 'option');
}