<?php

/**
 * Commons Booking Items
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @author    Christian Wenzel <christian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * This class includes display of items
 * *
 * @package Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 *
 */

class Commons_Booking_Public_Items {

    public $plugin_slug;

    /**
     *
     * @since   0.0.1
     *
     */
    const VERSION = '0.0.1';
    public $items;

    public function __construct() {

      // get timeframe data 
      $this->data = new Commons_Booking_Data;
      $this->plugin_slug = 'commons-booking';

      // get list of items
      $args = array( 'posts_per_page' => 10, 'post_type' => 'cb_items', 'orderby' => 'title', 'order' => 'DESC' );
      $this->the_query = new WP_Query( $args );
    }

    public function get_Items() {

      $content = array();

      $query = $this->the_query;

      if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
          
          $query->the_post();
          $item_id = get_the_ID();

          $content [$item_id]['permalink'] =  get_the_permalink();
          $content [$item_id]['title'] =  the_title();
          if ( has_post_thumbnail( $item_id ) ) { 
           $content [$item_id]['thumb'] = get_the_post_thumbnail( $item_id, 'thumbnail' );
         }
          $content [$item_id]['description'] =  get_post_meta( get_the_ID(), 'commons-booking_item_descr', true );

          // echo ( '<div class="cb-list-item">' );          

          // commons_booking_get_template_part( 'items', 'list'); // list template

          $timeframes = $this->data->get_timeframes( $item_id  );

          if ( $timeframes ) {
            // echo ( '<ul class="cb-list-item-timeframe">');
            foreach ( $timeframes as $tf ) {
              $location = $this->data->get_location ( $tf ['location_id'] );

              $content [$item_id]['location'][$tf ['location_id']] = $location;


              // include (commons_booking_get_template_part( 'items', 'list-timeframes', FALSE )); 

            } // end foreach
            // echo ( '</ul>');
          } 
       return $content; 
        }
      } else {
        echo __( ' Sorry, nothing found.');
      }
      /* Restore original Post Data */
      wp_reset_postdata();
    }
    public function items_render() {

    }
  }
?>