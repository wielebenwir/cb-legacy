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
    $codesDB = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE item_id = %s AND date > $dateRangeStart", $this->item_id ), ARRAY_A); // get dates from db
    return $codesDB;
  } 


  public function compare() {
    $this->get_settings();
    $codesDB = $this->get_codetable_entries();

    $tfDates = $this->get_dates();
    $codeDates = array();

    foreach ( $codesDB as $entry ) {
      array_push ($codeDates, $entry['date']);
    }
    
    $matched = array();
    $missing = array();

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


public function render() {

  if ( $this->missingDates ) { ?>
    <h2><?php _e('No codes generated or codes missing. Please generate Codes', 'cb_timeframes_table')?></h2>
    <form id ="codes" method="POST">
    <input class="hidden" name="generate" value="generate">
    <input type="submit" value="<?php _e('Generate Codes', 'cb_timeframes_table')?>" id="submit_generate" class="button-primary" name="submit_generate">
    </form>
    <?php
    $this->generate_codes_sql();
  } else { // no Codes missing?>
    <h2>Codes</h2>
    <?php   
  } // end if $missingDates

  $allDates = array_merge ($this->missingDates, $this->matchedDates);
  $this->render_table( $allDates );
}

public function render_table( $dates ) {
  echo ( '<table class="widefat striped">' );
  foreach ($dates as $row) {
    if ( !isset($row[ 'code' ])) { $row[ 'code' ] = ('<span style="color:red">'. __( ' Missing! ') .'</span>'); }
    echo ( '<tr><td>' . $row[ 'date' ] . '</td><td>' . $row[ 'code' ] . '</td>');
    echo ( '</tr>' );
  }
  echo ( '</table>' );
}

private function generate_codes_sql() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'cb_codes'; 
  if (isset($_REQUEST['generate'])) {
    echo ("we would generate now");
  }


}



}