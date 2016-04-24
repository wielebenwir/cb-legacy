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
 * This class includes display of items
 * *
 * @package   Commons_Booking_items
 * @author    Florian Egermann <florian@wielebenwir.de>
 *
 */

class CB_Public_Items {

    public $items;

    public function __construct() {

      // get timeframe data 
      $this->data = new CB_Data;

      $this->defaults = array( 
        'p' => '',
        'cat' => '',
        'posts_per_page' => 10, 
        'post_type' => 'cb_items', 
        'orderby' => 'title', 
        'order' => 'DESC',
      );
    }

  /**
   * Get items
   * 
   * @since     0.4.5
   * @param     mixed
   * @return    array
   */
  public function get_Items( $the_query ) {

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
   * @since     0.4.5
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
   * Output the item list
   * 
   * @since     0.4.5
   * @param     args array
   * @return    array
   */
  public function output( $args = array() ) {
   
    $content = '';
    $queryargs = $this->merge_args($args);  

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $p = array( 'paged' => $paged );

    $thequery = array_merge_recursive( $queryargs, $p );
    
    $query = new WP_Query( $thequery );
    wp_reset_postdata();
    $item_ids = $this->get_Items( $query );

    if ( is_array ($item_ids) ) { // if result

      foreach ($item_ids as $item_id) {
        $content .= $this->data->render_item_list( $item_id );
      }

      } else {
        $content = __('No items found', 'commons-booking');
      }

      $pagination = $this->item_pagination($query ); 

    return $content . $pagination; 
  }

  public function item_pagination( $query ) {
    $next = get_next_posts_link( __('Next items', 'commons-booking'), $query->max_num_pages );
    $prev = get_previous_posts_link( __('Previous items', 'commons-booking') , $query->max_num_pages );
    return $prev . $next;
  }
}
?>