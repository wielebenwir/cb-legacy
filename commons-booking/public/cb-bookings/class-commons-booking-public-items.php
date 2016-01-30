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

class Commons_Booking_Public_Items {

    public $items;

    public function __construct() {

      // get timeframe data 
      $this->data = new Commons_Booking_Data;

      $this->defaults = array( 
        'p' => '',
        'cat' => '',
        'posts_per_page' => 10, 
        'post_type' => 'cb_items', 
        'orderby' => 'title', 
        'order' => 'DESC'
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

        $content [$item_id]['permalink'] =  get_the_permalink();
        $content [$item_id]['title'] =  get_the_title();
        if ( has_post_thumbnail( $item_id ) ) {  $content [$item_id]['thumb'] = get_the_post_thumbnail( $item_id, 'thumbnail' ); }
        $content [$item_id]['description'] =  get_post_meta( get_the_ID(), 'commons-booking_item_descr', true );

        $timeframes = $this->data->get_timeframes( $item_id  );

        if ( $timeframes ) {
          foreach ( $timeframes as $tf ) {
            $location = $this->data->get_location ( $tf ['location_id'] );

            $content [$item_id]['location'][$tf ['location_id']] = $location;
            $content [$item_id]['location'][$tf ['location_id']]['date_start'] = $tf ['date_start'];
            $content [$item_id]['location'][$tf ['location_id']]['date_end'] = $tf ['date_end'];
          } // end foreach
        }
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
   
    $queryargs = $this->merge_args($args);  
    $query = new WP_Query( $queryargs );
    $items = $this->get_Items( $query );


    if ( is_array ($items) ) { // if result
      ob_start();
      include ( commons_booking_get_template_part( 'items', 'list', false ) );
      return ob_get_clean();
      }

  }
}
?>