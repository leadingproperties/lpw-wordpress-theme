<?php

namespace Lprop\Extras;

use Lprop\Setup;
use Lprop\Tags;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_page() && !(is_page_template('page-buy.php') ||
                     is_page_template('page-rent.php') ||
                     is_page_template('page-favorites.php') ||
                     is_page_template('page-favorites-rent.php') ||
                     is_page_template('page-object.php')
	  )
  ) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
	  $classes[] = 'page-static';
    }
  }
	if(is_archive() || is_home()) {
		$classes[]  = 'blog-list';
	}
	if( is_page_template('page-buy.php') ) {
		$classes[] = 'page-sales';
	} elseif( is_page_template('page-rent.php') ) {
		$classes[] = 'page-rent';
	} elseif(is_page_template('page-favorites.php')) {
		$classes[] = 'page-favorites';
	} elseif(is_page_template('page-favorites-rent.php')) {
		$classes[] = 'page-favorites-rent';
	} elseif(is_page_template('page-object.php')) {
		$classes[] = 'page-single-object';
	}

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'leadingprops') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\\lp_menu_static_links', 10, 2 );

function lp_menu_static_links ( $items, $args ) {
	global $lp_settings;
	$return = '';
    if ($args->theme_location === 'primary_navigation') {


	    if($args->container) {
		    $return .= '<li><a class="menu-link" href="' . esc_url(get_field('sale', 'option')) . '">' . __('Buy', 'leadingprops');
		    $return .= ' <sup class="text-red"></sup>';

		    $return .= '</a></li>';
		    $return .= '<li><a class="menu-link" href="' . esc_url(get_field('rent', 'option')) . '"">' . __('Rent', 'leadingprops') . '  <sup class="text-red">0</sup></a></li>';
		    $return .= $items;
	    } else {
		    $return .= '<li><a class="menu-link" href="' . esc_url(home_url('/')) . '">' . __('Buy', 'leadingprops') . ' <sup class="text-red"></sup></a></li>';
		    $return .= '<li><a class="menu-link" href="#">' . __('Rent', 'leadingprops') . '  <sup class="text-red"></sup></a></li>';
		    $return .= $items;
		    $return .= '<li class="divider"></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-region" href="#">' . __('Select region', 'leadingprops') . '</a></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-favorites" href="' . $lp_settings['favorites'] . '">' . __('Favorites', 'leadingprops') . ' <sup class="text-red"></sup></a></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-offmarket" href="' . $lp_settings['off-market'] . '">' . __('Off-market', 'leadingprops') . ' <sup class="text-red"></sup></a></li>';
		    $return .= '<li class="divider"></li>';
	    }
    } elseif($args->theme_location === 'footer_navigation') {
	    $return .= '<li><a href="' . esc_url(home_url('/')) . '">' . __('Buy', 'leadingprops') . '</a></li>';
	    $return .= '<li><a href="#">' . __('Rent', 'leadingprops') . '</a></li>';
	    $return .= $items;
    }
    return $return;
}

