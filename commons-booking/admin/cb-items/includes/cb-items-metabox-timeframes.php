<?php

/*
 * Registers a metabox for display of timeframe entries on the item edit screen
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

  public function __construct() {

    add_action('add_meta_boxes', array($this, 'cb_items_add_meta_box'));
    add_action('save_post', array($this, 'save'));
    add_action('the_content', array($this, 'custom_message'));
  }

  /**
   * Adds the meta box container.
   */
  public function cb_items_add_meta_box( $post_type ) {
    $post_types = array( 'cb_items' );

    //limit meta box to certain post types
    if (in_array($post_type, $post_types)) {
      add_meta_box('cs-meta',
      __('Timeframes'),
      array($this, 'cb_items_meta_box_function'),
      $post_type,
      'normal',
      'high');
    }
  }

  /**
   * Render Meta Box content.
   *
   * @param WP_Post $post The post object.
   */

  public function cb_items_meta_box_function($post) {

    $timeframes = new Commons_Booking_Timeframes( $post->ID );
    echo ( '<a class="add-new-h2" href="'. get_admin_url(get_current_blog_id()) . 'admin.php?page=timeframes_form">' . __('Add new Timeframe', 'cb_timeframes_table') . '</a>' );
    $timeframes->render_timeframes();

  }

}