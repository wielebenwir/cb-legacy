<?php

/*
 * Registers a metabox for display of timeframe entries on the item edit screen
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

class Commons_Booking_Items_Metabox extends Commons_Booking {

  /**
   * Hook into the appropriate actions when the class is constructed.
   */
  public function __construct() {

    // timeframe meta box & messages
    add_action('add_meta_boxes', array($this, 'cb_items_add_timeframe_meta_box'));
    add_action('the_content', array($this, 'custom_message'));

    // description meta box   
    add_filter( 'cmb2_meta_boxes', array( $this, 'cb_item_descr_metaboxes' ) );

  }

  /**
   * Adds the timeframes meta box container.
   */
  public function cb_items_add_timeframe_meta_box( $post_type ) {
    $post_types = array( 'cb_items' );

    //limit meta box to certain post types
    if (in_array($post_type, $post_types)) {
      add_meta_box('cs-meta',
      __('Timeframes'),
      array($this, 'cb_items_timeframe_meta_box_function'),
      $post_type,
      'normal',
      'high');      
    }
  }

  /**
   * Render timeframes Meta Box content.
   *
   * @param WP_Post $post The post object.
   */

  public function cb_items_timeframe_meta_box_function($post) {

    $timeframes = new Commons_Booking_Timeframes_List( $post->ID );
    $timeframes->render_timeframes();

  }

  /**
   * Set up the description meta box.
   *
   * @param WP_Post $post The post object.
   */

  public function cb_item_descr_metaboxes( array $meta_boxes ) {

    $meta_boxes[ 'cb_item_metabox_descr' ] = array(
      'id' => 'cb_item_metabox_descr',
      'title' => __( 'Short description of the item, displayed in the list.', parent::$plugin_slug ),
      'object_types' => array( 'cb_items', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left   
      'fields' => array(        
        array(
          'name' => __( 'Short description', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_item_descr',
          'type' => 'textarea',
        ),
      ),      
    );
    return $meta_boxes;
  }
}