<?php
function get_favorites_bar($is_bottom = false) {
	global $pos;
	$pos = $is_bottom;
	get_template_part('templates/favorites', 'bar');

}
