<?php
add_action( 'admin_menu', 'register_settings_page' );

function register_settings_page() {
	$settings_page = new WP_Settings_Page( array(
		'slug' => 'totally_rad_theme_settings',
		'menu_title' => 'Theme Settings',
		'page_title' => 'Theme Settings'
	) );

	$settings_page->add_section( array(
		'slug' => 'social_media',
		'title' => 'Social Media Links',
		'settings_page' => 'totally_rad_theme_settings'
	) );

	$settings_page->get_section( 'social_media' )->add_field( array(
		'slug' => 'twitter_link',
		'title' => 'Twitter Link'
	) );

	$settings_page->get_section( 'social_media' )->add_field( array(
		'slug' => 'facebook_link',
		'title' => 'Facebook Link'
	) );

	$settings_page->get_section( 'social_media' )->add_field( array(
		'slug' => 'xanga_link',
		'title' => 'Xanga Link'
	) );

	$settings_page->get_section( 'social_media' )->add_field( array(
		'slug' => 'instagram_link',
		'title' => 'Instagram Link'
	) );
}