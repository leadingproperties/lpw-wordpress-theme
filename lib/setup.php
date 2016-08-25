<?php

namespace Lprop\Setup;

use Lprop\Assets;
use Lprop\Tags;

/**
 * Theme setup
 */
function setup() {

  // Make theme available for translation
  load_theme_textdomain('leadingprops', get_template_directory() . '/lang');

  // Enable plugins to manage the document title
  // http://codex.wordpress.org/Function_Reference/add_theme_support#Title_Tag
  add_theme_support('title-tag');

  // Register wp_nav_menu() menus
  // http://codex.wordpress.org/Function_Reference/register_nav_menus
  register_nav_menus([
    'primary_navigation' => __('Primary Navigation', 'leadingprops'),
    'footer_navigation' => __('Footer Navigation', 'leadingprops'),
  ]);

  // Enable post thumbnails
  // http://codex.wordpress.org/Post_Thumbnails
  // http://codex.wordpress.org/Function_Reference/set_post_thumbnail_size
  // http://codex.wordpress.org/Function_Reference/add_image_size
  add_theme_support('post-thumbnails');
  set_post_thumbnail_size(350, 205, true);
  add_image_size('featured', 700, 410, true);
  add_image_size('logo', 350, 90, true);
  add_image_size('office', 350, 210, true);
  add_image_size('agent', 350, 350, true);


  // Enable HTML5 markup support
  // http://codex.wordpress.org/Function_Reference/add_theme_support#HTML5
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

  // Use main stylesheet for visual editor
  // To add custom styles edit /assets/styles/layouts/_tinymce.scss
  add_editor_style(Assets\asset_path('styles/main.css'));
}
add_action('after_setup_theme', __NAMESPACE__ . '\\setup');

/**
 * Rewrite rule for single object page
 */

add_action('init', __NAMESPACE__ . '\\lp_rewrite_rule');
function lp_rewrite_rule(){
	global $lp_settings;

	add_rewrite_rule( '^([^/]*)/?' . get_post_field('post_name', $lp_settings['property_page_id']) . '/([^/]*)/?', 'index.php?page_id=' . $lp_settings['property_page_id'] . '&object_slug=$matches[2]', 'top' );
	flush_rewrite_rules();
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'object_slug';
		return $vars;
	} );
}


/**
 * Theme assets
 */
function assets() {
  global $lp_settings;
  wp_enqueue_style('sage/css', Assets\asset_path('styles/main.css'), false, null);

  if (is_single() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
  if(is_page_template('page-buy.php') || is_page_template('page-rent.php')) {
	  /*TODO: Change API key*/
	 wp_enqueue_script('google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyB9AMFYWn5z8QYptnbetxXckrldFpsZyGA&libraries=places&language=en', null, true);
      wp_enqueue_script('js-marker-clusterer', Assets\asset_path('/scripts/js-marker-clusterer.js'), ['jquery', 'google-map'], null, true);
	 wp_register_script('lprop/js', Assets\asset_path('scripts/main.js'), ['jquery', 'lodash', 'google-map', 'js-marker-clusterer'], null, true);
  } else {
	  wp_register_script('lprop/js', Assets\asset_path('scripts/main.js'), ['jquery', 'lodash'], null, true);
  }
  wp_enqueue_script('lodash', Assets\asset_path('scripts/lodash.js'), [], null, true);
  $count_posts = wp_count_posts('post');
  $data = [
	  'siteTitle'   => $lp_settings['site_title'],
	  'homeUrl' => home_url('/'),
	  'ajaxUrl' => admin_url('admin-ajax.php'),
	  'useShortener' => $lp_settings['use_shortener'],
	  'salePage'    => $lp_settings['sale_page'],
	  'rentPage'    => $lp_settings['rent_page'],
	  'propertyPage'    => $lp_settings['property_page'],
	  'totalPost' => $count_posts->publish,
	  'perPage' => get_option('posts_per_page'),
	  'lang'    => $lp_settings['lang'],
	  'totalSale' => $lp_settings['counters']['for_sale'],
	  'totalRent'   => $lp_settings['counters']['for_rent']
  ];
	$data['totalObjects'] = (is_page_template('page-buy.php')) ? $lp_settings['counters']['for_sale'] : $lp_settings['counters']['for_rent'];
	if(is_tag()) {
		$tag_id = get_query_var('tag_id');
		$tag = get_tags( ['include' => $tag_id] );
		$data['tag'] = $tag_id;
		$data['totalPost'] = $tag[0]->count;
	}
  wp_localize_script('lprop/js', 'LpData', $data);
  wp_enqueue_script('lprop/js');
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);

add_action('init', __NAMESPACE__ . '\\lp_activation');
function lp_activation() {
  if (get_option('permalink_structure') !== '/%postname%/') {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    flush_rewrite_rules();
  }
}

