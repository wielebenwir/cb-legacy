<?php

/*
Plugin Name: CMB2 Field Type: Icon
Plugin URI: https://wordpress.org/plugins/commons-booking
GitHub Plugin URI: https://wordpress.org/plugins/commons-booking
Description: Icon field type for CMB2.
Version: 0.1.3
Author: Annesley Newholm
License: MIT
*/

define('CMB2_ICON_FIELD_NAME', 'icon');

class CMB2_Field_Icon {

    /**
     * @var string Version
     */
    const VERSION = '0.1.0';

    /**
     * CMB2_Field_Icon constructor.
     */
    public function __construct() {
        add_filter( 'cmb2_render_icon', [ $this, 'render_icon_selector' ], 10, 5 );
        add_filter( 'cmb2_sanitize_icon', [ $this, 'sanitize_icon' ], 10, 4 );
    }

    /**
     * Render the field
     *
     * @param $field
     * @param $field_escaped_value
     * @param $object_id
     * @param $object_type
     * @param $field_type_object
     */
    public function render_icon_selector(
        CMB2_Field $field,
        $field_escaped_value,
        $object_id,
        $object_type,
        CMB2_Types $field_type_object
    ) {

        $this->enqueue_scripts();

        if ( version_compare( CMB2_VERSION, '2.2.2', '>=' ) ) {
            $field_type_object->type = new CMB2_Type_Text( $field_type_object );
        }

        // Icons from dir
        $attributes           = $field->args( 'options' );
        if ( isset( $attributes['paths'] ) ) {
          $plugin_icons_dirs    = $attributes['paths'];
          $field_value          = ( isset( $field_escaped_value[CMB2_ICON_FIELD_NAME] ) ? $field_escaped_value[CMB2_ICON_FIELD_NAME] : '' );
          $id_base              = "{$field->args( 'id' )}_{CMB2_ICON_FIELD_NAME}";
          echo("<ul class='icon-scroll'>");
          foreach ( $plugin_icons_dirs as $plugin_icons_dir ) {
            if ( preg_match( '/^https?:\/\//', $plugin_icons_dir ) ) {
              // Webpage
              $this->render_icon_selector_webpage_contents( $plugin_icons_dir, $field, $field_value, $id_base, $field_type_object );
            } else {
              // Normal filesystem directory
              $this->render_icon_selector_directory( $plugin_icons_dir, $field, $field_value, $id_base, $field_type_object );
            }
          }
          echo("</ul>");
        } else echo( __('No options->paths to images area set') );

        // Custom icon
        echo('<ul class"icon-other"><li class="custom_icon"><label>Custom:</label> ');
        $this->render_input( CMB2_ICON_FIELD_NAME, $field, $field_escaped_value, $field_type_object );
        echo('</li></ul>');

        $field_type_object->_desc( true, true );

    }
    
    /**
     * Render the webpage contents recursively
     *
     * @param $url
     * @param $field
     * @param $field_value
     * @param $id_base
     * @param $field_type_object
     */
    public function render_icon_selector_webpage_contents( $url, $field, $field_value, $id_base, $field_type_object ) {
      $response = wp_remote_get( $url );
      if( ! is_wp_error( $response ) && is_array( $response ) ) {
        $code = wp_remote_retrieve_response_code( $response );
        $type = wp_remote_retrieve_header( $response, 'content-type' );
        if ( $code == '200' && strstr( $type, 'text' ) ) {
          preg_match_all( '/ (href|src)=["|\']([^"\']+)/', wp_remote_retrieve_body( $response ), $links );
          if ( is_array( $links ) && count( $links ) > 2 ) {
            foreach ( $links[2] as $file_path_url ) {
              if ( preg_match( '/\.(png|jpg|ico|jpeg|bmp)/', $file_path_url ) ) {
                $checked       = ( $file_path_url == $field_value );
                $selected      = ( $checked ? 'selected' : '' );

                echo("<li class='$selected'>");
                $id = $this->render_input_radio( CMB2_ICON_FIELD_NAME, $field, $file_path_url, $field_value, $field_type_object );
                echo("<label for='$id'><img src='$file_path_url'></label>");
                echo('</li>');
              }
            }
          }
        }
      }
    }
    
    /**
     * Render the directory recursively
     *
     * @param $plugin_icons_dir
     * @param $field
     * @param $field_value
     * @param $id_base
     * @param $field_type_object
     */
    public function render_icon_selector_directory( $plugin_icons_dir, $field, $field_value, $id_base, $field_type_object ) {
      // SECURITY: we are accessing the filesystem here and pulicly outputting results...
      $wordpress_dir        = get_home_path();
      $plugin_icons_dir_url = '/' . str_replace( get_home_path(), '', $plugin_icons_dir );

      if ( substr( $plugin_icons_dir, 0, strlen($wordpress_dir) ) == $wordpress_dir ) {
        if ( file_exists( $plugin_icons_dir ) && is_dir( $plugin_icons_dir ) ) {
          foreach( scandir($plugin_icons_dir) as $filename ) {
            $full_path = "$plugin_icons_dir/$filename";
            if ( is_dir( $full_path ) ) {
              if ( ! ( $filename == '.' || $filename == '..' ) ) {
                $this->render_icon_selector_directory( $full_path, $field, $field_value, $id_base, $field_type_object );
              }
            } else {
              if ( ! strstr( $filename , '-shadow.' ) ) {
                $file_path_url = "$plugin_icons_dir_url/$filename";
                $checked       = ( $file_path_url == $field_value );
                $selected      = ( $checked ? 'selected' : '' );
                
                // TODO: we were setting the correct label id here to allow HTML to check the input. doesn't work though
                // for='$id'
                // Probably because the id text content is complex...
                echo("<li class='$selected'>");
                $id = $this->render_input_radio( CMB2_ICON_FIELD_NAME, $field, $file_path_url, $field_value, $field_type_object );
                echo("<label for='$id'><img src='$file_path_url'></label>");
                echo('</li>');
              }
            }
          }
        } else {
          // Do nothing because maybe the directory has not been created yet, e.g. a media folder
          // echo( __('directory does not exist') );
          // echo( "[$plugin_icons_dir]" );
        }
      } else {
        echo( __('options->paths must be within the WordPress fielsystem somewhere') );
        echo( "[$plugin_icons_dir]" );
      }
    }

    /**
     * Sanitize values
     */
    public function sanitize_icon( $override_value, $value, $object_id, $field_args ) {
        // Add in the full URL and final filename selection for conveinience
        if ( isset( $value['icon'] ) && $value['icon'] ) {
          $icon_url = $value['icon'];
          
          // Make the URL relative if it points to this domain
          $home_url = get_home_url(); // http://example.com
          if ( substr( $icon_url, 0, strlen($home_url) ) == $home_url ) {
            $icon_url = substr( $icon_url, strlen($home_url) );
          }
          
          // If we have a relative URL then check for a shadow
          if ( $icon_url[0] == '/') {            
            $wordpress_dir = get_home_path();
            $icon_path     = "$wordpress_dir/$icon_url";
            
            // Auto-detect shadow
            $shadow_path = preg_replace( '/\.([a-zA-Z0-9]+)$/', '-shadow.$1', $icon_path );
            if ( file_exists( $shadow_path ) ) {
              $shadow_url = preg_replace( '/\.([a-zA-Z0-9]+)$/', '-shadow.$1', $icon_url );
              $value['icon_shadow'] = $shadow_url;
            }
          }
        }
        return $value;
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'cmb2-icon-main', plugins_url( 'assets/js/main.js',    __FILE__ ), NULL, self::VERSION );
        wp_enqueue_style(  'cmb2-icon-main', plugins_url( 'assets/css/style.css', __FILE__ ), NULL, self::VERSION );
    }

    /**
     * @param string     $field_name
     * @param CMB2_Field $field
     * @param            $field_escaped_value
     * @param CMB2_Types $field_type_object
     *
     * @internal param array $args
     */
    protected function render_input_radio( $field_name = '', CMB2_Field $field, $field_this_value, $field_escaped_value, CMB2_Types $field_type_object ) {
        $value_id = 'icon_' . preg_replace( '/[^a-zA-Z0-9]+/', '-', $field_this_value );
        $id       = "{$field->args( 'id' )}_{$field_name}_{$value_id}";
        $checked  = ( isset( $field_escaped_value[$field_name] ) && $field_this_value == $field_escaped_value[$field_name] );
        $attrs    = $field_type_object->concat_attrs( [
            'id'    => $id,
            'type'  => 'radio',
            'name'  => "{$field->args( '_name' )}[{$field_name}]",
            'value' => $field_this_value,
            'checked' => $checked,
            'class' => "icon__{$field_name}",
            'desc'  => ''
        ], [ 'attributes' ] );

        echo sprintf( '<input%s />', $attrs );
        
        return $id;
    }

    /**
     * @param string     $field_name
     * @param CMB2_Field $field
     * @param            $field_escaped_value
     * @param CMB2_Types $field_type_object
     *
     * @internal param array $args
     */
    protected function render_input( $field_name = '', CMB2_Field $field, $field_escaped_value, CMB2_Types $field_type_object, $field_type = 'text' ) {
        $id    = "{$field->args( 'id' )}_{$field_name}";
        $attrs = $field_type_object->concat_attrs( [
            'id'    => $id,
            'type'  => $field_type,
            'name'  => "{$field->args( '_name' )}[{$field_name}]",
            'value' => isset( $field_escaped_value[ $field_name ] ) ? $field_escaped_value[ $field_name ] : '',
            'class' => "icon__{$field_name}",
            'desc'  => ''
        ], [ 'attributes' ] );

        echo sprintf( '<input%s />', $attrs );
        
        return $id;
    }
}

new CMB2_Field_Icon();
