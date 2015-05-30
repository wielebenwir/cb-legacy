<?php
/**
 * Define the custom post types:
 * items, locations
 *
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */


/**
 * 1. Items
 */

class Item_CPT extends CPT_Core {

    /**
     * Register Custom Post Types. See documentation in CPT_Core, and in wp-includes/post.php
     */
    public function __construct() {

        // Register this cpt
        // First parameter should be an array with Singular, Plural, and Registered name
        parent::__construct(
            array( __( 'Item', 'your-text-domain' ), __( 'Items', 'your-text-domain' ), 'cb_items' ),
            array( 'supports' => array( 'title', 'editor', 'thumbnail' ), 'show_in_menu' => 'cb_menu')

        );

    }

    /**
     * Registers admin columns to display. Hooked in via CPT_Core.
     * @since  0.1.0
     * @param  array  $columns Array of registered column names/labels
     * @return array           Modified array
     */
    public function columns( $columns ) {
        $new_column = array(
            'image' => sprintf( __( '%s image', 'your-text-domain' ), $this->post_type( 'singular' ) ),
        );
        return array_merge( $new_column, $columns );
    }

    /**
     * Handles admin column display. Hooked in via CPT_Core.
     * @since  0.1.0
     * @param  array  $column Array of registered column names
     */
    public function columns_display( $column, $post_id ) {
        switch ( $column ) {
            case 'image':
                the_post_thumbnail();
                break;
        }
    }

}


/**
 * 2. Locations
 */

class Locations_CPT extends CPT_Core {

    /**
     * Register Custom Post Types. See documentation in CPT_Core, and in wp-includes/post.php
     */
    public function __construct() {

        // Register this cpt
        // First parameter should be an array with Singular, Plural, and Registered name
        parent::__construct(
            array( __( 'Location', 'your-text-domain' ), __( 'locations', 'your-text-domain' ), 'cb_locations' ),
            array( 'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ), 'show_in_menu' => 'cb_menu')

        );

    }

}


?>