WP_Settings_Page
================

An object-oriented approach to make creating WordPress settings pages easier.

The WordPress Settings API, although robust, seems to be the not-so-fun API for most WordPress developers to play with. That's why I've made a few classes that wrap the Settings API functionality into a simpler, usable format.

## Example Usage
```php
<?php
add_filter('settings_array', 'create_settings' );
function create_settings( $settings ){
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


```
