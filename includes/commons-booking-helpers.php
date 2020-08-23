<?php

/**
 * Commons Booking Helpers
 * 
 * Helper functions
 *    Dropdown of Pages
 *    Replace Template Tags    
 *    cb_get_dates_between
 *
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */


/**
 * Get a List of all wordpress pages for use in dropdown selects. 
 *
 * @return Array of wordpress pages as [pagedID][title]
 */

function cb_pages_dropdown() {
  // dropdown for page select
  $pages = get_pages();
  $dropdown = array();
  
  foreach ( $pages as $page ) {
    $dropdown[$page->ID] = $page->post_title;
  }
  return $dropdown;
}

/**
 * Get a List of all wordpress pages for use in dropdown selects. 
 *
 * @return Array of Sub-Folders
 */

function cb_theme_select() {

  $themes_dir = Commons_Booking::get_plugin_dir() . 'assets/css/themes';
  $themes_url = Commons_Booking::get_plugin_url() . 'assets/css/themes';
  $directories = glob( $themes_dir . '/*' , GLOB_ONLYDIR);

  $dropdown = array();
  
  foreach ( $directories as $dir ) {
    $theme_name = basename( $dir );
    $dropdown[ $theme_name ] = $theme_name;
  }
  return $dropdown;
}


/**
 * Replace template tags – {MYTAG} with tags array
 *
 *@param string to replace
 *@param array of tags
 *
 *@return string
 */
 function cb_replace_template_tags( $string, $tags_array ) {
    foreach($tags_array as $key => $value){
        $string = str_replace('{{'.strtoupper($key).'}}', $value, $string);
    }
    return $string;
  }

/**
 * Format message
 *
 *@param $string, 
 *
 *@return html
 */
 function cb_display_message( $string, $replace = null, $success = TRUE ) {
    
    if ( ! $replace ) {
      $replace = array();
    }

    $msg = cb_replace_template_tags( $string, $replace );
    $class = $success ? 'success' : 'error';
    return sprintf (' <p class="cb-message %s">%s</p>', $class , $msg );
  }

/**
 * Get a list of all dates within the defind range. 
 *
 * @return array dates
 */
 function cb_get_dates_between( $date_start, $date_end ) {
    $dates = array ( $date_start );
    while(end($dates) < $date_end){
        $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
    }
    return $dates;
  }
  
/**
 * Helper: search a 2-dim array for key, return value
 * 
 */
  function cb_search_array($value, $key, $array) {
   foreach ($array as $k => $val) {
       if ($val[$key] == $value) {
           return $k;
       }
   }
   return null;
  }
/**
 * Helper: Convert object to array.  
 * 
 */

 function cb_object_to_array($object) {
  $array = json_decode(json_encode($object), true);
  return $array;
    // if (is_array($data) || is_object($data))
    // {
    //     $result = array();
    //     foreach ($data as $key => $value)
    //     {
    //         $result[$key] = cb_object_to_array($value);
    //     }
    //     return $result;
    // }
    // return $data;
} 
/**
 * Helper: Check if Thumbmail exists, if so, return it.  
 * 
 */
  function cb_get_thumb( $post_id ) {
    if ( has_post_thumbnail( $post_id ) ) {
      $thumb = get_the_post_thumbnail( $post_id, 'thumbnail' );
    } else {
      $thumb = "";
    }
    return $thumb;
  }
/**
 * Convert to timestamp.  
 * 
 */
  function cb_convert_to_timestamp( $date ) {
    return strtotime($date);
  }
  /**
   * Check if string is valid date.
   *
   * @since    0.3
   *
   * @param     $string string to check
   * @param     Bool
   */
  function cb_is_valid_date( $string ) {
    return (bool)strtotime( $string );
  }
  /**
   * Create page.
   *
   * @since    0.2
   *
   * @param     $title page title
   * @param     $option_name the name of the option in the settings-pages array
   * @return    Page ID
   */
  function cb_create_page( $title, $option_name ) {

    $option_page = get_option ( 'commons-booking-settings-pages' );
    $option = $option_page[ $option_name ];

    if ( !empty ( $option ) ) {

      return $option;

    } else {

      $my_page = array(
        'post_title'    =>  $title,
        'post_content'  => '',
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_author'   => 1
      );
      // Insert the post into the database
      $id = wp_insert_post( $my_page );
      return $id;
 
    }
   
  }
  /**
   * Get WP Custom field
   *
   * @since    0.9.2.1
   *
   * @param     $key
   * @param     $echo
   * @return    Meta field
   */
function cb_get_custom_field( $key, $echo = FALSE ) {
  global $post;
  $custom_field = get_post_meta($post->ID, $key, true);

  if (! $custom_field ) {
    $custom_field = "";
  }

  if ($echo == FALSE) return $custom_field;
  echo $custom_field;
}


?>