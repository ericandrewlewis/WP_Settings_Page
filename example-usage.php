<?php

add_filter('settings_array', 'bwp_create_settings' );
function bwp_create_settings( $settings ){
    $settings = array(
        'slug'          => 'settings_page',
        'menu_title'    => 'Settings',
        'capability'    => 'manage_options',
        'page_title'    => 'Plugin Settings',
        'parent_slug'   => '/tools.php',
        'sections'      => array(
            'new_section' => array(
                'slug'              => 'new_section',
                'title'             => 'New Section',
                'settings_page'     => 'settings_page',
                'fields'            => array(
                    'text_field' => array(
                        'slug'          => 'text_field',
                        'title'         => 'Text Field',
                        'field_type'    => 'text',
                    ),
                    'checkbox' => array(
                        'slug'          => 'checkbox',
                        'title'         => 'Checkbox Field',
                        'field_type'    => 'checkbox',
                    ),
                    'upload' => array(
                        'slug'          => 'upload',
                        'title'         => 'Upload Field',
                        'field_type'    => 'upload',
                    ),
                    'wysiwyg' => array(
                        'slug'          => 'wysiwyg',
                        'title'         => 'WYSIWYG Field',
                        'field_type'    => 'wysiwyg',
                        'instructions'    => '<p>{name} - Adds donor name<br>{amount} - Adds donation amount<br>{sitename} - Adds site name</p>',
                    ),
                )
            ),
            
        )
    );
    return $settings;
}