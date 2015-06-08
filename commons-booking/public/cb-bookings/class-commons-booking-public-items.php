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

    /**
     *
     * @since   0.0.1
     *
     */
    const VERSION = '0.0.1';
    public $items;

    public function __construct() {
      // get list of items
      $args = array( 'posts_per_page' => -1, 'post_type' => 'cb_items');
      // $this->items = get_posts( $args ); 
      $this->the_query = new WP_Query( $args );
    }

    public function show() {

      $query = $this->the_query;
      // The Loop
      if ( $query->have_posts() ) {
        echo '<ul>';
        while ( $query->have_posts() ) {
          $query->the_post();
          // echo '<li>' . get_the_title() . '</li>';
          commons_booking_get_template_part( 'items', 'list' );
        }
        echo '</ul>';
      } else {
        // no posts found
      }
      /* Restore original Post Data */
      wp_reset_postdata();

      // foreach ($this->items as $post) {
      //   // var_dump($item);
      //   // echo get_the_title($item->ID);
      //   setup_postdata( $post );

      //   commons_booking_get_template_part( 'items', 'list' );
      //   echo ("<hr>");
      //   // var_dump($item);
      // }
      // wp_reset_postdata();

    }
  }
?>