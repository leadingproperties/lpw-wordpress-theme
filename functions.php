<?php
use Lprop\Tags;
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/customizer.php', // Theme customizer
  'lib/nav.php',         //Walker nav
  'lib/blog.php',       //Blog functions
  'lib/conditional_tags.php',
  'lib/objects/objects.php',
  'lib/acf/acf.php',
  'lib/acf-settings.php',
  'lib/ajax/ajax-handlers.php'
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

/* Global Settings */
global $lp_settings;
$lp_settings = [
	'contact_phone' => get_field('contact_phone', 'option'),
	'contact_email' => get_field('contact_email', 'option'),
	'favorites' => esc_url(get_field('sale_favorites', 'option')),
	'favorites_rent' => esc_url(get_field('rent_favorites', 'option')),
	'off-market' => esc_url(home_url('/off-market/')),
	'use_shortener' => get_field('use_google_shortener', 'option'),
	'site_title' => get_bloginfo('name'),
	'sale_page' => get_field('sale', 'option'),
	'rent_page' => get_field('rent', 'option'),
	'property_page_id' => get_field('single_object', 'option'),
	'lang' => lpw_get_current_lang()
];
$lp_settings['property_page'] = get_page_link($lp_settings['property_page_id']);
 //echo get_post_field('post_name', $objectPageId);

$objects = new LP_ObjectList();
$counters = $objects->get_global_counters();
$lp_settings['counters'] = [
	'for_sale' => ($counters['global_counters']['for_sale']) ? $counters['global_counters']['for_sale'] : '',
	'for_rent' => ($counters['global_counters']['for_rent']) ? $counters['global_counters']['for_rent'] : '',
	'commercial' => ($counters['global_counters']['commercial']) ? $counters['global_counters']['commercial'] : ''
];

function lpw_get_current_lang() {
	if(function_exists('qtranxf_getLanguage')) {
		return qtranxf_getLanguage();
	} else {
		return 'en';
	}
}

function get_social_links() {
	$fb_link = get_field('facebook_link', 'option');
	$tw_link = get_field('twitter_link', 'option');
	$ln_link = get_field('linkedin_link', 'option');
	$gp_link = get_field('google_plus_link', 'option');
	$inst_link = get_field('instagram_link', 'option');
	$return = '';
	if($fb_link || $tw_link || $ln_link || $gp_link || $inst_link) :
		$return .= '<ul>';
		if($fb_link) {
			$return .= '<li><a href="' . $fb_link . '" target="_blank" class="soc-icon fb-icon"></a></li>';
		}
		if($tw_link) {
			$return .= '<li><a href="' . $tw_link . '" target="_blank" class="soc-icon twitter-icon"></a></li>';
		}
		if($ln_link) {
			$return .= '<li><a href="' . $ln_link . '" target="_blank" class="soc-icon ln-icon"></a></li>';
		}
		if($gp_link) {
			$return .= '<li><a href="' . $gp_link . '" target="_blank" class="soc-icon gplus-icon"></a></li>';
		}
		if($inst_link) {
			$return .= '<li><a href="' . $inst_link . '" target="_blank" class="soc-icon instagram-icon"></a></li>';
		}
	 endif;
	return $return;
}

function get_floating_bar() {
	global $lp_settings;
	$return = '';
	if(is_page_template('page-buy.php') || is_page_template('page-buy.rent') || is_page_template('page-favorites.php') || is_page_template('page-favorites-rent.php') ) {
		$return = ' <nav class="floating-bar" role="navigation"><div class="container">
        <ul>';
		if ( !(is_page_template('page-favorites.php') || is_page_template('page-favorites-rent.php')) ) {
			$return .= '<li><a href="#" class="search-link"></a></li>
			            <li><a href="' . $lp_settings['favorites'] . '" class="favorites-link"><sup></sup></a></li>
			            <li><a href="#" class="off-market-link half-opaque" data-type="off_market"><sup></sup></a></li>';
		}
		$return .= '<li><a href="#" class="request-link" data-toggle="modal" data-type="default" data-target=".contact-modal"></a></li>
            <li><a href="#" class="to-top-link"></a></li>
        </ul></div>
    </nav>';
	}
	echo $return;
}

add_action('wp_footer', 'get_floating_bar', 5);

/**
 *  S3 Logo path
 */
  function lwp_get_image ( $id, $size = 'thumbnail' ) {
    $s3meta = get_post_meta( $id, 'amazonS3_info', true );
    if ($s3meta == ''){
      return wp_get_attachment_image_url( $id, $size);
    }
    else {
      return 'http://'.$s3meta['bucket'].'.s3.amazonaws.com/'.$s3meta['key'];
    }
  }

/**
 * Add CF modals
 */
function add_cf_modals() {
	if(is_page_template('page-buy.php') || is_page_template('page-rent.php') || is_page_template('page-favorites.php') || is_page_template('page-favorites-rent.php') || is_page_template('page-object.php')) {
		get_template_part('templates/form', 'contact');
		get_template_part('templates/form', 'request');
		get_template_part('templates/form', 'offmarket');
	}

}
add_action('wp_footer', 'add_cf_modals');