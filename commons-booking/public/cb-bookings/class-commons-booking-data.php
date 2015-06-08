<?php
/**
 *
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @package Commons_Booking_Data
 * @author  Florian Egermann <email@example.com>
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
     // $this->queryid = $queryid;
     // $this->selector = $selector;
    $this->daystoshow = 30;
}


/**
 * Gather all necessary data 
 *
 */
  public function gather_data() {

    $this->timeframes = $this->get_timeframes();
    $this->codes = $this->get_codes();
    $this->dates = $this->get_dates();

  } 



/**
 * Get settings from backend.
 */
  public function get_settings() {
    global $wpdb;
    $settings = get_option( 'commons-booking-settings-codes' ); // @TODO: add Prefix;
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

  private function get_timeframes() {

    $return = '';

    if ($this->item_id) {
      global $wpdb;

      $table_name = $wpdb->prefix . 'cb_timeframes'; 
      $sql = $wpdb->prepare( 'SELECT * FROM ' . $table_name . ' WHERE item_id = %s ORDER BY item_id DESC', $this->item_id );
      $this->timeframes = $wpdb->get_results($sql, ARRAY_A);

      if ( $this->timeframes ) {
          return $this->timeframes;
        } else { 
          return __(' No Timeframes configured ' );
        } 
    } else {
      return __(' Something went wrong ' );
    }
  }
/**
 * Compare timeframe dates and entries in the codes db 
 * */
  public function compare() {
    $codesDB = $this->codes;
    $tfDates = $this->dates;
    $codeDates = array();

    foreach ( $codesDB as $entry ) {
      array_push ($codeDates, $entry['booking_date']);
    }
    
    $matched = array();
    $missing = array();
    $missingFlat = '';

    for ( $i = 0; $i < count($tfDates); $i++ ) {

      $index = array_search( $tfDates[ $i ], $codeDates );
      $temp = array();
      if ( ($index !== FALSE) ) {
        $temp[ 'date'] = $tfDates[ $i ];
        $temp[ 'code'] = $codesDB[ $index ]['bookingcode'];
        array_push ($matched, $temp);
      } else {
        $temp[ 'date'] = $tfDates[ $i ];
        array_push ($missing, $temp);
      }
    }
    $this->matchedDates = $matched;
    $this->missingDates = $missing;
  }



  public function show_by_item( $item_id ) {

    $this->item_id = $item_id;

    // get a list of all dates that should be shown (config setting)
    $this->date_range_start = date('Y-m-d');
    $this->date_range_end = date('Y-m-d', strtotime ( '+ ' .$this->daystoshow . 'days' ));

    // get Data
    $this->gather_data();


    $tf = $this->timeframes;
    $codes = $this->codes;

    // var_dump($codes);

    foreach ( $this->timeframes as $tf) {
      if ( $tf['date_start'] <= $this->date_range_end ) { // check if start date is within the date range
      $this->render_timeframe( $tf, $codes );
      }
    }

  }

  public function render_timeframe( $tf, $codes ) {

    $timeframe_comment = $tf['timeframe_title'];

    $location_name = get_the_title( $tf['location_id'] );
    $location_geo = ( get_post_meta( $tf['location_id'], 'commons-booking_location_map', true ) ); // @TODO: add prefix
    $location_contact = ( get_post_meta( $tf['location_id'], 'commons-booking_location_contactinformation', true ) ); // @TODO: add prefix

    include (commons_booking_get_template_part( 'calendar', 'location', FALSE ));


    // $dates = array();
    $first = strtotime( $tf['date_start'] );
    $last = min ( strtotime( $tf['date_end'] ), strtotime( $this->date_range_end ) ); // must be within range

    while( $first <= $last ) {

      echo ( '<li class="' .  date ('D', $first ) .'" >' . date ('D d.m.', $first ) . '</li>' );
      $code = ( $this->searcharray( date('Y-m-d', $first ), 'booking_date', $codes ) );
      echo ($codes[$code]['bookingcode']);
      $first = strtotime('+1 day', $first); // counter
    }
    echo ('</ol>');
  }

  public function searcharray($value, $key, $array) {
   foreach ($array as $k => $val) {
       if ($val[$key] == $value) {
           return $k;
       }
   }
   return null;
  }

}







?>