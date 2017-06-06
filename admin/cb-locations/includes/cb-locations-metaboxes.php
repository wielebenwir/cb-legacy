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

    $myitems = new CB_Data;
    $items = $myitems->get_items();

    $meta_boxes[ 'cb_location_metabox_adress' ] = array(
      'id' => 'cb_location_metabox_adress',
      'title' => __( 'Address', 'commons-booking'),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left   
      'fields' => array(        
        array(
          'name' => __('Coordinates', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_coordinates',
          'type' => 'leaflet_map',
          'desc' => '<b>' . __('Search first', 'commons-booking') . '</b>, ' . __('then Drag the marker after finding the right spot to set the exact coordinates', 'commons-booking'),
          'attributes' => array(
            'initial_coordinates' => [
                // TODO: detect the users location initially
                'lat' => 61.9241, // Go Finland!
                'lng' => 25.7482  // Go Finland!
            ],
            'initial_zoom'        => 4,  // Zoomlevel when there's no coordinates set,
            'default_zoom'        => 12, // Zoomlevel after the coordinates have been set & page saved
            // For these extra attributes, please consult Leaflet [documentation](http://leafletjs.com/reference-1.0.0.html)
            /*
            'tilelayer'           => 'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            'searchbox_position'  => 'topright'; // topright, bottomright, topleft, bottomleft,
            'search'              => __( 'Search...', 'commons-booking' ),
            'not_found'           => __( 'Not found', 'commons-booking' ),
            */
          )
        ),
        array(
          'name' => __( 'Door colour', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_door_colour',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'Floor, apartment', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_floor_apartment',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'House / building number', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_house_number',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'Street', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_street',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'City', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_city',
          'type' => 'text',
        ),        
        array(
          'name' => __( 'Zip Code', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_zip',
          'type' => 'text',
        ),          
        array(
          'name' => __( 'Country', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_adress_country',
          'type' => 'text',
        ),  
      ),      
    );

    $meta_boxes[ 'cb_item_metabox_icon' ] = array(
      'id' => 'cb_location_metabox_icon',
      'title' => __( 'Icon', parent::$plugin_slug ),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'side',
      'priority' => 'high',
      'show_names' => false,
      'fields' => array(        
        array(
          'name' => __( 'Icon', parent::$plugin_slug ),
          'id' => parent::$plugin_slug . '_location_icon',
          'type' => 'icon',
          'desc' => 'Used in Maps.',
          'options' => array(
            'paths' => array( 
              COMMONSBOOKING_PATH . 'public/assets/images',
              wp_upload_dir()['path'], // Since 2.0.0
              'http://www.flaticon.com/packs/holiday-travelling-3',
            ),
          ),
        ),
      ),      
    );

    $meta_boxes[ 'cb_location_metabox_contactinfo' ] = array(
      'id' => 'cb_location_metabox_contactinfo',
      'title' => __( 'Contact Information', 'commons-booking'),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(    
        array(
          'name' => __( 'Phone Number, Email, ...', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_contactinfo_text',
          'type' => 'textarea',
        ),        
        array(
          'name' => __( 'Hide contact information until user has confirmed the booking.', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_contactinfo_hide',
          'type' => 'checkbox',
        ),  
      ),              
    );   
    
    $meta_boxes[ 'cb_location_metabox_openinghours' ] = array(
      'id' => 'cb_location_metabox_openinghours',
      'title' => __( 'Opening hours', 'commons-booking'),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(    
        array(
          'name' => __( 'Enter the opening hours here. E.g.: Mon-Fri, 8:00 - 18:00', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_openinghours',
          'type' => 'textarea',
        ),         
      ),              
    );    

    $meta_boxes[ 'cb_location_metabox_closeddays' ] = array(
      'id' => 'cb_location_metabox_closeddays',
      'title' => __( 'Closed Days', 'commons-booking'),
      'object_types' => array( 'cb_locations', ), // Post type
      'context' => 'normal',
      'priority' => 'high',
      'show_names' => true, // Show field names on the left 
      'fields' => array(          
        array(
          'name' => __( 'Location is closed on the following days, booking is prohibited. ', 'commons-booking'),
          'id' => parent::$plugin_slug . '_location_closeddays',
          'type'    => 'multicheck',
          'options' => array(
              '1' => __( 'Monday', 'commons-booking'),
              '2' => __( 'Tuesday', 'commons-booking'),
              '3' => __( 'Wednesday', 'commons-booking'),
              '4' => __( 'Thursday', 'commons-booking'),
              '5' => __( 'Friday', 'commons-booking'),
              '6' => __( 'Saturday', 'commons-booking'),
              '7' => __( 'Sunday', 'commons-booking'),
              ),
          ),        
      ),              
    );

    return $meta_boxes;
  }
}
