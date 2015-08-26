<?php
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * This class handles the display of calendar & timeframes
 *
 * @package Commons_Booking_Data
 * @author    Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking_Data {

  public $timeframe_id;
  public $item_id;
  public $date_start;
  public $date_end;
  public $bookings;

/**
 * Constructor.
 *
 * @param $timeframe_id 
 * @param $item_id 
 * @param $date_start
 * @param $date_end
 * @param $currentdate
 *
 *
 */
  public function __construct() {
    $this->prefix = 'commons-booking';
    $this->daystoshow = 30; //@TODO make a backend setting
    $this->current_date = date('Y-m-d');
}


/**
 * Gather all necessary data from databases for timeframe render
 *
 */
  public function gather_data() {
    $item_id = $this->item_id;
    $this->timeframes = $this->get_timeframes( $item_id );
    $this->codes = $this->get_codes();
    $this->dates = $this->get_dates();

  } 

/**
 * Get settings from backend. Return either full array or specified setting
 *
 *@param setting_page: name of the page (cmb metabox name)
 *@param (optional) name of the setting
 *
 *@return array
 */
  public function get_settings( $setting_page, $setting_name = "") {
    global $wpdb;
    $page = get_option( $this->prefix . '-settings-' .$setting_page ); 
    if ( $setting_name ) {
     return esc_attr( $page [ $this->prefix . '_'. $setting_name ] );
    } else {
      return esc_attr( $page );
    }
  }

/**
 * Get a list of all dates within the defind range. 
 *
 * @return array
 */
  public function get_dates() {
    $dates = array($this->date_start);
    while(end($dates) < $this->date_end){
        $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
    }
    return $dates;
  }


/**
 * Get all entries from the codes DB. Ignore dates earlier than 30 days 
 *
 * @return array
 */
  public function get_codes() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_codes';
    $dateRangeStart = date('Y-m-d', strtotime( '-30 days' )); // currentdate - 30 days
    $codesDB = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE item_id = %s AND booking_date > $dateRangeStart", $this->item_id ), ARRAY_A); // get dates from db
    return $codesDB;
  } 
/**
 * Get timeframes by item_id
 * 
 *
 * @return array
 */
  public function get_timeframes( $item_id, $date_start = '' ) {

    $return = '';

    if ( empty($date_start) ) {
      $date_start = $this->current_date;
    }

    if ( $item_id ) {
      global $wpdb;
      // @TODO: Fix start date not being honored by function -> maybe change data format
      $table_name = $wpdb->prefix . 'cb_timeframes'; 
      $sql = $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE item_id = %s and date_end > %s ORDER BY date_start ASC', $item_id, $date_start );
      $this->timeframes = $wpdb->get_results($sql, ARRAY_A);

      if ( !empty( $this->timeframes) ) {
          return $this->timeframes;
        } else { 
          return FALSE;
        } 
    } else {
      return FALSE;
    }
  }




/**
 * Get Location & metadata 
 *
 *@param $id location id
 *
 *@return array 
 *
 */

  public function get_location ( $id ) {
  
    if ( $id ) {
      $location = array ( 
        'name' => get_the_title( $id ),
        'id' => $id ,
        'address' => array ( 
          'street' => get_post_meta( $id, $this->prefix . '_location_adress_street', true ),
          'city' => get_post_meta( $id, $this->prefix . '_location_adress_city', true ),
          'zip' => get_post_meta( $id, $this->prefix . '_location_adress_zip', true ),
          'country' => get_post_meta( $id, $this->prefix . '_location_adress_country', true ),
        ),
        'contact' => get_post_meta( $id, $this->prefix . '_location_contactinfo_text', true ),
        'contact_hide' => get_post_meta( $id, $this->prefix . '_location_contactinfo_hide', true ),
        'closed_days' => get_post_meta( $id, $this->prefix . '_location_closeddays', true ),
        'openinghours' => get_post_meta( $id, $this->prefix . '_location_openinghours', true ),
        );
      return $location;
    } else {
      return false;
    }
  }

/**
 * Get Item contents outside the loop
 *
 *@param $id item id
 *
 *@return array 
 *
 */
  public function get_item ( $id ) {
  
    global $wpdb;

    if ( $id ) {
      $item = get_post($id , ARRAY_A);
      return $item;
    } else {
      return false;
    }
  }
/**
 * Get all items
 *
 *
 * @return array 
 *
 */
 public function get_items( ) {

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

/**
 * Get User info and meta outside the loop
 *
 *@param $id user id
 *
 *@return array 
 *
 */
  public function get_user( $id ) {
  
    if ( $id ) {
      $userdb = get_user_by( 'id', $id );
 
      $user = array ( 
        'id' => $id ,
        'name' => $userdb->first_name . ' ' .  $userdb->last_name,
        'login' => $userdb->userdb_login,
        'email' => $userdb->user_email,
        'phone' => get_user_meta( $id, 'phone', true ),
        'address' => get_user_meta( $id, 'address', true ),
        );
      return $user;
    } else {
      return false;
    }
  }

/**
 * Single item, all calendars.  
 *
 *@param $id item id
 *
*/

  public function render_item_single_timeframes( $item_id  ) {


    $item_descr_short = get_post_meta( $item_id, 'commons-booking_item_descr', TRUE  );
    $item_descr_full = get_the_content();

    include (commons_booking_get_template_part( 'item_single', 'item_info', FALSE )); // include the item info template

    $this->item_id = $item_id;

    // get a list of all dates that should be shown (config setting)
    $this->date_range_start = date('Y-m-d');
    $this->date_range_end = date('Y-m-d', strtotime ( '+ ' .$this->daystoshow . 'days' ));

    // get Data
    $this->gather_data();


    $timeframes = $this->timeframes;
    $codes = $this->codes;

    if ($timeframes ) {
      foreach ( $timeframes as $tf) {
        if ( $tf['date_start'] <= $this->date_range_end ) { // check if start date is within the date range          
          $location = $this->get_location ( $tf['location_id'] );
          $this->render_item_single_timeframe_calendar( $tf, $codes, $location, $item_id );     
        }
      }
    } else {
      echo __( 'This item can´t be booked at the moment.', $this->prefix );
    }
  }

/**
 * List of items, list of calendar entries.  
 *
 *@param $id item id
 *
*/

  public function show_item_list_timeframes( $item_id  ) {

    $this->item_id = $item_id;

    // get a list of all dates that should be shown (config setting)
    $this->date_range_start = date('Y-m-d');
    $this->date_range_end = date('Y-m-d', strtotime ( '+ ' .$this->daystoshow . 'days' ));

    // get Data
    $this->gather_data();


    $tf = $this->timeframes;

    foreach ( $this->timeframes as $tf) {
      if ( $tf['date_start'] <= $this->date_range_end ) { // check if start date is within the date range
        
        $location = $this->get_location ( $tf['location_id'] );
        $this->render_item_single_timeframe_list( $tf, $codes, $location, $item_id );
      
      }
    }

  }


  public function render_item_single_timeframe_list( $tf, $location, $item_id ) {

    $timeframe_comment = $tf['timeframe_title'];
    $timeframe_date = date_i18n( get_option( 'date_format' ), strtotime( $tf['date_start'] ) ) . ' - ' . date_i18n( get_option( 'date_format' ), strtotime( $tf['date_end'] ) );

    echo ( '<div class="cb-timeframe-list" data-tfid="'. $tf['id'] .'" data-itemid="'. $item_id . '"' .'" data-locid="'. $tf['location_id'] . '">' );

  }

/**
 * Item Single: Render the timeframe (item info, location, calendar)
 *
 * @param $tf       array   timeframe data 
 * @param $codes    array   of codes
 * @param $location array location data
 * @param $item_id  int   id of the item
 */
  public function render_item_single_timeframe_calendar( $tf, $codes, $location, $item_id ) {

    $booked = new Commons_Booking_Booking;
    $booked_days = $booked->get_booked_days( $item_id );


    $timeframe_comment = $tf['timeframe_title'];
    $timeframe_date = date_i18n( get_option( 'date_format' ), strtotime( $tf['date_start'] ) ) . ' - ' . date_i18n( get_option( 'date_format' ), strtotime( $tf['date_end'] ) );

    echo ( '<div class="cb-timeframe" data-tfid="'. $tf['id'] .'" data-itemid="'. $item_id . '"' .'" data-locid="'. $tf['location_id'] . '">' );

    
    include (commons_booking_get_template_part( 'item_single', 'location_detailed', FALSE )); // include the location template

    $start = strtotime( $tf['date_start'] );
    $counter = $start;
    $last = min ( strtotime( $tf['date_end'] ), strtotime( $this->date_range_end ) ); // must be within range

    $target_page_id = $this->get_settings( 'pages', 'bookingconfirm_page_select' ); // get setting for bookings review page (id)
    $this->target_url = get_the_permalink( $target_page_id ); // get url from id

    echo (' <div id ="timeframe_' . $tf[ 'id' ] .'" class="cb_timeframe_form">');
    echo ('<ul class="cb-calendar">');

    while( $counter <= $last ) { // loop through days
      $display_day = date_i18n ('D', $counter );
      $display_date = date_i18n ('j.n.', $counter ); 
      $code = $this->get_code_by_date ( $counter, $codes ); 

      $class= $this->set_day_status( $counter, $location, $booked_days );

      include (commons_booking_get_template_part( 'calendar', 'cell', FALSE )); // include the template

      $counter = strtotime('+1 day', $counter); // counter
    }
    echo ('</ul>' );
    echo ( '</div>' );
    echo ( '</div>');
  }

/**
 * Get code by Date
 *
 * @param $date single date
 * @param $codes array of codes
 * @return string / false
 */
  private function get_code_by_date ( $date, $codes ) {
      $needle = ( search_array( date('Y-m-d', $date ), 'booking_date', $codes ) );
      if ( $needle ) {
         $code = ( $codes[ $needle ][ 'bookingcode' ] ); 
         return $code;    
      } else {
        return false;
      }

  }

/**
 * Get status of the day
 *
 * @param $date         single date
 * @param $location     array
 * @param $booked_days  array
 *
 * @return array statuses
 */
  private function set_day_status( $date, $location, $booked_days ) {
    $status = '';

    $timestamp = array_map( 'convert_to_timestamp', $booked_days);

    // first: check if the date is in the locations´ closed days array
    if ( ( is_array( $location[ 'closed_days'] )) &&  ( in_array( date( 'N', $date ), $location[ 'closed_days'] ))) {  
       $status = 'closed';
    // second: check if day is booked
    } elseif ( is_array( $timestamp) && in_array( $date, $timestamp )) {
        $status = 'booked'; 
    // you may book
    } else {
      $status = 'bookable';
    }
    return $status;

  }

/**
 * Include the booking bar
 *
 */
  public function show_booking_bar() {
    include (commons_booking_get_template_part( 'calendar', 'bookingbar', FALSE )); // include the template
  }
}
?>