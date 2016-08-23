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

	add_rewrite_rule( '^' . get_post_field('post_name', $lp_settings['property_page_id']) . '/([^/]*)/?', 'index.php?page_id=' . $lp_settings['property_page_id'] . '&object_slug=$matches[1]', 'top' );
	flush_rewrite_rules();
	add_filter( 'query_vars', function( $vars ){
		$vars[] = 'object_slug';
		return $vars;
	} );
}

/**
 * Register sidebars
 */
function widgets_init() {

  register_sidebar([
    'name'          => __('Footer', 'sage'),
    'id'            => 'sidebar-footer',
    'before_widget' => '<section class="widget %1$s %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ]);
}
add_action('widgets_init', __NAMESPACE__ . '\\widgets_init');

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
	  'perPage' => get_option('posts_per_page')
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

add_action('init', __NAMESPACE__ . '\\lp_rewrite_rules');

function lp_rewrite_rules() {
	/*$languages = [
		[
			'code' => 'en-US',
			'title' => 'English',
			'wrapper_class' => 'en',
			'url'   => ''
		],
		[
			'code' => 'fr-FR',
			'title' => 'Français',
			'wrapper_class' => 'fr',
			'url'   => ''
		],
		[
			'code' => 'de-DE',
			'title' => 'Deutsch',
			'wrapper_class' => 'de',
			'url'   => ''
		],
		[
			'code' => 'ru-RU',
			'title' => 'Русский',
			'wrapper_class' => 'ru',
			'url'   => ''
		],
		[
			'code' => 'cs-CZ',
			'title' => 'Čeština',
			'wrapper_class' => 'cs',
			'url'   => ''
		],
		[
			'code' => 'zh-hans',
			'title' => '简体中文',
			'wrapper_class' => 'zh',
			'url'   => ''
		],
		[
			'code' => 'da-DK',
			'title' => 'Dansk',
			'wrapper_class' => 'da',
			'url'   => ''
		],
		[
			'code' => 'nl-NL',
			'title' => 'Nederlands',
			'wrapper_class' => 'nl',
			'url'   => ''
		],
		[
			'code' => 'it-IT',
			'title' => 'Italiano',
			'wrapper_class' => 'it',
			'url'   => ''
		],
		[
			'code' => 'ja-JP',
			'title' => '日本語',
			'wrapper_class' => 'ja',
			'url'   => ''
		],
		[
			'code' => 'es-ES',
			'title' => 'Español',
			'wrapper_class' => 'es',
			'url'   => ''
		],
		[
			'code' => 'ar',
			'title' => 'العربية',
			'wrapper_class' => 'ar',
			'url'   => ''
		],
		[
			'code' => 'he-IL',
			'title' => 'עברית',
			'wrapper_class' => 'he',
			'url'   => ''
		],
		[
			'code' => '',
			'title' => 'हिन्दी',
			'wrapper_class' => 'hi',
			'url'   => 'hi_IN'
		],
		[
			'code' => 'ko-KR',
			'title' => '한국어',
			'wrapper_class' => 'ko',
			'url'   => ''
		],
		[
			'code' => 'nb-NO',
			'title' => 'Norwegian Bokmål',
			'wrapper_class' => 'nb',
			'url'   => ''
		],
		[
			'code' => 'pt-PT',
			'title' => 'Português',
			'wrapper_class' => 'pt',
			'url'   => ''
		],
		[
			'code' => 'sv-SE',
			'title' => 'Svenska',
			'wrapper_class' => 'sv',
			'url'   => ''
		],
		[
			'code' => 'th-TH',
			'title' => 'ไทย',
			'wrapper_class' => 'th',
			'url'   => ''
		],
		[
			'code' => 'tr-TR',
			'title' => 'Türkçe',
			'wrapper_class' => 'tr',
			'url'   => ''
		],
		[
			'code' => 'ur',
			'title' => 'Urdu',
			'wrapper_class' => 'ur',
			'url'   => ''
		],
		[
			'code' => 'vi-VN',
			'title' => 'Vietnamese',
			'wrapper_class' => 'vi',
			'url'   => ''
		],
		[
			'code' => 'bg-BG',
			'title' => 'Български език',
			'wrapper_class' => 'bg',
			'url'   => ''
		],
		[
			'code' => 'fi-FI',
			'title' => 'Suomi',
			'wrapper_class' => 'fi',
			'url'   => ''
		],
		[
			'code' => 'pl-PL',
			'title' => 'Język Polski',
			'wrapper_class' => 'pl',
			'url'   => ''
		],
		[
			'code' => 'uk-UA',
			'title' => 'Українська',
			'wrapper_class' => 'uk',
			'url'   => ''
		],
		[
			'code' => 'hr-HR',
			'title' => 'Hrvatski Jezik',
			'wrapper_class' => 'hr',
			'url'   => ''
		],
		[
			'code' => 'lv-LV',
			'title' => 'Latviešu Valoda',
			'wrapper_class' => 'lv',
			'url'   => ''
		],
		[
			'code' => 'et-EE',
			'title' => 'Eesti',
			'wrapper_class' => 'et',
			'url'   => ''
		],
		[
			'code' => 'hu-HU',
			'title' => 'Magyar',
			'wrapper_class' => 'hu',
			'url'   => ''
		],
		[
			'code' => 'ro-RO',
			'title' => 'Limba Română',
			'wrapper_class' => 'ro',
			'url'   => ''
		],
		[
			'code' => 'sr-RS',
			'title' => 'Српски језик',
			'wrapper_class' => 'sr',
			'url'   => ''
		],
		[
			'code' => 'is-IS',
			'title' => 'Íslenska',
			'wrapper_class' => 'is',
			'url'   => ''
		],
		[
			'code' => 'sk-SK',
			'title' => 'Slovenčina',
			'wrapper_class' => 'sk',
			'url'   => ''
		],
		[
			'code' => 'sl-SI',
			'title' => 'Slovenščina',
			'wrapper_class' => 'sl',
			'url'   => ''
		],
		[
			'code' => 'el-GR',
			'title' => 'ελληνικά',
			'wrapper_class' => 'el',
			'url'   => ''
		],
		[
			'code' => 'id-ID',
			'title' => 'Bahasa Indonesia',
			'wrapper_class' => 'ind',
			'url'   => ''
		],
		[
			'code' => 'tl-PH',
			'title' => 'Wikang Tagalog',
			'wrapper_class' => 'fil',
			'url'   => ''
		],
		[
			'code' => 'ca-ES',
			'title' => 'Català',
			'wrapper_class' => 'ca',
			'url'   => ''
		],
		[
			'code' => 'lt-LT',
			'title' => 'Lietuvių Kalba',
			'wrapper_class' => 'lt',
			'url'   => ''
		]
	];
	foreach($languages as $lang) {
		add_rewrite_rule('^'.$lang['wrapper_class'].'/?([^/]*)/?','index.php?lang='.$lang['code'],'top');
	}*/
	//add_rewrite_rule( '^object/([^/]*)/([^/]*)/([^/]*)/?', 'index.php?is_object=1&object_id=$matches[3]', 'top' );
	//add_rewrite_rule('^favorites?','index.php?is_favorites_page=1','top');
	//add_rewrite_rule('^off-market?','index.php?is_offmarket_page=1','top');
	flush_rewrite_rules();
}
//add_action('query_vars', __NAMESPACE__ . '\\lp_set_query_var');

function lp_set_query_var($vars) {
	array_push($vars, 'is_object', 'is_favorites_page', 'is_offmarket_page', 'object_id');
	return $vars;
}


//add_filter('template_include', __NAMESPACE__ . '\\lp_include_template', 1000, 1);

function lp_include_template($template){
	if(Tags\is_favorites()){
		$new_template = get_stylesheet_directory() . '/favorites.php';
		if(file_exists($new_template))
			$template = $new_template;
	}
	return $template;
}

function lp_redefine_locale($locale) {
	$lang = get_query_var('lang');

	if(!$lang) {
		$lang = 'en-US';
	}
	return $lang;
}
//add_filter('locale', __NAMESPACE__ . '\\lp_redefine_locale',10);