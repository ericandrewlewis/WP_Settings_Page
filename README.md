WP_Settings_Page
================

An object-oriented approach to make creating WordPress settings pages easier.

The WordPress Settings API, although robust, seems to be the not-so-fun API for most WordPress developers to play with. That's why I've made a few classes that wrap the Settings API functionality into a simpler, usable format. 

===Example Usage===

```
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
```
