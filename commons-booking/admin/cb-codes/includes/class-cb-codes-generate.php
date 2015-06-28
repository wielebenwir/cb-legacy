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




  public function prepare( ) {

    if ( isset( $_REQUEST['item_id'] ) ) {
        echo ("<h1>ITEM ID" . $_REQUEST['item_id'] . "</h1>" );

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
   * Generates the codes.
   *
   */

  public function generate_codes( ) {

    $this->prepare();
    if ( count($this->missing_dates) > 0 ) {
      $this->sql_insert($this->item_id);
    } else {
      new Admin_Table_Message ( __('Codes already in Database.', $this->prefix), 'updated' );
    }
  }



  /**
   * Inserts into Database.
   *
   */
  private function sql_insert( ) {

    global $wpdb;

    shuffle( $this->codes_array ); // randomize array

    var_dump($this->missing_dates);

    if ( count( $this->codes_array ) < count( $this->missing_dates )) {
      new Admin_Table_Message ( __('No or not enough codes defined. Add them in Commons Booking Settings.', $this->prefix), 'error' );
      return false;
    } else {
        new Admin_Table_Message ( __('New booking codes have been generated.', $this->prefix), 'updated' );
    }

    $sqlcols = "item_id,booking_date,bookingcode";
    $sqlcontents = array();
    $sqlquery = '';
    $count = count( $this->missing_dates );

    for ( $i=0; $i < $count; $i++ ) {
      array_push($sqlcontents, '("' . $this->item_id. '","' . $this->missing_dates[$i]['date'] . '","' . $this->codes_array[$i] . '")');
    }
    $sqlquery = 'INSERT INTO ' . $this->table_name . ' (' . $sqlcols . ') VALUES ' . implode (',', $sqlcontents ) . ';';
        new Admin_Table_Message ( $sqlquery , 'updated' );

    $wpdb->query($sqlquery);
  }

}