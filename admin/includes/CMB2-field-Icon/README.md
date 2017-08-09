# CMB2 Field Icon
Allow icon selection for a post from:
1. local directories:  THIS_PLUGIN_PATH . 'public/assets/images'
  The code will recurse all sub-directories also
2. remote webpages:    http://www.flaticon.com/packs/holiday-travelling-3
  The code will check all a and img tags in the remote webpage for images
3. upload directories: wp_upload_dir()['path']

## Installation

#### Composer
`composer require anewholm/cmb2-field-icon`

#### Manual
1. [Download](https://github.com/anewholm/cmb2-field-icon/archive/master.zip) the plugin
2. Place the plugin folder in your `/wp-content/plugins/` directory
3. Activate the plugin in the plugins dashboard

# Usage
```php
array(
  'id' => $prefix . 'location_metabox_icon',
  'title' => __( 'Icon', $plugin_slug ),
  'object_types' => array( $post_type, ), // Post type
  'context' => 'side',
  'priority' => 'high',
  'show_names' => false,
  'fields' => array(        
    array(
      'name' => __( 'Icon', $plugin_slug ),
      'id' => $plugin_slug . '_location_icon',
      'type' => 'icon',
      'desc' => 'Used in Maps.',
      'options' => array(
        'paths' => array( 
          THIS_PLUGIN_PATH . 'public/assets/images',
          wp_upload_dir()['path'], // Since 2.0.0
          'http://www.flaticon.com/packs/holiday-travelling-3',
        ),
      ),
    ),
  ),      
),
```