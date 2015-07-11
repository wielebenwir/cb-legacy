<?php

/**
 * Commons Booking Helpers
 * 
 * Helper functions
 *    Dropdown of Pages
 *    Replace Template Tags    
 *    get_dates_between
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

function pages_dropdown() {
  // dropdown for page select
  $pages = get_pages();
  $dropdown = array();
  
  foreach ( $pages as $page ) {
    $dropdown[$page->ID] = $page->post_title;
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
 function replace_template_tags( $string, $tags_array ) {
    foreach($tags_array as $key => $value){
        $string = str_replace('{{'.strtoupper($key).'}}', $value, $string);
    }
    return $string;
  }

/**
 * Get a list of all dates within the defind range. 
 *
 * @return array dates
 */
 function get_dates_between( $date_start, $date_end ) {
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
  function search_array($value, $key, $array) {
   foreach ($array as $k => $val) {
       if ($val[$key] == $value) {
           return $k;
       }
   }
   return null;
  }
/**
 * Helper: Check if Thumbmail exists, if so, return it.  
 * 
 */
  function get_thumb( $post_id ) {
    if ( has_post_thumbnail( $post_id ) ) {
      $thumb = get_the_post_thumbnail( $post_id, 'thumbnail' );
    } else {
      $thumb = "";
    }
  }
/**
 * Convert to timestamp.  
 * 
 */
  function convert_to_timestamp( $date ) {
    return strtotime($date);
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
  function create_page( $title, $option_name ) {

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



?>