<?php

/**
 * Commons Booking Helpers
 * 
 * Helper functions
 *    Dropdown of Pages
 *    Replace Template Tags    
 *
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de>
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
        $string = str_replace('{'.strtoupper($key).'}', $value, $string);
    }
    return $string;
  }


?>