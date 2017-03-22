<?php
/**
 * Master theme class
 *
 * @package Bolts
 * @since 1.0
 */
class LP_Theme_Options {

	private $sections;
	private $checkboxes;
	private $settings;

	/**
	 * Construct
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// This will keep track of the checkbox options for the validate_settings function.
		$this->checkboxes = array();
		$this->settings = array();
		$this->get_settings();

		$this->sections['locations']      = __( 'Locations' );


		add_action( 'admin_menu', array( &$this, 'add_pages' ) );
		add_action( 'admin_init', array( &$this, 'register_settings' ) );

		add_action( 'admin_enqueue_scripts', [&$this, 'add_location_page_scripts'], 10, 1 );

		if ( ! get_option( 'lp_options' ) ) {
			$this->initialize_settings();
		}
	}

	/**
	 * Add options page
	 *
	 * @since 1.0
	 */
	public function add_pages() {

		$admin_page = add_menu_page( __( 'Default location' ), __( 'Default location' ), 'manage_options', 'lwp-options', array( &$this, 'display_page' ), 'dashicons-location' );

		add_action( 'admin_print_scripts-' . $admin_page, array( &$this, 'scripts' ) );
		add_action( 'admin_print_styles-' . $admin_page, array( &$this, 'styles' ) );

	}

	/**
	 * Create settings field
	 *
	 * @since 1.0
	 */
	public function create_setting( $args = array() ) {

		$defaults = array(
			'id'      => 'default_field',
			'title'   => __( '' ),
			'desc'    => __( 'This is a default description.' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'general',
			'choices' => array(),
			'class'   => ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;

		add_settings_field( $id, $title, array( $this, 'display_setting' ), 'lwp-options', $section, $field_args );
	}

	/**
	 * Display options page
	 *
	 * @since 1.0
	 */
	public function display_page() {

		echo '<div class="wrap">
	<h2>' . __( 'Default Locations' ) . '</h2>';

		if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true )
			echo '<div class="updated fade"><p>' . __( 'Locations updated.' ) . '</p></div>';

		echo '<form action="options.php" method="post">';

		settings_fields( 'lp_options' );
		echo '<div>';

		do_settings_sections( $_GET['page'] );

		echo '</div>';



		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __( 'Save Changes' ) . '" /></p></form>';

		echo '</div>';

	}


	/**
	 * Description for section
	 *
	 * @since 1.0
	 */
	public function display_section() {
	}

	/**
	 * HTML output for text field
	 *
	 * @since 1.0
	 */
	public function display_setting( $args = array() ) {

		extract( $args );

		$options = get_option( 'lp_options' );

		if ( ! isset( $options[$id] ) && $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( ! isset( $options[$id] ) )
			$options[$id] = 0;

		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;

		switch ( $type ) {

			case 'heading':
				echo '</td></tr><tr valign="top"><td colspan="2"><h4>' . $desc . '</h4>';
				break;

			case 'checkbox':

				echo '<input class="checkbox' . $field_class . '" type="checkbox" id="' . $id . '" name="lp_options[' . $id . ']" value="1" ' . checked( $options[$id], 1, false ) . ' /> <label for="' . $id . '"></label>';

				break;

			case 'autocomplete' :

				echo '<input class="form-control autocomplete-text' . $field_class . '" type="text" id="' . $id . '" name="lp_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				break;

			case 'hidden':
				echo '<input class="autocomplete-geodata" type="hidden" id="' . $id . '" name="lp_options[' . $id . ']" value="' . esc_attr( $options[$id] ) . '" />';
				break;

			case 'text':
			default:
				echo '<input class="form-control' . $field_class . '" type="text" id="' . $id . '" name="lp_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';

				if ( $desc != '' )
					echo '<br /><span class="description">' . $desc . '</span>';

				break;

		}

	}

	/**
	 * Settings and defaults
	 *
	 * @since 1.0
	 */
	public function get_settings() {

		/* General Settings
		===========================================*/
		$this->settings['use_default'] = array(
			'section' => 'locations',
			'title'   => __( 'Use default locations' ),
			'type'    => 'checkbox',
			'std'     => 0
		);

		$this->settings['sale_location'] = array(
			'title'   => __( 'Sale default location' ),
			'std'     => 'Switzerland or London or Old Town, Prague',
			'type'    => 'autocomplete',
			'section' => 'locations',
			'desc'  => ''
		);
		$this->settings['sale_location_geodata'] = array(
			'std'     => '',
			'type'    => 'hidden',
			'section' => 'locations',
			'desc'  => ''
		);
		$this->settings['use_default_rent'] = array(
			'section' => 'locations',
			'title'   => __( 'Use default locations' ),
			'type'    => 'checkbox',
			'std'     => 0
		);
		$this->settings['rent_location'] = array(
			'title'   => __( 'Rent default location' ),
			'std'   => 'Switzerland or London or Old Town, Prague',
			'type'    => 'autocomplete',
			'section' => 'locations',
			'desc'  => ''
		);
		$this->settings['rent_location_geodata'] = array(
			'std'     => '',
			'type'    => 'hidden',
			'section' => 'locations',
			'desc'  => ''
		);

	}

	/**
	 * Initialize settings to their default values
	 *
	 * @since 1.0
	 */
	public function initialize_settings() {

		$default_settings = array();
		foreach ( $this->settings as $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}

		update_option( 'lp_options', $default_settings );

	}

	/**
	 * Register settings
	 *
	 * @since 1.0
	 */
	public function register_settings() {

		register_setting( 'lp_options', 'lp_options', array ( &$this, 'validate_settings' ) );

		foreach ( $this->sections as $slug => $title ) {
			if ( $slug == 'about' )
				add_settings_section( $slug, $title, array( &$this, 'display_about_section' ), 'lwp-options' );
			else
				add_settings_section( $slug, $title, array( &$this, 'display_section' ), 'lwp-options' );
		}

		$this->get_settings();

		foreach ( $this->settings as $id => $setting ) {
			$setting['id'] = $id;
			$this->create_setting( $setting );
		}

	}


	public function scripts() {
		global $lp_settings;
		wp_enqueue_script('lodash', get_template_directory_uri() . '/dist/scripts/lodash.js', [], null, true);
		wp_enqueue_script('lwp-google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $lp_settings['google_api_key'] . '&libraries=places&language=en', null, true);
		wp_enqueue_script('typehead', get_template_directory_uri() . '/lib/admin/assets/js/bootstrap3-typeahead.min.js', ['jquery'], null, true);
		wp_enqueue_script('lwp-autocomplete', get_template_directory_uri() . '/lib/admin/assets/js/Autocomplete.js', ['jquery', 'lwp-google-map', 'typehead', 'lodash']);
		wp_enqueue_script('lwp-admin', get_template_directory_uri() . '/lib/admin/assets/js/scripts.js', ['lwp-autocomplete'], null, true);
	}

	/**
	 * Styling for the theme options page
	 *
	 * @since 1.0
	 */
	public function styles() {
		wp_enqueue_style('lwp-admin', get_template_directory_uri() . '/lib/admin/assets/css/style.css');


	}

	/**
	 * Validate settings
	 *
	 * @since 1.0
	 */
	public function validate_settings( $input ) {

		if ( ! isset( $input['reset_theme'] ) ) {
			$options = get_option( 'lp_options' );

			foreach ( $this->checkboxes as $id ) {
				if ( isset( $options[$id] ) && ! isset( $input[$id] ) )
					unset( $options[$id] );
			}

			return $input;
		}
		return false;

	}

	public function add_location_page_scripts( $hook ) {
		global $post, $lp_settings;
		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
			if ( 'page' === $post->post_type ) {
				wp_enqueue_script('lodash', get_template_directory_uri() . '/dist/scripts/lodash.js', [], null, true);
				wp_enqueue_script('lwp-google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $lp_settings['google_api_key'] . '&libraries=places&language=en', null, true);
				wp_enqueue_script('typehead', get_template_directory_uri() . '/lib/admin/assets/js/bootstrap3-typeahead.min.js', ['jquery'], null, true);
				wp_enqueue_script('lwp-autocomplete', get_template_directory_uri() . '/lib/admin/assets/js/Autocomplete.js', ['jquery', 'lwp-google-map', 'typehead', 'lodash']);
				wp_enqueue_script('lwp-location-page', get_template_directory_uri() . '/lib/admin/assets/js/location-page.js', ['lwp-autocomplete'], null, true);

				wp_enqueue_style('lwp-admin', get_template_directory_uri() . '/lib/admin/assets/css/style.css');
			}
		}
	}

}
$theme_options = new LP_Theme_Options();
function lwp_option( $option ) {
	$options = get_option( 'lp_options' );
	if ( isset( $options[$option] ) )
		return $options[$option];
	else
		return false;
}