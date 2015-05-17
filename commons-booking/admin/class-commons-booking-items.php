<?php
/**
 * Items
 *
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */


/**
 *
 * @package   Commons_Booking
 * @subpackage  Commons_Booking/admin
 * @author    Florian Egermann <florian@macht-medien.de>
 */

class Commons_Booking_Items {

  /**
   * Returns a post object of portfolio posts
   *
   * @param   array     $params       An array of optional parameters
   *              types       An array of portfolio item type slugs
   *              industries    An array of portfolio industry slugs
   *              quantity    Number of posts to return
   *
   * @return  object    A post object
   */
  public function get_Items( ) {

    $return = '';

    $args['post_type']    = 'cb_items';
    $args['post_status']  = 'publish';
    // $args['order_by']     = $params['order'];
    // $args['posts_per_page'] = $params['quantity'];

    $query = new WP_Query( $args );

    if ( 0 == $query->found_posts ) {

      $return = __( 'None found' ) ;

    } else {

      $return = $query;

    }

    return $return;

  }

}

?>