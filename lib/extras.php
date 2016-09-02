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
                     is_page_template('page-object.php') ||
                     is_page_template('page-sharer.php') ||
                     is_page_template('page-sharer-rent.php')
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
	} elseif(is_page_template('page-sharer.php')) {
		$classes[] = 'page-sharer';
	} elseif(is_page_template('page-sharer-rent.php')) {
		$classes[] = 'page-sharer-rent';
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
	    $sp_class = (is_page_template('page-buy.php')) ? ' active' : '';
	    $rp_class = (is_page_template('page-rent.php')) ? ' active' : '';


	    if($args->container) {
		 //   $return .= '<li><a class="menu-link' . $sp_class . '" href="' . esc_url(get_field('sale', 'option')) . '">' . __('Buy', 'leadingprops');
		 //   $return .= ' <sup class="text-red">' . $lp_settings['counters']['for_sale'] . '</sup>';

		 //   $return .= '</a></li>';
		 //   $return .= '<li><a class="menu-link' . $rp_class . '" href="' . esc_url(get_field('rent', 'option')) . '"">' . __('Rent', 'leadingprops');
		 //   $return .= '  <sup class="text-red">' . $lp_settings['counters']['for_rent'] . '</sup></a></li>';
		    $return .= $items;
	    } else {
		   // $return .= '<li><a class="menu-link' . $sp_class . '" href="' . esc_url(home_url('/')) . '">' . __('Buy', 'leadingprops') . ' <sup class="text-red">' . $lp_settings['counters']['for_sale'] . '</sup></a></li>';
		   // $return .= '<li><a class="menu-link' . $rp_class . '" href="#">' . __('Rent', 'leadingprops') . '  <sup class="text-red">' . $lp_settings['counters']['for_rent'] . '</sup></a></li>';
		    $return .= $items;
		    $return .= '<li class="divider"></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-region" data-toggle="modal" data-target="#map-modal">' . __('alerts:show_map_tooltip', 'leadingprops') . '</a></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-favorites" href="' . $lp_settings['favorites'] . '">' . __('menu:favorites', 'leadingprops') . ' <sup class="text-red"></sup></a></li>';
		    $return .= '<li><a class="menu-link menu-icon menu-offmarket half-opaque" data-type="off_market">' . __('menu:offmarket', 'leadingprops') . ' <sup class="text-red"></sup></a></li>';
		    $return .= '<li class="divider"></li>';
		    if($lp_settings['contact_phone']) {
			    $return .= '<li><a class="menu-link menu-icon menu-phone">' . $lp_settings['contact_phone'] . '</a></li>';
		    }
		    if($lp_settings['contact_email']) {
			    $return .= '<li><a class="menu-link menu-icon menu-email" href="mailto:' . $lp_settings['contact_email'] . '">' . $lp_settings['contact_email'] . '</a></li>';
		    }
	    }
    } elseif($args->theme_location === 'footer_navigation') {
	   // $return .= '<li><a href="' . esc_url(home_url('/')) . '">' . __('Buy', 'leadingprops') . '</a></li>';
	   // $return .= '<li><a href="#">' . __('Rent', 'leadingprops') . '</a></li>';
	    $return .= $items;
    }
    return $return;
}

