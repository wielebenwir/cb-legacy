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
class CB_Data {

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

    // $this->settings = new CB_Admin_Settings();

    $this->prefix = 'commons-booking';
    $this->settings = new CB_Admin_Settings;
    // from settings
    $this->daystoshow = $this->settings->get_settings( 'bookings', 'bookingsettings_daystoshow' );    
    $this->render_daynames = $this->settings->get_settings( 'bookings', 'bookingsettings_calendar_render_daynames' );
    $this->target_url = $this->settings->get_settings( 'pages', 'booking_review_page_select' );
    $this->current_date = current_time('Y-m-d');

    $this->codes = $this->get_codes();

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
 * Get a list of all dates within the defind range. @TODO retire this function 
 *
 * @return array
 */
  public function get_dates() {
    $dates = array( $this->date_start );
    while(end($dates) < $this->date_end){
        $dates[] = date('Y-m-d', strtotime( end( $dates ).' +1 day'));
    }
    return $dates;
  }

/**
 * Return a list of all dates within the defined range. 
 *
 * @return array
 */
  public function get_dates_list( $start, $end ) {
    $dates = array( $start );
    while( end( $dates ) < $end ){
        $dates[] = date('Y-m-d', strtotime( end( $dates ).' +1 day'));
    }
    return $dates;
  }


/**
 * Get all entries from the codes DB. Ignore dates earlier than 30 days 
 *
 * @return array
 */
  public function get_codes( $scope = '-30 days' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_codes';
    $dateRangeStart = date('Y-m-d', strtotime( $scope )); // currentdate - 30 days
    $codesDB = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE item_id = %s AND booking_date > $dateRangeStart", $this->item_id ), ARRAY_A); // get dates from db
    return $codesDB;
  } 
/**
 * Get timeframes by item_id
 * 
 *
 * @return array
 */
  public function get_timeframes( $item_id, $date_start = '', $single = FALSE ) {

    $return = $limit = '';

    if ( empty( $date_start) ) {
      $date_start = $this->current_date;
    }

    if ( $single ) {
      $limit = 'LIMIT 1';
    }

    if ( $item_id ) {
      global $wpdb;
      // @TODO: Fix start date not being honored by function -> maybe change data format
      $table_name = $wpdb->prefix . 'cb_timeframes'; 
      $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE item_id = %s AND date_end >= %s ORDER BY date_start ASC $limit", $item_id, $date_start );
      $timeframes = $wpdb->get_results($sql, ARRAY_A);

      if ( !empty( $timeframes) ) {
          return $timeframes;
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
        'closed_days' => get_post_meta( $id, $this->prefix . '_location_closeddays', true, true ),
        'openinghours' => get_post_meta( $id, $this->prefix . '_location_openinghours', true ),
        );
      return $location;
    } else {
      return false;
    }
  }

/**
 * Get Item meta
 *
 *@param $id item id
 *
 *@return array 
 *
 */
  public function get_item_meta ( $id ) {
  
    $meta = get_post_meta ( $id );

  }


/**
 * Get Item post & meta outside the loop
 *
 *@param $id item id
 *
 *@return array 
 *
 */
  public function get_item ( $id ) {
  
    global $wpdb;

    if ( $id ) {
      $post = get_post( $id , ARRAY_A );
      $meta = get_post_meta ( $id );
      $item = array_merge ( $post, $meta );
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
        'first_name' => $userdb->first_name,
        'last_name' => $userdb->last_name,
        'name' => $userdb->userdb_login,
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
  public function render_item_single( $item_id  ) {

    $template_vars = $this->get_timeframe_array( $item_id );
    if ($template_vars) {
      return cb_get_template_part( 'timeframes-full', $template_vars, true ); // include the template
    } else {
      return '<span class="">'. __( 'This item can´t be booked at the moment.', $this->prefix ) . '</span>';
    }
  }

/**
 * Single item, all calendars. 
 *
 *@param $id item id
 *
*/

  public function get_timeframe_array( $item_id, $single = FALSE  ) {

    // 1. Get Item (Title & Description)
    $item = $this->get_item( $item_id );
    $codes = $this->codes;

    $booking_comments = new CB_Booking_Comments();
    $comments = $booking_comments->get_booking_comments( $item_id );    
    $booked = new CB_Booking;
    $booked_days = $booked->get_booked_days_array( $item_id, $comments );

    // 2. Calculate start & end dates 
    $date_range_start = date('Y-m-d'); // current date
    $date_range_end = date('Y-m-d', strtotime ( '+ ' .$this->daystoshow . 'days' )); // current date + configured daystoshow setting
    $dates_list = $this->get_dates_list ( $date_range_start, $date_range_end, $single );

    // 3. Get timeframes from the db that: match the item_id + end_date is after today´s date
    $timeframes = $this->get_timeframes( $item_id, $date_range_start );
    
    // ob_start(); // start buffering

    $template_vars = array(
      'item' => $item,
      'timeframes' => array()
      );
    // 4. Loop through timeframes  
    if ( $timeframes ) { // there are timeframes

      foreach ( $timeframes as $tf) {

          $location = $this->get_location ( $tf['location_id'] ); // get location info
          // 5. Calculate the starting & end-dates for display of the timeframe 
          $cal_start = strtotime ( max( $date_range_start, $tf['date_start'] ) );
          $cal_end = strtotime( min( $date_range_end, $tf['date_end'] ) );
          $day_counter = $cal_start;

          // 6. check if there are days to be displayed (honoring the settings-function days_to_show)
          if ( $cal_start <= $cal_end ) {
           
            $template_vars[ 'timeframes' ][ $tf[ 'id' ] ] =  $this->prepare_template_vars_timeframe( $location, $tf );

            // 7. Loop through days
            while ( $day_counter <= $cal_end ) { // loop through days

              // $cell_attributes = $this->prepare_template_vars_calendar_cell( $day_counter, $location, $booked_days );        
              $template_vars[ 'timeframes' ][ $tf[ 'id' ] ][ 'calendar' ][ $day_counter ] =  $this->prepare_template_vars_calendar_cell( $day_counter, $location, $booked_days );

              $day_counter = strtotime('+1 day', $day_counter); // count up
            }    
          } 
      }

    } else { // no timeframes, item can´t be booked
      return FALSE;
    }
    return $template_vars;
  }


  public function set_tooltip ( $day_counter ) {

  }

/**
 * Renders the list of items (Archive) 
 *
 * @return html
*/

  public function render_item_list ( $id = false ) {

    $item_content = '';
    if (! $id ) {
      $id = get_the_ID(); // get post id 
    }

    $attributes = array (
      'id' => $id,
      'title' => get_the_title( $id ),
      'permalink' => get_the_permalink( $id ),
      'thumb' => get_post_thumbnail_id ( $id ), 
      'meta' => get_post_meta( $id )
      );

    $item_content =  cb_get_template_part( 'item-list-item', $attributes, TRUE );      

    $timeframes = $this->get_timeframe_array( $id, $this->current_date, TRUE );

    if ( $timeframes ) {
      $item_content .=  cb_get_template_part( 'item-list-timeframes-compact', $timeframes, TRUE );           
    } else {
      $item_content .= '<span class="">'. __( 'This item can´t be booked at the moment.', $this->prefix ) . '</span></div>';
    }
    
    return $item_content;

  }  

/**
 * Prepare attributes for calendar-cell template
 * Converts the timestamp to an array with 
 * Day name ("Tue"), Short date ("11.3."), weekday-code ("day2")  
 *
 * @param $string timestamp
 * @param array $location
 * @param array $booked_days
 * @return array
*/

public function prepare_template_vars_item ( $item ) {
  
  $attributes = array (
    'id' => $item['id'],
    'title' => $item['post_title'],
    'description_short' => $item['commons-booking_item_descr'],
    'description_full' => $item['post_content']  
    );
  
  return $attributes;
}


/**
 * Prepare attributes for calendar-cell template
 * Converts the timestamp to an array with 
 * Day name ("Tue"), Short date ("11.3."), weekday-code ("day2")  
 *
 * @param $string timestamp
 * @param array $location
 * @param array $booked_days
 * @return array
*/

public function prepare_template_vars_calendar_cell ( $timestamp, $location, $booked_days ) {

  $dates = array_keys( $booked_days );
  $status = $this->set_day_status( $timestamp, $location, $dates );
  $tooltip = '';

  if ( $status == 'closed' ) {
    $tooltip = __('This day is closed. No pick up or return.', 'commons-booking');
  } elseif ( $status == 'bookable' ){
    $tooltip = __('You can book on this day.', 'commons-booking');
  } elseif ( $status == 'booked' ) {
     if ( ! empty( $booked_days[ $timestamp ] ) ) { // booking comment available
        $tooltip =  __('Booked by a user: ', 'commons-booking') . $booked_days[ $timestamp ];
      } else {
      $tooltip =  __('This day is booked', 'commons-booking');
    }
  }

  
  $attributes = array (
    'day_short' => date_i18n ('M', $timestamp ),
    'date_short' => date_i18n ('j.', $timestamp ),
    'weekday_code' => 'day' . date('N', $timestamp),
    'id' => $timestamp,
    'status' => $this->set_day_status( $timestamp, $location, $dates ),   
    'tooltip' => $tooltip    
    );
  
  return $attributes;
}

/**
 * Prepare attributes for calendar-location template
 *
 * @param array $location
 * @param array $location, $timeframe
 * @return array
*/

public function prepare_template_vars_timeframe ( $location, $timeframe ) {

  $contact_string = $address_string = '';

  if ( !empty( $location['contact_hide'] ) ) { // honor setting to hide contact info
    $contact_string = __('You will recieve contact information after your booking.', 'commons-booking');
  } else {
    $contact_string = $location[ 'contact' ];

  }

  $address_check = array_filter( $location[ 'address' ] );

   if ( !empty ( $address_check ) ) { // format the adress
      $address_string = $this->format_adress( $address_check );
  }

  $daterange_string = date_i18n( 'd.m.y', strtotime( $timeframe['date_start'] ) ) . ' - ' . date_i18n( 'd.m.y', strtotime( $timeframe['date_end'] ) );

  $attributes = array (
    'name' => $location['name'], 
    'contact' => $contact_string,
    'opening_hours' => $location['openinghours'],
    'address' => $address_string,
    'date_range' => $daterange_string,
    'timeframe_title' =>  $timeframe['timeframe_title'],
    'timeframe_id' =>  $timeframe['id'],
    'render_daynames' => $this->render_daynames,
    'location_id' =>  $location['id']
    );
  
  return $attributes;
}

/**
 * Format the adress, support for localization
 *
 * @param $location array
 * @return $adress string
 *
*/
  public function format_adress( $address ) {
    $street = isset ( $address[ 'street'] ) ? $address[ 'street'] : '';
    $zip = isset ( $address[ 'zip'] ) ? $address[ 'zip'] : '';
    $city = isset ( $address[ 'city'] ) ? $address[ 'city'] : '';
    $country = isset ( $address[ 'country'] ) ? $address[ 'country'] : '';

    $address_string = sprintf(
        /* translators: 1: Name of Street 2: ZIP code 3: Name of a city  4: Country*/
        __( '%1$s, %2$s %3$s, %4$s', 'commons-booking' ),
        $street,
        $zip,
        $city,
        $country

    );
    return $address_string;
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

    $timestamp = $booked_days;

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
  public function render_booking_bar() {

    $template_vars = array (
      'target_url' => get_permalink( $this->target_url ),
      'plugin_slug' => $this->prefix
      );
    return cb_get_template_part( 'calendar-bookingbar', $template_vars , true );
  }
}
?>
