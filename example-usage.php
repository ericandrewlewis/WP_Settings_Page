<?php

add_action( 'admin_menu', 'register_settings_page' );

function register_settings_page()  {
	$settings_page = new WP_Settings_Page( array(
		'slug' => 'more_settings',
		'menu_title' => 'More Settings',
		'page_title' => 'More Settings'
	) );

	$settings_page->add_section( array(
		'slug' => 'theme_options',
		'title' => 'Theme Options',
		'settings_page' => 'more_settings'
		)
	);

	$settings_page->get_section( 'theme_options' )->add_field( array(
		'slug' => 'footer_text',
		'title' => 'Footer Text'
	) );
}
