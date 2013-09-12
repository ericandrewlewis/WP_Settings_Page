<?php

class WP_Settings_Page {

	/**
	 *
	 */
	var $args;

	var $sections = array();

	function __construct( $args = array() ) {

		$defaults = array(
			'page_title'      => 'Settings Page Title',          // The title to be displayed in the browser window on the settings page
			'menu_title'      => 'Settings Menu Title',           // Menu item text for the settings page
			'capability'      => 'manage_options',               // Which type of users can see this menu item
			'slug'            => '',                             // The unique slug for this menu item
			'render_callback' => array( $this, 'render_page' ),        // The rendering callback
			'parent_slug'     => '',
			'icon_url'        => '',
			'position'        => NULL,
		);

		$args = wp_parse_args( $args, $defaults );

		foreach ( $args as $arg_name => $arg_value )
			$this->$arg_name = $arg_value;

		extract( $args );

		if ( $parent_slug ) {
			add_submenu_page(
				$parent_slug,
				$page_title,
				$menu_title,
				$capability,
				$slug,
				$render_callback
			);
		} else {
			add_menu_page(
				$page_title,
				$menu_title,
				$capability,
				$slug,
				$render_callback,
				$icon_url,
				$position
			);
		}

		register_setting( $slug, $slug );
	}

	function render_page() {
		?>
			<!-- Create a header in the default WordPress 'wrap' container -->
			<div class="wrap">

				<div id="icon-themes" class="icon32"></div>
				<h2><?php echo $this->page_title; ?></h2>
				<?php settings_errors(); ?>

				<form method="post" action="options.php">
					<?php settings_fields( $this->slug ); ?>
					<?php do_settings_sections( $this->slug ); ?>
					<?php submit_button(); ?>
				</form>

			</div><!-- /.wrap -->
		<?php
	}

	function add_section( $args ) {
		$this->sections[$args['slug']] = new WP_Settings_Section( $args );
	}

	function get_section( $slug ) {
		return $this->sections[$slug];
	}

}

class WP_Settings_Section {

	var $fields = array();

	function __construct( $args = array() ) {
		$defaults = array(
			'slug'          => '',               // The unique slug for this section
			'title'         => 'Section Title',  // The title to be displayed at the top of the section
			'settings_page' => NULL              // The slug of the settings page this section will be shown on
		);

		$args = wp_parse_args( $args, $defaults );

		foreach ( $args as $arg_name => $arg_value )
			$this->$arg_name = $arg_value;

		extract( $args );

		add_settings_section(
			$slug,
			$title,
			array( $this, 'render' ),
			$settings_page
		);
	}

	function render() {

	}

	function add_field( $args = array() ) {
		$args['section'] = $this->slug;
		$args['settings_page'] = $this->settings_page;
		$fields[$args['slug']] = new WP_Settings_Field( $args );
	}
}

class WP_Settings_Field {


	function __construct( $args = array() ) {
		$defaults = array(
			'slug'          => '',                        // The unique slug for this section
			'title'         => 'Field Title',             // The title to be displayed at the top of the section
			'callback'      => array( $this, 'render' ),  // Render callback
			'settings_page' => NULL,                      // The slug of the settings page this section will be shown on
			'section'       => ''                         // Slug of the section that this field belongs to
		);

		$args = wp_parse_args( $args, $defaults );

		foreach ( $args as $arg_name => $arg_value )
			$this->$arg_name = $arg_value;

		extract( $args );

		add_settings_field(
			$slug,
			$title,
			$callback,
			$settings_page,
			$section
		);

	}

	function render() {
		$option = get_option( $this->settings_page );
		if ( ! empty( $option[$this->slug] ) )
			$value = $option[$this->slug];
		else
			$value = '';
		?><input type="text" id="<?php echo $this->slug ?>" name="<?php echo $this->settings_page ?>[<?php echo $this->slug ?>]" value="<?php echo $value ?>" ><?php
	}

}