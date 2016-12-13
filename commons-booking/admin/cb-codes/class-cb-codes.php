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
 *
 * @package Commons_Booking_Codes
 * @author    Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking_Codes {

  public $table_name;

  public $codes_array;
  public $csv;

  public $item_id; 
  public $date_start;
  public $date_end;

  public $missing_dates;
  public $matched_dates;


  public $date;
  public $daterange_start;
  public $timeframe_id;

/**
 * Constructor.
 *
 * @param $item_id 
 *
 */
  public function __construct( $item_id ) {
 
    // get Codes from Settings page
    $settings = new CB_Admin_Settings;

    global $wpdb;
    $this->table_name = $wpdb->prefix . 'cb_codes';

    $this->prefix = "commons-booking";

    $this->daterange_start = date('Y-m-d', strtotime( '-30 days' )); // currentdate - 30 days

    $this->item_id = $item_id;

    $csv = $settings->get_settings( 'codes', 'codes_pool' ); // codes as csv 
    $this->codes_array = $this->split_csv( $csv );  // codes as array


}

/**
 * Sets the timeframe basic variables
 *
 */

public function set_timeframe ( $timeframe_id, $date_start, $date_end ) {

    $this->timeframe_id = $timeframe_id;
    $this->date_start = $date_start;
    $this->date_end = $date_end;
  }

/**
 * Split comma-seperated values.
 *
 *@param $csv string
 *@return array
 */
  public function split_csv( $csv ) {

    $splitted = explode(",", $csv);
    $splitted = array_filter($splitted); // Remove empty
    $splitted = array_map('trim', $splitted); // Remove white space
    
    return ($splitted);
  }

/**
 * Get all entries from the codes DB. Ignore dates earlier than daterange_start 
 *
 * @return array
 */
  public function get_codes( ) {
    global $wpdb;
    $codes = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table_name  WHERE item_id = %s AND booking_date > $this->daterange_start", $this->item_id ), ARRAY_A); // get dates from db
    // $single = $this->split_csv( $codes );
    return $codes;
  }

 /**
 * Get code for date / item 
 *
 * @return array
 */
  public function get_code( $date ) {
    global $wpdb;
    $code = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table_name  WHERE item_id = %s AND booking_date > $this->$date", $this->item_id ), ARRAY_A); // get dates from db
    return $code;
  }


/**
 * Compare timeframe dates and entries in the codes db 
 * 
 */
  public function compare() {
    $codes_db = $this->get_codes();

    $tfDates = get_dates_between( $this->date_start, $this->date_end );
    $codeDates = array();

    foreach ( $codes_db as $entry ) {
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
        $temp[ 'code'] = $codes_db[ $index ]['bookingcode'];
        array_push ($matched, $temp);
      } else {
        $temp[ 'date'] = $tfDates[ $i ];
        array_push ($missing, $temp);
      }
    }
    $this->matched_dates = $matched;
    $this->missing_dates = $missing;
  }

/**
 * Handle the display of the dates/codes interface on the timeframe edit screen
 */
public function render() {

  echo ( '<h2> '. __( 'Codes', 'commons-booking') . '</h2>');

  $allDates = array_merge ($this->missing_dates, $this->matched_dates);
  $this->render_table( $allDates );
}
/**
 * Render the dates/codes-table on the timeframe edit screen
  */
public function render_table( $dates ) {
  ?>
  <table class="widefat striped" id="table-codes">
    <thead>
      <tr>
        <th><?php _e( 'Date' ); ?></th>
        <th><?php _e( 'Code' ); ?></th>
      </tr>
    </thead>
  <?php foreach ($dates as $row) {
      if ( !isset($row[ 'code' ])) { 
        $row[ 'code' ] = ('<span style="color:red">'. __( 'Code is missing!', 'commons-booking') .'</span>'); 
      } ?>
    <tr><td><?php _e( date( 'j.n.y', strtotime( $row[ 'date' ] ))); ?></td><td><?php echo strip_tags( $row[ 'code' ] ); ?></td></tr>
  <?php } // end foreach ?>
  </table>
  <?php
  }

}