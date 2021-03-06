<?php

class WP_Settings_Page {

	/**
	 * Array of WP_Settings_Sections objects
	 */
	var $sections = array();

	/**
	 * URL slug of the admin page.
	 */
	var $url_slug = '';

	function __construct( $args = array() ) {

		$defaults = array(
			'page_title'        => 'Settings Page Title', // The title to be displayed in the browser window on the settings page
			'menu_title'        => 'Settings Menu Title', // Menu item text for the settings page
			'capability'        => 'manage_options',      // Which type of users can see this menu item
			'slug'              => '',                    // The unique slug for this menu item
			'render_callback'   => NULL,                  // The rendering callback
			'sanitize_callback' => NULL,                  // The sanitization callback
			'parent_slug'       => '',
			'icon_url'          => '',
			'position'          => NULL,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->page_title        = $args['page_title'];
		$this->menu_title        = $args['menu_title'];
		$this->capability        = $args['capability'];
		$this->slug              = $args['slug'];
		$this->render_callback   = $args['render_callback'];
		$this->sanitize_callback = $args['sanitize_callback'];
		$this->parent_slug       = $args['parent_slug'];
		$this->icon_url          = $args['icon_url'];
		$this->position          = $args['position'];

		if ( $this->parent_slug ) {
			$this->url_slug = add_submenu_page(
				$this->parent_slug,
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->slug,
				array( $this, 'render_callback' )
			);
		} else {
			$this->url_slug = add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->slug,
				array( $this, 'render_callback' ),
				$this->icon_url,
				$this->position
			);
		}

		register_setting( $this->slug, $this->slug, array( $this, 'sanitize_callback' ) );
	}

	/**
	 * The main render method.
	 *
	 * Passes object to render method
	 */
	function render_callback() {
		$callback = NULL === $this->render_callback
			? array( $this, 'render_page' )
			: $this->render_callback;

		call_user_func( $callback, $this );
	}

	/**
	 * Render the settings page
	 */
	function render_page() {
		?>
			<!-- Create a header in the default WordPress 'wrap' container -->
			<div class="wrap">

				<div id="icon-themes" class="icon32"></div>
				<h2><?php echo $this->page_title; ?></h2>
				<?php // settings_errors(); ?>

				<form method="post" action="options.php">
					<?php settings_fields( $this->slug ); ?>
					<?php do_settings_sections( $this->slug ); ?>
					<?php submit_button(); ?>
				</form>

			</div><!-- /.wrap -->
		<?php
	}

	/**
	 * The main render method.
	 *
	 * Passes object to render method
	 */
	function sanitize_callback( $value ) {
		if ( NULL === $this->sanitize_callback ) {
			// No sanitization by default
			return $value;
		}
		// Custom sanitization callback
		return call_user_func( $this->sanitize_callback, $value, $this );
	}

	/**
	 * Add a settings section to this settings page.
	 *
	 * @see WP_Settings_Section::__construct()
	 */
	function add_section( $args ) {
		$this->sections[$args['slug']] = new WP_Settings_Section( $args );
		return $this;
	}

	function get_section( $slug ) {
		return $this->sections[$slug];
	}

}

class WP_Settings_Section {

	var $fields = array();

	/**
	 * @param array $args {
	 *     An array of arguments. Required.
	 *
	 *     @type string   'slug'            Unique identifying slug.
	 *     @type string   'title'           Title for the header of the section.
	 *     @type string   'settings_page'   Slug for the settings page this section will be shown on
	 *     @type callback 'render_callback' Render callback for the section
	 * }
	 */
	function __construct( $args ) {
		$defaults = array(
			'slug'            => '',
			'title'           => '',
			'settings_page'   => NULL,
			'render_callback' => NULL,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->slug            = $args['slug'];
		$this->title           = $args['title'];
		$this->settings_page   = $args['settings_page'];
		$this->render_callback = $args['render_callback'];

		add_settings_section(
			$this->slug,
			$this->title,
			array( $this, 'render_callback' ),
			$this->settings_page
		);
	}

	/**
	 * The main render method.
	 *
	 * Passes object to render method
	 */
	function render_callback() {
		$callback = NULL === $this->render_callback
			? array( $this, 'render' )
			: $this->render_callback;

		call_user_func( $callback, $this );
	}

	/**
	 * Built-in render callback that does nothing.
	 *
	 * Override in a subclass.
	 */
	function render() {}

	/**
	 * Adds a settings field as a child of this section.
	 *
	 * @see WP_Settings_Field::__construct()
	 */
	function add_field( $args = array() ) {
		$args['section'] = $this->slug;
		$args['settings_page'] = $this->settings_page;
		$fields[$args['slug']] = new WP_Settings_Field( $args );
		return $this;
	}
}

class WP_Settings_Field {

	/**
	 * @param array $args {
	 *     An array of arguments. Required.
	 *
	 *     @type string   'slug'            Unique identifying slug.
	 *     @type string   'title'           Title that will be output for the field.
	 *     @type callback 'render_callback' Render callback for the section
	 *     @type string   'settings_page'   Slug of the settings page this section will be shown on
	 *     @type string   'section'   Slug of the section this field will be shown in
	 *     @type string   'field_type'   The type of input field
	 * }
	 */
	function __construct( $args = array() ) {
		$defaults = array(
			'slug'            => '',
			'title'           => '',
			'render_callback' => NULL,
			'settings_page'   => NULL,
			'section'         => '',
			'field_type'      => 'text',
			'options'         => NULL,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->slug            = $args['slug'];
		$this->title           = $args['title'];
		$this->render_callback = $args['render_callback'];
		$this->settings_page   = $args['settings_page'];
		$this->section         = $args['section'];
		$this->field_type      = $args['field_type'];
		$this->field_options   = $args['options'];

		// Preload the value of this field
		$option = get_option( $this->settings_page );
		if ( ! empty( $option[$this->slug] ) )
			$this->value = $option[$this->slug];
		else
			$this->value = '';

		add_settings_field(
			$this->slug,
			$this->title,
			array( $this, 'render_callback' ),
			$this->settings_page,
			$this->section
		);

	}


	/**
	 * The main render method.
	 *
	 * Calls a submethod depending on the field_type.
	 */
	function render_callback() {
		if ( NULL === $this->render_callback ) {
			$sub_render_method = 'render_' . $this->field_type;
			call_user_func( array( $this, $sub_render_method ) );
		} else {
			call_user_func( $this->render_callback, $this );
		}
	}

	/**
	 * Render a simple text input box
	 */
	function render_text() {
		?><input type="text" id="<?php echo $this->slug ?>" name="<?php echo $this->settings_page ?>[<?php echo $this->slug ?>]" value="<?php echo $this->value ?>" ><?php
	}

	/**
	 * Render a textarea
	 */
	function render_textarea() {
		?><textarea id="<?php echo $this->slug ?>" name="<?php echo $this->settings_page ?>[<?php echo $this->slug ?>]"><?php echo $this->value ?></textarea><?php
	}

}
