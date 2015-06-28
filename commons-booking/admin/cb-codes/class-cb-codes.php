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
 * @package Commons_Booking_Codes
 * @author  Florian Egermann <email@example.com>
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
 * @param $date_start
 * @param $date_end
 *
 */
  public function __construct( $item_id ) {
 
    // get Codes from Settings page
    $settings = new Commons_Booking_Admin_Settings;

    global $wpdb;
    $this->table_name = $wpdb->prefix . 'cb_codes';

    $this->prefix = "commons-booking";

    $this->daterange_start = date('Y-m-d', strtotime( '-30 days' )); // currentdate - 30 days

    $this->item_id = $item_id;

    $csv = $settings->get( 'codes', 'codes_pool' ); // codes as csv 
    $this->codes_array = $this->split_csv( $csv );  // codes as array


}

public function set_timeframe ( $timeframe_id, $date_start, $date_end ) {

    $this->timeframe_id = $timeframe_id;
    $this->date_start = $date_start;
    $this->date_end = $date_end;
  }

/**
 * Get settings from backend.
 */
  public function split_csv( $csv ) {

    $splitted = explode(",", $csv);
    $splitted = preg_grep('#S#', array_map('trim', $splitted)); // Remove Empty
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
 * */
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
 * Handle the display of the dates/codes interface
 */
public function render() {

  echo ( '<h2>Codes: ' . get_the_title( $this->item_id ) . '</h2>');
  // $dir = plugin_dir_url( __FILE__ ) . 'codes-generate.php';
  // echo $dir;

  // if ( $this->missing_dates ) { 
  //   ?>
  //   <?php new Admin_Table_Message ( __('No codes generated or codes missing.', $this->prefix), 'error' ); ?>
  //   <form id="codes-generate" method="POST" action="<?php echo $dir ?>">
  //     <input class="hidden" name="id" value="<?= $this->timeframe_id; ?>">  
  //     <input class="hidden" name="generate" value="generate">
  //     <input type="submit" value="<?php _e('Generate Codes', $this->prefix)?>" id="submit_generate" class="button-primary" name="submit_generate">
  //   </form>

  //   <?php
  //   if (isset($_REQUEST['generate'])) {
  //     $sql = $this->sql_insert( $this->item_id, $this->missing_dates, $this->codes_array );
  //   }
  // } else { // no Codes missing?>
  //   <?php   
  // } // end if $missing_dates

  $allDates = array_merge ($this->missing_dates, $this->matched_dates);
  $this->render_table( $allDates );
}
/**
 * Render the dates/codes-table.
 *
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
        $row[ 'code' ] = ('<span style="color:red">'. __( ' Missing Code', $this->prefix) .'</span>'); 
      } ?>
    <tr><td><?php _e( date( 'j.n.y', strtotime( $row[ 'date' ] ))); ?></td><td><?php _e( $row[ 'code' ] ); ?></td></tr>
  <?php } // end foreach ?>
  </table>
  <?php
  }

}