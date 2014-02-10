WP_Settings_Page
================

An object-oriented approach to make creating WordPress settings pages easier.

The WordPress Settings API, although robust, seems to be the not-so-fun API for most WordPress developers to play with. That's why I've made a few classes that wrap the Settings API functionality into a simpler, usable format.

## Example Usage
```php
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

	$settings_page->get_section( 'social_media' )->add_field( array(
		'slug'            => 'about_section',
		'title'           => 'About Blurb',
		'render_callback' => 'totally_rad_wysiwyg',
	) );

}

function totally_rad_wysiwyg( $field ) {
	wp_editor( html_entity_decode( esc_html( $field->value ) ), $field->slug, array(
		'name' => $field->settings_page .'['. $field->slug .']',
		'textarea_rows' => 5,
	) );
}
```
