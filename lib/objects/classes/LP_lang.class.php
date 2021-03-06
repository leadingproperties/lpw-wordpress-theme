<?php

class LP_lang {
	/**
	 * Available Languages
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	/*public $languages = [
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
	]; */

	public function __construct() {
		add_action('lang_panel', [&$this, 'lang_panel_html']);
	}

	public function lang_panel_html() {
		global $q_config,  $objects_obj, $lp_settings;
		$url = (is_404()) ? get_option('home') : '';
		if(is_page_template('page-object.php')) {
			$url = $lp_settings['property_page'];
		}

		$output = '<section id="lang-panel" class="section-lang-lists collapse language-chooser">'.PHP_EOL;
		$output .= '<div class="lang-panel-row container">'.PHP_EOL;
		$output .= '<div class="row">'.PHP_EOL;

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(is_plugin_active('qtranslate-x/qtranslate.php')) {

			foreach(qtranxf_getSortedLanguages() as $language) {
				$href = $url;
				if(is_page_template('page-object.php') && (isset($objects_obj->slugs->$language) || isset($objects_obj->rent_slugs))) {
					if(isset($objects_obj->slug_type)) {
						if($objects_obj->slug_type === 'PropertyObject') {
							$href .= $objects_obj->slugs->$language;
						} elseif($objects_obj->slug_type === 'PropertyRent') {
							$href .= $objects_obj->rent_slugs->$language;
						}
					}

				}
				$classes = ['lang-'.$language];
				$alt = $q_config['language_name'][$language].' ('.$language.')';
				$output .= '<div class="lang-option-wrapper">'.PHP_EOL;
				$output .= '<a href="' . qtranxf_convertURL( $href, $language, false, true ) . '"';
				$output .= ' hreflang="'.$language.'"';
				$output .= ' title="'.$alt.'"';
				$output .= '><span class="'. implode(' ', $classes) . '"></span>' . $q_config['language_name'][$language] . '</a>';
				$output .= '</div>' .PHP_EOL;
			}
		}
		$output .= '</div>'.PHP_EOL;
		$output .= '</div>'.PHP_EOL;
		$output .= '</section>'.PHP_EOL;

		echo $output;
	}

}

$lang = new LP_lang();