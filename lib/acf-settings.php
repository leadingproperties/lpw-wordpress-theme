<?php
add_filter('acf/settings/path', 'my_acf_settings_path');
function my_acf_settings_path( $path ) {
	// update path
	$path = get_template_directory() . '/lib/acf/';
	// return
	return $path;
}

add_filter('acf/settings/dir', 'my_acf_settings_dir');

function my_acf_settings_dir( $dir ) {
	// update path
	$dir = get_template_directory_uri() . '/lib/acf/';
	// return
	return $dir;
}
acf_add_options_page([
	'page_title' => 'Options',
	'menu_title' => 'Options',
	'menu_slug' => 'lp-options',
	'capability' => 'manage_options',
	'autoload' => true
]);

/* Define fields */
if( !function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_574ec8bbec4aa',
		'title' => 'General',
		'fields' => array (
			array (
				'key' => 'field_574ec8c604d9c',
				'label' => 'Header',
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array (
				'key' => 'field_574ec8e804d9d',
				'label' => 'Logo',
				'name' => 'logo',
				'type' => 'image',
				'instructions' => '350 х 90 px max',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'url',
				'preview_size' => 'full',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => 350,
				'max_height' => 90,
				'max_size' => '',
				'mime_types' => '',
			),
			array (
				'key' => 'field_574f13ac8f0c8',
				'label' => 'Contact Phone',
				'name' => 'contact_phone',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => 50,
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_574f13bf8f0c9',
				'label' => 'Contact Email',
				'name' => 'contact_email',
				'type' => 'email',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => 50,
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_574f311122b17',
				'label' => 'API',
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			),
			array (
				'key' => 'field_574f312c22b18',
				'label' => 'API url',
				'name' => 'api_url',
				'type' => 'url',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'https://api-mylpw.herokuapp.com/v1/',
				'placeholder' => '',
			),
			array (
				'key' => 'field_574f315822b19',
				'label' => 'API key',
				'name' => 'api_key',
				'type' => 'password',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'readonly' => 0,
				'disabled' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'lp-options',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));

endif;