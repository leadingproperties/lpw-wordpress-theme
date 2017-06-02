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

add_action('init', __NAMESPACE__ . '\\lp_rewrite_rule', 10);
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
  $ver = "3.3.0";
	  //TODO Check why wp_get_theme()->get('Version'); not working
    if(!is_admin()) {
        wp_enqueue_style( 'lprop/css', Assets\asset_path( 'styles/main.css' ), false, $ver );

        if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
        if ( is_page_template( 'page-buy.php' ) || is_page_template( 'page-rent.php' ) || is_page_template( 'page-invest.php' ) || is_page_template( 'page-location-buy.php' ) || is_page_template( 'page-location-rent.php' )) {

            wp_enqueue_script( 'google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $lp_settings['google_api_key'] . '&libraries=places&language=en', null, true );
            wp_enqueue_script( 'js-marker-clusterer', Assets\asset_path( '/scripts/js-marker-clusterer.js' ), [
                'jquery',
                'google-map'
            ], null, true );
            wp_register_script( 'lprop/js', Assets\asset_path( 'scripts/main.js' ), [
                'jquery',
                'lodash',
                'google-map',
                'js-marker-clusterer'
            ], $ver, true );
        } else {
            wp_register_script( 'lprop/js', Assets\asset_path( 'scripts/main.js' ), [
                'jquery',
                'lodash'
            ], $ver, true );
        }
        wp_enqueue_script( 'lodash', Assets\asset_path( 'scripts/lodash.js' ), [ ], null, true );
        $count_posts          = wp_count_posts( 'post' );
        $data                 = [
            'siteTitle'    => $lp_settings['site_title'],
            'homeUrl'      => home_url( '/' ),
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'useShortener' => $lp_settings['use_shortener'],
            'salePage'     => $lp_settings['sale_page'],
            'rentPage'     => $lp_settings['rent_page'],
            'propertyPage' => $lp_settings['property_page'],
            'saleSharer'   => $lp_settings['sale_share'],
            'rentSharer'   => $lp_settings['rent_share'],
            'totalPost'    => $count_posts->publish,
            'perPage'      => get_option( 'posts_per_page' ),
            'lang'         => $lp_settings['lang'],
            'totalSale'    => $lp_settings['counters']['for_sale'],
            'totalRent'    => $lp_settings['counters']['for_rent'],
            'totalLongRent'    => $lp_settings['counters']['long_rent'],
            'totalShortRent'    => $lp_settings['counters']['short_rent'],
            'totalInvest'  => $lp_settings['counters']['commercial'],
	        'currency_id'   => $lp_settings['currency_id']
        ];
        $data['totalObjects'] = ( is_page_template( 'page-buy.php' ) ) ? $lp_settings['counters']['for_sale'] : $lp_settings['counters']['long_rent'];
        if ( ( is_page_template( 'page-sharer.php' ) || is_page_template( 'page-sharer-rent.php' ) ) && isset( $_GET['ids'] ) ) {
            $data['ids'] = explode( '.', $_GET['ids'] );
	        $data['totalObjects'] = count($_GET['ids']);
        }
        if ( is_tag() ) {
            $tag_id            = get_query_var( 'tag_id' );
            $tag               = get_tags( [ 'include' => $tag_id ] );
            $data['tag']       = $tag_id;
            $data['totalPost'] = $tag[0]->count;
        }
        if ( is_page_template( 'page-buy.php' ) ) {
            if ( 1 == lwp_option( 'use_default' ) ) {
                $data['defaultLocation']   = true;
                $data['saleDefault']       = lwp_option( 'sale_location' );
                $data['saleDefaultCoords'] = json_decode( lwp_option( 'sale_location_geodata' ) );
            }
        }
        if ( is_page_template( 'page-rent.php' ) ) {
        	//Check if there are rent filter parameters
	        $filter = getParametersByName('filter');

	        $data['rentSubCat'] =  ($filter && isset($filter['short_rent']) && !empty($filter['short_rent'])) ? 'short_rent' : 'long_rent';

            if ( 1 == lwp_option( 'use_default_rent' ) ) {
                $data['defaultLocation']   = true;
                $data['rentDefault']       = lwp_option( 'rent_location' );
                $data['rentDefaultCoords'] = json_decode( lwp_option( 'rent_location_geodata' ) );
            }
        }
        if( is_page_template('page-location-buy.php') || is_page_template('page-location-rent.php')) {
	        $data['isLocationPage']   = true;
	        $data['defaultCoords'] = json_decode( get_field('geodata', get_queried_object_id()) );
	        $data['defaultGeoTitle'] = get_field('location', get_queried_object_id());
	        $data['propertyType']  = get_field('property_type', get_queried_object_id());

        }
        if( is_page_template( 'page-rent.php' ) || is_page_template('page-location-rent.php')) {
	        $data['filterPeriod'] = [
	            [
					'id' => 'day',
		            'text'  => __('s_object:rent:day', 'leadingprops')
	            ],
		        [
			        'id' => 'week',
			        'text'  => __('s_object:rent:week', 'leadingprops')
		        ],
		        [
			        'id' => 'month',
			        'text'  => __('s_object:rent:month', 'leadingprops')
		        ]
	        ];
        }
        wp_localize_script( 'lprop/js', 'LpData', $data );
        wp_enqueue_script( 'lprop/js' );
    }
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

// Generate pages on theme activation
// @howtwizer
// 2.09.2016


add_action('after_switch_theme', __NAMESPACE__ . '\\create_initial_pages');
add_action('after_switch_theme', __NAMESPACE__ . '\\set_home_page');

function create_initial_pages() {
	$pages = array(
	     // Page Title and URL (a blank space will end up becomeing a dash "-")
	    'Buy' => array(
	        // Page Content     // Template to use (if left blank the default template will be used)
	        'template' => 'page-buy.php',
		    'content'  => ''
	    ),

	    'Rent' => array(
	        'template' => 'page-rent.php',
		    'content'   => ''
	    ),

	    'Favorites Sale' => array(
	        'template'  => 'page-favorites.php',
	        'content'   => 'Favorites Sale page template. Please do not delete this page!'
	    ),

	    'Favorites Rent' => array(
	        'template'  => 'page-favorites-rent.php',
	        'content'   => 'Favorites Rent page template. Please do not delete this page!'
	    ),

	    'Single property' => array(
		    'template'  => 'page-object.php',
	        'content'   => 'Single property page template. Please do not delete this page!'
	    ),

	    'Buy share' => array(
		    'template'  => 'page-sharer.php',
	        'content'   => 'Buy share page template. Please do not delete this page!'
	    ),

	    'Rent share' => array(
		    'template'  => 'page-sharer-rent.php',
	        'content'   => 'Rent share page template. Please do not delete this page!'
	    ),

	    'Invest' => array(
		    'template'  => 'page-invest.php',
	        'content'   => 'Invest page template. Please do not delete this page!'
	    )

	);
	foreach($pages as $page_url_title => $page_meta) {
	        $id = get_page_by_title($page_url_title);

		$page = array(
			'post_type'   => 'page',
			'post_title'  => $page_url_title,
			'post_name'   => $page_url_title,
			'post_status' => 'publish',
			'post_content' => $page_meta['content'],
			'post_author' => 1,
			'post_parent' => ''
		);
		if(!isset($id->ID)){
			$new_page_id = wp_insert_post($page);
			switch ($page_url_title) {
				case 'Buy':
					update_field('sale', $new_page_id, 'option');
					break;
				case 'Rent':
					update_field('rent', $new_page_id, 'option');
					break;
				case 'Favorites Sale':
					update_field('sale_favorites', $new_page_id, 'option');
					break;
				case 'Favorites Rent':
					update_field('rent_favorites', $new_page_id, 'option');
					break;
				case 'Single property':
					update_field('single_object', $new_page_id, 'option');
					break;
				case 'Buy share':
					update_field('sale_share', $new_page_id, 'option');
					break;
				case 'Rent share':
					update_field('rent_share', $new_page_id, 'option');
					break;
				default:
			}

			if(isset($page_meta['template']) && !empty($page_meta['template'])){
				update_post_meta($new_page_id, '_wp_page_template', $page_meta['template']);
			}
		}
	  };
}
// XXX -> Generate pages on activation end
//
// Use a static front page
function set_home_page(){
  $buy = get_page_by_title( 'Buy' );
   if(isset($buy->ID)){
    update_option( 'show_on_front', 'page' );
    update_option( 'page_on_front', $buy->ID );
  }
}

