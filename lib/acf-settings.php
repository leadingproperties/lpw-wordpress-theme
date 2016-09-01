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

if( function_exists('acf_add_local_field_group') ):

	acf_add_local_field_group(array (
		'key' => 'group_57bd72e15c6e2',
		'title' => 'Contact',
		'fields' => array (
			array (
				'key' => 'field_57bd72f5fcd02',
				'label' => 'Offices',
				'name' => 'offices',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => '',
				'max' => '',
				'layout' => 'table',
				'button_label' => 'Add Office',
				'sub_fields' => array (
					array (
						'key' => 'field_57bd7359fcd03',
						'label' => 'Office title',
						'name' => 'title',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
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
						'key' => 'field_57bd737ffcd04',
						'label' => 'Office Photo',
						'name' => 'image',
						'type' => 'image',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'id',
						'preview_size' => 'office',
						'library' => 'all',
						'min_width' => 350,
						'min_height' => 210,
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => '',
					),
					array (
						'key' => 'field_57bd73bcfcd05',
						'label' => 'Office Address',
						'name' => 'address',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => 'br',
						'readonly' => 0,
						'disabled' => 0,
					),
				),
			),
			array (
				'key' => 'field_57bd8deb00c8e',
				'label' => 'Agents Section Title',
				'name' => 'agents_title',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
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
				'key' => 'field_57bd8e0b00c8f',
				'label' => 'Agents',
				'name' => 'agents',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => '',
				'min' => '',
				'max' => '',
				'layout' => 'table',
				'button_label' => 'Add Agent',
				'sub_fields' => array (
					array (
						'key' => 'field_57bd8e1900c90',
						'label' => 'Name',
						'name' => 'name',
						'type' => 'text',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
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
						'key' => 'field_57bd8e3500c91',
						'label' => 'Contact Information',
						'name' => 'info',
						'type' => 'textarea',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'placeholder' => '',
						'maxlength' => '',
						'rows' => '',
						'new_lines' => 'br',
						'readonly' => 0,
						'disabled' => 0,
					),
					array (
						'key' => 'field_57bd8e4c00c92',
						'label' => 'Photo',
						'name' => 'photo',
						'type' => 'image',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'id',
						'preview_size' => 'agent',
						'library' => 'all',
						'min_width' => 350,
						'min_height' => 350,
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => '',
					),
				),
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'page-contact.php',
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

	acf_add_local_field_group(array (
		'key' => 'group_574ec8bbec4aa',
		'title' => 'General',
		'fields' => array (
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
			array (
				'key' => 'field_57ab60ff32728',
				'label' => 'Pages',
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
				'key' => 'field_57ab612332729',
				'label' => 'Sale',
				'name' => 'sale',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_57ab61693272a',
				'label' => 'Rent',
				'name' => 'rent',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_57ab61793272b',
				'label' => 'Sale Favorites',
				'name' => 'sale_favorites',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_57ab61b23272c',
				'label' => 'Rent Favorites',
				'name' => 'rent_favorites',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_57b730b82cb0f',
				'label' => 'Single Object',
				'name' => 'single_object',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'id',
				'ui' => 1,
			),
			array (
				'key' => 'field_57c81f54e9c44',
				'label' => 'Sale Share',
				'name' => 'sale_share',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_57c81faae9c45',
				'label' => 'Rent Share',
				'name' => 'rent_share',
				'type' => 'page_link',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array (
					0 => 'page',
				),
				'taxonomy' => array (
				),
				'allow_null' => 0,
				'multiple' => 0,
			),
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
				'key' => 'field_57c81feae9c46',
				'label' => 'Header Style',
				'name' => 'header_style',
				'type' => 'radio',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => array (
					'left' => 'Logo at left, contacts at right',
					'center' => 'Logo at center',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'vertical',
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
				'return_format' => 'id',
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
				'key' => 'field_57bf43ff5ac8c',
				'label' => 'Logo Max Height',
				'name' => 'logo_max_height',
				'type' => 'number',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => 'px',
				'min' => 10,
				'max' => 90,
				'step' => '',
				'readonly' => 0,
				'disabled' => 0,
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
				'key' => 'field_57504c576e9e2',
				'label' => 'Footer',
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
				'key' => 'field_575457b087913',
				'label' => 'Address',
				'name' => 'address',
				'type' => 'textarea',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'The Leading Properties of the World
Grand-Rue 3, 1820 Montreux, Switzerland info@leadingproperties.com
Montreux +41 21 966 03 03
Moscow +7 495 565 34 09',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'new_lines' => 'br',
				'readonly' => 0,
				'disabled' => 0,
			),
			array (
				'key' => 'field_57504cb26e9e4',
				'label' => 'Facebook link',
				'name' => 'facebook_link',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_57504ce96e9e5',
				'label' => 'Twitter Link',
				'name' => 'twitter_link',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_57504cfb6e9e6',
				'label' => 'Google Plus Link',
				'name' => 'google_plus_link',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_57504d0a6e9e7',
				'label' => 'LinkedIn Link',
				'name' => 'linkedin_link',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_57504d1a6e9e8',
				'label' => 'Instagram Link',
				'name' => 'instagram_link',
				'type' => 'url',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
			),
			array (
				'key' => 'field_57af5658e6aef',
				'label' => 'Google Services',
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
				'key' => 'field_57c82047e9c47',
				'label' => 'Google API key',
				'name' => 'google_api_key',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
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
				'key' => 'field_57af566be6af0',
				'label' => 'Use Google Url Shortener?',
				'name' => 'use_google_shortener',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array (
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
			),
			array (
				'key' => 'field_57af5699e6af1',
				'label' => 'Google Shortener API key',
				'name' => 'google_shortener_api',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array (
					array (
						array (
							'field' => 'field_57af566be6af0',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array (
					'width' => '',
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