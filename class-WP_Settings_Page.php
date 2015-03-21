<?php
    /**
     * Defines location of our admin class
     * replace get_template_directory_uri() with 
     * plugin_dir_url(  __FILE__  ) if this file is in your plugin directory
     */
    define('WP_SETTINGS_LOCATION', get_template_directory_uri());
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
            wp_enqueue_media(); 
            wp_enqueue_script( 'admin-class-settings', WP_SETTINGS_LOCATION . '/js/settings.js' , array('jquery'), null, true );
            ?>
                <!-- Create a header in the default WordPress 'wrap' container -->
                <div class="wrap">
                    <h2 class="nav-tab-wrapper">
                        <?php
            
                            $sections = $this->sections;
                            foreach ($sections as $section ) {
                                echo '<a class="nav-tab" href="#' . $section->slug . '">' . $section->title . '</a>';
                            }
                            
                        ?>
                    </h2><br/>
                    <div id="icon-themes" class="icon32"></div>
                    
                    <?php // settings_errors(); ?>

                    <form method="post" action="options.php">
                        <div class="settings-fields-wrapper">
                            <?php settings_fields( $this->slug ); ?>
                        </div>
                        <?php $this->do_settings_sections_tabs( $this->slug ); ?>
                        <?php submit_button(); ?>
                    </form>
                </div><!-- /.wrap -->
            <?php
        }
        /**
         * Automatically creates tabs for sections
         * @param $page aka $this->slug
         */
        function do_settings_sections_tabs($page){
            global $wp_settings_sections, $wp_settings_fields;
            if(!isset($wp_settings_sections[$page])) :
                return;
            endif;
            foreach((array)$wp_settings_sections[$page] as $section) {
                printf('<div class="settings_panel" id="%1$s">',
                    $section['id']      /** %1$s - The ID of the tab */
                );
                if(!isset($section['title']))
                    continue;
                if($section['callback'])
                    call_user_func($section['callback'], $section);
                if(!isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]))
                    continue;
                echo '<h2>' . $section['title'] . '</h2>';
                echo '<table class="form-table">';
                do_settings_fields($page, $section['id']);
                echo '</table>';
                echo '</div>';
            }
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
                'instructions'    => '',
            );
            $args = wp_parse_args( $args, $defaults );
            $this->slug            = $args['slug'];
            $this->title           = $args['title'];
            $this->render_callback = $args['render_callback'];
            $this->settings_page   = $args['settings_page'];
            $this->section         = $args['section'];
            $this->field_type      = $args['field_type'];
            $this->field_options   = $args['options'];
            $this->instructions    = $args['instructions'];
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
            if($this->instructions != '') {
                echo '<br>' . $this->instructions;
            }
        }
        /**
         * Render a textarea
         */
        function render_textarea() {
            ?><textarea id="<?php echo $this->slug ?>" name="<?php echo $this->settings_page ?>[<?php echo $this->slug ?>]"><?php echo $this->value ?></textarea><?php
            if($this->instructions != '') {
                echo '<br>' . $this->instructions;
            }
        }
        /**
         * Render a checkbox
         */
        function render_checkbox() {
                $html = '<label for="' . $this->settings_page . '[' . $this->slug . ']">';
                    $html .= '<input type="checkbox" name="' . $this->settings_page . '[' . $this->slug . ']" id="' . $this->slug . '" value="1" ' . checked( 1, $this->value, false ) . ' />';
                $html .= '</label>';
                echo $html;
                if($this->instructions != '') {
                    echo '<br>' . $this->instructions;
                }
        }
        /**
         * Render a Upload Field
         */
        function render_upload() {
            ?><label for="upload_image">
                <input id="<?php echo $this->slug; ?>" class="upload_image" id=""type="text" size="36" name="<?php echo $this->settings_page ?>[<?php echo $this->slug ?>]" value="<?php echo $this->value ?>" /> 
                <input id="<?php echo $this->slug; ?>-button" class="upload_image_button button button-second" type="button" value="Upload Image" /><?php
                if($this->instructions != '') {
                    echo '<br>' . $this->instructions;
                }
                ?>
            </label><?php
        }
        /**
         * Render WYSIWYG editor
         */
        function render_wysiwyg() {
            $settings = array(
                'teeny' => true,
                'media_buttons' => false,
                'textarea_rows' => 15,
                'tabindex' => 1,
                'textarea_name' => $this->settings_page . '[' . $this->slug . ']'
            );
            wp_editor(esc_html($this->value), $this->slug, $settings);
            if($this->instructions != '') {
                echo '<br>' . $this->instructions;
            }
        }
    }


    add_action( 'admin_menu', 'register_settings_page' );
    function register_settings_page() {
        $mysettings = apply_filters( 'settings_array', array());
        $currPage = array();
        $currPage['slug'] = $mysettings['slug'];
        $currPage['menu_title'] = $mysettings['menu_title'];
        $currPage['capability'] = $mysettings['capability'];
        $currPage['page_title'] = $mysettings['page_title'];

        if(isset($mysettings['parent_slug'])) {
            $currPage['parent_slug'] = $mysettings['parent_slug'];
        }
        $settings_page = new WP_Settings_Page( $currPage );

        $sections = $mysettings['sections'];
        foreach($sections as $slug => $section_info ) {
            $settings_page->add_section( array(
                'slug'          => $section_info['slug'],
                'title'         => $section_info['title'],
                'settings_page' => $section_info['settings_page']
            ));
            $fields = $section_info['fields'];

            foreach ( (array) $fields as $field ) {
                $currField = array();
                $currField['slug'] = $field['slug'];
                $currField['title'] = $field['title'];
                $currField['field_type'] = $field['field_type'];
                if(isset($field['instructions'])) {
                    $currField['instructions'] = $field['instructions'];
                }
                $settings_page->get_section( $section_info['slug'] )->add_field( $currField );
            }

        }
    }