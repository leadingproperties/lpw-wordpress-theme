<?php
	namespace Lprop\Tags;

	function is_favorites() {
		return get_query_var('is_favorites_page');
	}
	function is_offmarket() {
		return get_query_var('is_offmarket');
	}