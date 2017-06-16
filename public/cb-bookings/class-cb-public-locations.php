<?php

/**
 * Commons Booking 
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @author    Christian Wenzel <christian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * This class includes display of locations
 * *
 * @package   Commons_Booking_locations
 * @author    Annesley Newholm <annesley_newholm@yahoo.it>
 * @since     0.9.2.5
 *
 */

class CB_Public_Locations {

    public $items;

    public function __construct() {

      // get timeframe data 
      $this->data = new CB_Data;

      $this->defaults = array( 
        'p' => '',
        'cat' => '',
        'posts_per_page' => 1000,         // Can use -1 for all but not backward compatable
        'post_type' => 'cb_locations',
        // 'orderby' => 'title',          // Showing on a map, order not important
        // 'order' => 'DESC',
      );
    }

  /**
   * Get locations
   * 
   * @since     0.9.2.5
   * @param     mixed
   * @return    array
   */
  public function get_Locations( $the_query ) {

    $content = array();

    if ( $the_query->have_posts() ) {
      while ( $the_query->have_posts() ) {
        
        $the_query->the_post();
        $item_id = get_the_ID();
        array_push( $content, $item_id );
      }
    } else {
      return __( ' Sorry, nothing found.');
    }
     wp_reset_postdata();
      return $content; 
    }

  /**
   * Merge supplied arguments with defaults
   * 
   * @TODO move this to main file so other classes can use it.
   *
   * @since     0.9.2.5
   * @param     array
   * @return    array
   */
  public function merge_args( $args = array() ){

    //sort out whether defaults were supplied or just the array of search values
    if( empty( $args ) ){
      $array = $this->defaults;
      
      }else{
        $array = array_merge($this->defaults, $args);
      }
      $array = array_filter( $array, 'strlen' ); // remove empty keys
      return ($array);
    }

  /**
   * Output the location list
   * using foreach render_location_list()
   * 
   * @since     0.9.2.5
   * @param     args array
   * @return    array
   */
  public function output( $args = array() ) {
   
    $content = '';
    $queryargs = $this->merge_args($args);  
    
    // Header and map
    // TODO: Implement this as a shortcode
    // $content .= "[commons-booking-map]";
    // This commons-booking-map will, by default, plot all hCard's found in the page
    $content .= '<div class="commons-init commons-booking-hcard-map">' . __('map loading') . '...</div>';

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $p = array( 'paged' => $paged );

    $thequery = array_merge_recursive( $queryargs, $p );
    
    $query = new WP_Query( $thequery );
    wp_reset_postdata();
    $location_ids = $this->get_Locations( $query );

    if ( is_array ($location_ids) ) { // if result

      foreach ($location_ids as $location_id) {
        $content .= $this->data->render_location_list( $location_id );
      }

      } else {
        $content = __('No locations found', 'commons-booking');
      }

      $pagination = $this->item_pagination($query ); 

    return $content . $pagination; 
  }

  public function item_pagination( $query ) {
    $next = get_next_posts_link( __('Next locations', 'commons-booking'), $query->max_num_pages );
    $prev = get_previous_posts_link( __('Previous locations', 'commons-booking') , $query->max_num_pages );
    return $prev . $next;
  }
}
?>