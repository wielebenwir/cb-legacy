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
 * Generate Codes 
 *
 * @package Commons_Booking_Codes
 * @author  Florian Egermann <email@example.com>
 */
class Commons_Booking_Codes_Generate extends Commons_Booking_Codes {

  public $item_id;

  /**
   * Gets all necessary variables from parent function, compares
   *
   * @param $item_id 
   * @param $date_start
   * @param $date_end
   *
   */
  public function __construct() {

  }
  /**
   * Get all necessary variables.
   *
   */
  public function prepare( ) {

    if ( isset( $_REQUEST['item_id'] ) ) {

      $this->item_id = $_REQUEST['item_id'];
      $this->date_start = $_REQUEST['date_start'];
      $this->date_end = $_REQUEST['date_end'];
      parent::__construct( $this->item_id );
      parent::compare();

    } else {

      die ( __( 'No id!'));
    }
    
  }
  /**
   * Generate the codes, display message
   *
   */
  public function generate_codes( ) {

    $this->prepare();
    if ( count($this->missing_dates) > 0 ) {
      $this->sql_insert($this->item_id);
    } else {
      new Admin_Table_Message ( __('Codes already in Database.', 'commons-booking' ), 'updated' );
    }
  }



  /**
   * Insert into Database.
   *
   */
  private function sql_insert( ) {

    global $wpdb;

    $sqlcols = "item_id,booking_date,bookingcode";
    $sqlcontents = array();
    $sqlquery = '';
    $count = count( $this->missing_dates );

    if ( count( $this->codes_array ) < count( $this->missing_dates )) {
      new Admin_Table_Message ( __('Notice: Not enough codes defined in settings, some codes will be repeated.', $this->prefix), 'error' );
    } 
    new Admin_Table_Message ( __('Missing booking codes have been generated.', $this->prefix), 'updated' );

    // create mysql query
    for ( $i=0; $i < $count; $i++ ) {
      $random = rand( 0, count( $this->codes_array ) -1 );
      array_push($sqlcontents, '("' . $this->item_id. '","' . $this->missing_dates[$i]['date'] . '","' . $this->codes_array[ $random ] . '")');
    }
    $sqlquery = 'INSERT INTO ' . $this->table_name . ' (' . $sqlcols . ') VALUES ' . implode (',', $sqlcontents ) . ';';
    $wpdb->query($sqlquery);
  }

}