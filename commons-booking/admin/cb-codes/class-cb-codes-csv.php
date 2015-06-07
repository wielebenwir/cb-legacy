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
 * @author  Florian Egermann <email@example.com>
 */
class Commons_Booking_Codes_CSV {

  public $csv;
  public $item_id;
  public $date_start;
  public $date_end;

/**
 * Constructor.
 *
 * @param $item_id 
 * @param $date_start
 * @param $date_end
 *
 */
  public function __construct( $timeframe_id, $item_id, $date_start, $date_end) {
     $this->timeframe_id = $timeframe_id;
     $this->item_id = $item_id;
     $this->date_start = $date_start;
     $this->date_end = $date_end;
}
/**
 * Get settings from backend.
 */
  public function get_settings() {
    global $wpdb;
    $settings = get_option( 'commons-booking-settings-codes' ); // @TODO: add Prefix;
    $csv = $settings['commons-booking_codes_pool'];

    $singleCodes = explode(",", $csv);
    $singleCodes = preg_grep('#S#', array_map('trim', $singleCodes)); // Remove Empty
    $this->csvcodes = $singleCodes;

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
  public function get_codetable_entries() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_codes';
    $dateRangeStart = date('Y-m-d', strtotime( '-30 days' )); // currentdate - 30 days
    $codesDB = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE item_id = %s AND booking_date > $dateRangeStart", $this->item_id ), ARRAY_A); // get dates from db
    return $codesDB;
  } 

/**
 * Compare timeframe dates and entries in the codes db 
 * */
  public function compare() {
    $this->get_settings();
    $codesDB = $this->get_codetable_entries();

    $tfDates = $this->get_dates();
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

/**
 * Handle the display of the dates/codes interface
 */
public function render() {

  echo ( '<h2>Codes: ' . get_the_title( $this->item_id ) . '</h2>');

  if ( $this->missingDates ) { 
    ?>

    <?php  new Admin_Table_Message ( __('No codes generated or codes missing.', 'cb_timeframes_table'), 'error' ); ?>
    <form id="codes" method="POST">
      <input class="hidden" name="id" value="<?= $this->timeframe_id; ?>">  
      <input class="hidden" name="generate" value="generate">
      <input type="submit" value="<?php _e('Generate Codes', 'cb_timeframes_table')?>" id="submit_generate" class="button-primary" name="submit_generate">
    </form>

    <?php
    if (isset($_REQUEST['generate'])) {
      $sql = $this->sql_insert( $this->item_id, $this->missingDates, $this->csvcodes );
    }
  } else { // no Codes missing?>
    <?php   
  } // end if $missingDates

  $allDates = array_merge ($this->missingDates, $this->matchedDates);
  $this->render_table( $allDates );
}
/**
 * Render the dates/codes-table.
 *
 */
public function render_table( $dates ) {
  ?>
  <table class="widefat striped">
    <thead>
      <tr>
        <th><?php _e( 'Date' ); ?></th>
        <th><?php _e( 'Code' ); ?></th>
      </tr>
    </thead>
  <?php foreach ($dates as $row) {
      if ( !isset($row[ 'code' ])) { 
        $row[ 'code' ] = ('<span style="color:red">'. __( ' Missing! ') .'</span>'); 
      } ?>
    <tr><td><?php _e( date( 'j.n.y', strtotime( $row[ 'date' ] ))); ?></td><td><?php _e( $row[ 'code' ] ); ?></td></tr>
  <?php } // end foreach ?>
  </table>
  <?php
}
/**
 * Add pointers.
 * @TODO: check for security / split into prepare_sql and do_sql
 *
 * @param $itemid 
 * @param $array list of dates
 * @param $array list of codes
 */
private function sql_insert( $itemid, $array, $codes) {

  new WP_Admin_Notice( __( 'Error Messages' ), 'error' );

  global $wpdb;
  $table_name = $wpdb->prefix . 'cb_codes'; 

  shuffle( $codes ); // randomize array

  if ( count( $codes ) < count( $array )) {
    new Admin_Table_Message ( __('Not enough codes defined. Enter them in the Settings.', 'cb_timeframes_table'), 'error' );
    return false;

  }

  $sqlcols = "item_id,booking_date,bookingcode";
  $sqlcontents = array();
  $sqlquery = '';
  $count = count( $array );

  for ( $i=0; $i < $count; $i++ ) {
    array_push($sqlcontents, '("' . $itemid. '","' . $array[$i]['date'] . '","' . $codes[$i] . '")');
  }
  $sqlquery = 'INSERT INTO ' . $table_name . ' (' . $sqlcols . ') VALUES ' . implode (',', $sqlcontents ) . ';';

  $wpdb->query($sqlquery);
  }
}