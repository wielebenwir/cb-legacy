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
 * @package Commons_Booking_Codes_CSV
 * @author  Your Name <email@example.com>
 */
class Commons_Booking_Codes_CSV {

  public $csv;
  public $item_id;
  public $date_start;
  public $date_end;

  public function __construct( $item_id, $date_start, $date_end) {
     $this->item_id = $item_id;
     $this->date_start = $date_start;
     $this->date_end = $date_end;
}

  public function get_settings() {
    global $wpdb;
    $settings = get_option( 'commons-booking-settings-codes' ); // @TODO: add Prefix;
    $csv = $settings['commons-booking_codes_pool'];

    $singleCodes = explode(",", $csv);
    $singleCodes = preg_grep('#S#', array_map('trim', $singleCodes)); // Remove Empty
    shuffle($singleCodes);

    $this->codes = $singleCodes;

  }

  public function get_dates() {
    $dates = array($this->date_start);
    while(end($dates) < $this->date_end){
        $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
    }
    return $dates;
  }


  public function get_codetable_entries() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_codes';
    $dateRangeStart = date('Y-m-d', strtotime( '-30 days' )); // currentdate - 30 days
    $codeDates = $wpdb->get_results($wpdb->prepare("SELECT date FROM $table_name WHERE item_id = %s AND date > $dateRangeStart", $this->item_id ), ARRAY_A); // get dates from db
    $return = array();
    foreach ( $codeDates as  $codeDate ) { // flatten array
      array_push ($return, $codeDate['date'] );
    }
    return $return;

  } 


  public function compare() {
    $this->get_settings();
    $this->$timeframeDates = $this->get_dates();
    $this->$codeDates = $this->get_codetable_entries();

    $this->timeframeDates = array_filter($this->timeframeDates); // remove empty 
    $diff=array_diff($this->timeframeDates, $this->codeDates);

    if ( empty( $this->timeframeDates ) ) {
     echo __( 'You need to add a timeframe and save before generating Codes' );
    } elseif ( $diff ) {
     echo __( 'Codes missing or incomplete, click to generate' ); 
    }
  }

  public function compare_arrays( $cd, $tfd ) { // @TODO: not yet working properly 
    if (in_array( $cd, $this->timeframeDates)) {
      echo ("drin");
    } else {
      echo ("NICHT drin");
    }
  }

  public function render_dates() {
    // foreach ($this->timeframeDates as $d) {
  }


}