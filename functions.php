<?php
use Lprop\Tags;
$sage_includes = [
  'lib/assets.php',    // Scripts and stylesheets
  'lib/extras.php',    // Custom functions
  'lib/setup.php',     // Theme setup
  'lib/titles.php',    // Page titles
  'lib/nav.php',         //Walker nav
  'lib/blog.php',       //Blog functions
  'lib/conditional_tags.php',
  'lib/style.php',
  'lib/custom-scripts.php',
  'lib/objects/objects.php',
  'lib/acf/acf.php',
  'lib/acf-settings.php',
  'lib/ajax/ajax-handlers.php',
  'lib/admin/default-location.php',
  'lib/theme-updates/config.php',
  'lib/required/config.php'
];

foreach ($sage_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'sage'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);

/* Global Settings */
function lpw_set_globals() {
	global $lp_settings;
	$lp_settings = [];
	if(!is_admin()) {
		$lp_settings = [
			'contact_phone'    => get_field( 'contact_phone', 'option' ),
			'contact_email'    => get_field( 'contact_email', 'option' ),
			'use_shortener'    => get_field( 'use_google_shortener', 'option' ),
			'site_title'       => get_bloginfo( 'name' ),
			'sale_share'       => ( get_field( 'sale_share', 'option' ) ) ? get_field( 'sale_share', 'option' ) : get_page_by_title( 'Buy share' )->guid,
			'rent_share'       => ( get_field( 'rent_share', 'option' ) ) ? get_field( 'rent_share', 'option' ) : get_page_by_title( 'Rent share' )->guid,
			'favorites'        => esc_url( ( get_field( 'sale_favorites', 'option' ) ) ? get_field( 'sale_favorites', 'option' ) : get_page_by_title( 'Favorites Sale' )->guid ),
			'favorites_rent'   => esc_url( ( get_field( 'rent_favorites', 'option' ) ) ? get_field( 'rent_favorites', 'option' ) : get_page_by_title( 'Favorites Rent' )->guid ),
			'lang'             => lpw_get_current_lang(),
			'currency_id'      =>  isset($_COOKIE['lpw_currency_id']) ? (int) $_COOKIE['lpw_currency_id'] : 1
		];


		if(!(defined( 'DOING_AJAX' ) && DOING_AJAX)) {
			$objects                 = new LP_ObjectList();
			$counters                = $objects->get_global_counters();
			$lp_settings['counters'] = [
				'for_sale'   => ( $counters['global_counters']['for_sale'] ) ? $counters['global_counters']['for_sale'] : '',
				'for_rent'   => ( $counters['global_counters']['for_rent'] ) ? $counters['global_counters']['for_rent'] : '',
				'commercial' => ( $counters['global_counters']['commercial'] ) ? $counters['global_counters']['commercial'] : '',
                'long_rent' => ( $counters['global_counters']['long_rent'] ) ? $counters['global_counters']['long_rent'] : '',
				'short_rent' => ( $counters['global_counters']['short_rent'] ) ? $counters['global_counters']['short_rent'] : ''
			];
		}
	}
	$lp_settings['google_api_key'] = ( get_field( 'google_api_key', 'option' ) ) ? get_field( 'google_api_key', 'option' ) : 'AIzaSyB9AMFYWn5z8QYptnbetxXckrldFpsZyGA';
	$lp_settings['property_page_id'] = ( get_field( 'single_object', 'option' ) ) ? get_field( 'single_object', 'option' ) : get_page_by_title( 'Single property' )->id;
	$property_page                = get_page_link( $lp_settings['property_page_id'] );
	$lp_settings['property_page'] = is_ssl() ? str_replace( 'http:', 'https:', $property_page ) : $property_page;
	$lp_settings['sale_page'] = ( get_field( 'sale', 'option' ) ) ? get_field( 'sale', 'option' ) : get_page_by_title( 'Buy' )->guid;
	$lp_settings['rent_page'] = ( get_field( 'rent', 'option' ) ) ? get_field( 'rent', 'option' ) : get_page_by_title( 'Rent' )->guid;


}

add_action('init', 'lpw_set_globals', 1);


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
		$return .= '</ul>';
	 endif;
	return $return;
}

function get_floating_bar() {
	global $lp_settings;
	$return = '';
	if(!is_404() ) {
		$return = ' <nav class="floating-bar" role="navigation"><div class="container">
        <ul>';
		if ( is_page_template('page-buy.php') || is_page_template('page-rent.php') ) {
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
	get_template_part('templates/form', 'contact');
	if(is_page_template('page-buy.php') || is_page_template('page-rent.php') || is_page_template('page-favorites.php') || is_page_template('page-favorites-rent.php') || is_page_template('page-object.php')) {
		get_template_part('templates/form', 'request');
		get_template_part('templates/form', 'offmarket');
	}

}
add_action('wp_footer', 'add_cf_modals');

/**
 * Function to detect LPW API pages
 */

function is_lpw_page() {
	return is_page_template('page-buy.php') ||
	       is_page_template('page-rent.php') ||
	       is_page_template('page-favorites.php') ||
	       is_page_template('page-favorites-rent.php') ||
	       is_page_template('page-object.php') ||
	       is_page_template('page-sharer.php') ||
	       is_page_template('page-sharer-rent.php') ||
		   is_page_template('page-location-buy.php') ||
		   is_page_template('page-location-rent.php');
}

add_action( 'init', 'lpw_excerpts_to_pages' );

function lpw_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
}

add_action( 'add_meta_boxes', array ( 'T5_Richtext_Excerpt', 'switch_boxes' ) );

/**
 * Replaces the default excerpt editor with TinyMCE.
 */
class T5_Richtext_Excerpt
{
	/**
	 * Replaces the meta boxes.
	 *
	 * @return void
	 */
	public static function switch_boxes()
	{
		if ( ! post_type_supports( $GLOBALS['post']->post_type, 'excerpt' ) )
		{
			return;
		}

		remove_meta_box(
			'postexcerpt' // ID
			,   ''            // Screen, empty to support all post types
			,   'normal'      // Context
		);

		add_meta_box(
			'postexcerpt2'     // Reusing just 'postexcerpt' doesn't work.
			,   __( 'Excerpt' )    // Title
			,   array ( __CLASS__, 'show' ) // Display function
			,   null              // Screen, we use all screens with meta boxes.
			,   'normal'          // Context
			,   'core'            // Priority
		);
	}

	/**
	 * Output for the meta box.
	 *
	 * @param  object $post
	 * @return void
	 */
	public static function show( $post )
	{
		?>
		<label class="screen-reader-text" for="excerpt"><?php
			_e( 'Excerpt' )
			?></label>
		<?php
		// We use the default name, 'excerpt', so we don’t have to care about
		// saving, other filters etc.
		wp_editor(
			self::unescape( $post->post_excerpt ),
			'excerpt',
			array (
				'textarea_rows' => 20,
				'media_buttons' => false,
				'teeny'         => false,
				'tinymce'       => TRUE
			)
		);
	}

	/**
	 * The excerpt is escaped usually. This breaks the HTML editor.
	 *
	 * @param  string $str
	 * @return string
	 */
	public static function unescape( $str ) {
		return str_replace(
			array ( '&lt;', '&gt;', '&quot;', '&amp;', '&nbsp;', '&amp;nbsp;' )
			,   array ( '<',    '>',    '"',      '&',     ' ', ' ' )
			,   $str
		);
	}
}

function getParametersByName($name) {
	if(isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) {
		return json_decode(stripcslashes(urldecode($_GET['filter'])),true);
	} else {
		return false;
	}
}