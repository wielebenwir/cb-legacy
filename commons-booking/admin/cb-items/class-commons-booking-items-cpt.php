<?php
/**
 * Define the custom post types
 * items
 *
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

class Commons_Booking_Items_CPT extends CPT_Core {

    /**
     * Register the Item post type
     */
    public function __construct( $slug ) {

        $this->slug = $slug;

        // Register this cpt
        parent::__construct(
            array( 
                __( 'Item', $this->slug ), 
                __( 'Items', $this->slug ), 
                'cb_items' 
                ),
            array( 
                'supports' => array( 'title', 'editor', 'thumbnail' ), 
                'show_in_menu' => true,
                'menu_position' => 31,
                'menu_icon' => 'dashicons-carrot'
                )
        );

    }
    /**
     * Registers the cb_category taxonomy. Hooked in via Taxomony_Core.
     * @since  0.1.0
     */
    public function register_taxonomy() {

        $cb_item_cat_names = array(
            __( 'Category', $this->slug ),       // Singular
            __( 'Categories', $this->slug ),     // Plural
            'cb_item_cat'                                // Registered name
        );

        $cb_item_cats = register_via_taxonomy_core( $cb_item_cat_names, array(), array( 'cb_items' ) );
    }


    /**
     * Registers admin columns to display. Hooked in via CPT_Core.
     * @since  0.1.0
     * @param  array  $columns Array of registered column names/labels
     * @return array           Modified array
     */
    public function columns( $columns ) {
        $new_column = array(
            'image' => sprintf( __( '%s image', $this->slug ), $this->post_type( 'singular' ) ),
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

?>