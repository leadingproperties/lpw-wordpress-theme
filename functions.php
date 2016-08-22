<?php
use Lprop\Tags;
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/customizer.php', // Theme customizer
  'lib/nav.php',         //Walker nav
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
$lp_settings['logo'] = s3_logo_path(get_field('logo', 'option'));
$lp_settings['contact_phone'] = get_field('contact_phone', 'option');
$lp_settings['contact_email'] = get_field('contact_email', 'option');
$lp_settings['favorites'] = esc_url(get_field('sale_favorites', 'option'));
$lp_settings['favorites_rent'] = esc_url(get_field('rent_favorites', 'option'));
$lp_settings['off-market'] = esc_url(home_url('/off-market/'));
$lp_settings['use_shortener'] = get_field('use_google_shortener', 'option');
$lp_settings['site_title'] = get_bloginfo('name');
$lp_settings['sale_page'] = get_field('sale', 'option');
$lp_settings['rent_page'] = get_field('rent', 'option');
$lp_settings['property_page_id'] = get_field('single_object', 'option');
$lp_settings['property_page'] = get_page_link($lp_settings['property_page_id']);
 //echo get_post_field('post_name', $objectPageId);

$objects = new LP_ObjectList();

$lp_settings['object_info'] = $objects->get_objects_info();


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
	if(is_front_page() || Tags\is_favorites()) {
		$return = ' <nav class="floating-bar" role="navigation"><div class="container">
        <ul>';
		if ( !Tags\is_favorites() ) {
			$return .= '<li><a href="#" class="search-link"></a></li>
			            <li><a href="' . $lp_settings['favorites'] . '" class="favorites-link"><sup></sup></a></li>
			            <li><a href="#" class="off-market-link" data-toggle="modal" data-target=".offmarket-request"><sup></sup></a></li>';
		}
		$return .= '<li><a href="#" class="request-link" data-toggle="modal" data-target=".contact-modal"></a></li>
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
  function s3_logo_path ( $image_id ) {
    $s3meta = get_post_meta( get_field('logo', 'option'), 'amazonS3_info', true );
    if ($s3meta == ''){
      return wp_get_attachment_image_url( $image_id, 'logo');
    }
    else {
      return 'http://'.$s3meta['bucket'].'.s3.amazonaws.com/'.$s3meta['key'];
    }
  }
