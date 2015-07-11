<?php
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Registers all metaboxes for the locations edit screen
 *
 * @package Commons_Booking_Items
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

class Commons_Booking_Locations_Metaboxes extends Commons_Booking {

  /**
   * Hook into the appropriate actions when the class is constructed.
   */
  public function __construct() {

  }

  /**
   * Set up the meta boxes
   *
   * @param WP_Post $post The post object.
   */

  public function add_metabox ( array $meta_boxes ) {

    $items = new Commons_Booking_Items();

    $meta_boxes[ 'cb_location_metabox_adress' ] = array(
      'id' => 'cb_location_metabox_adress',
      'title' => __( 'Address', parent::$plugin_slug ),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left   
      'fields' => array(        
        array(
          'name' => __( 'Street', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_adress_street',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'City', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_adress_city',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'Zip Code', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_adress_zip',
          'type' => 'text',
        ),          
        array(
          'name' => __( 'Country', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_adress_country',
          'type' => 'text',
        ),  
      ),      
    );

    $meta_boxes[ 'cb_location_metabox_contactinfo' ] = array(
      'id' => 'cb_location_metabox_contactinfo',
      'title' => __( 'Contact Information', parent::$plugin_slug ),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(    
        array(
          'name' => __( 'Phone Number, Email, ...', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_contactinfo_text',
          'type' => 'textarea',
        ),        
        array(
          'name' => __( 'Hide contact information until user has confirmed the booking.', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_contactinfo_hide',
          'type' => 'checkbox',
        ),  
      ),              
    );      
    $meta_boxes[ 'cb_location_metabox_openinghours' ] = array(
      'id' => 'cb_location_metabox_openinghours',
      'title' => __( 'Opening hours', parent::$plugin_slug ),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(    
        array(
          'name' => __( 'Enter the opening hours here. E.g.: Mon-Fri, 8:00 - 18:00', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_openinghours',
          'type' => 'textarea',
        ),         
      ),              
    );    

    $meta_boxes[ 'cb_location_metabox_closeddays' ] = array(
      'id' => 'cb_location_metabox_closeddays',
      'title' => __( 'Closed Days', parent::$plugin_slug ),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(          
        array(
          'name' => __( 'Location is closed on the following days, booking is prohibited. ', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_closeddays',
          'type'    => 'multicheck',
          'options' => array(
              '1' => __( 'Monday', parent::$plugin_slug ),
              '2' => __( 'Tuesday', parent::$plugin_slug ),
              '3' => __( 'Wednesday', parent::$plugin_slug ),
              '4' => __( 'Thursday', parent::$plugin_slug ),
              '5' => __( 'Friday', parent::$plugin_slug ),
              '6' => __( 'Saturday', parent::$plugin_slug ),
              '7' => __( 'Sunday', parent::$plugin_slug ),
              ),
          ),        
      ),              
    );


    return $meta_boxes;
  }
}