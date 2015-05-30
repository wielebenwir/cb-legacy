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
 * @package Commons_Booking_Timeframes
 * @author  Your Name <email@example.com>
 */
class Commons_Booking_Timeframes {

  private $postID; 
  private $colDefinition;
  private $rows;

   /**
   *
   * @since     0.0.1
   */
  public function __construct( $postID ) {
    $this->postID = $postID;

  }

  /**
   * Set up col definitions and get Data from DB
   *   
   */  
  public function prepare() {
    $this->colDefinition = $this->set_Columns();
    $this->rows = $this->get_Items();
  }

  /**
   * Sets the columns
   *   
   * @return  array    names of the col headers
   */
  private function set_Columns() {
    $this->columns = array(
      'location_id' => __( 'Location' ),
      'date_start' => __( 'Start Date '),
      'date_end' => __( 'End Date '),
      'timeframe_title' => __('Note'),
      'edit' => __( 'Edit' )
      );
    return $this->columns;
  }
  /**
   * Renders the header
   *   
   * @return  mixed html
   */
  private function table_header() {
    $colheader = '<tr>';
    foreach ($this->colDefinition as $key => $value) {
      $colheader .= (' <th>' . $value . '</th>');
    }
    return ($colheader .= '</tr>');
  }  
  /**
   * Handle Columns
   *   
   * @return  mixed html
   */
  private function table_columns() {

    $columns = "";

    foreach ($this->rows as $row => $cols) {

      foreach ($this->colDefinition as $headerkey => $headerval) { // loop through defined fields
        if ( isset($cols[$headerkey])) {  // check if col is in defined fields OR edit  
            $columns .= $this->table_fields ($headerkey, $cols[$headerkey]); // get table fields
        } elseif ($headerkey = "edit") {
            $columns .= $this->table_fields ($headerkey, "");
        }
      }
      $columns = '<tr>' .$columns . '</tr>';
    }
    return ($columns);
  }

  /**
   * Handle field display
   *   
   * @return  mixed html
   */
  private function table_fields( $key, $value ) {

    if (array_key_exists($key, $this->colDefinition)) {
      switch ($key) {  // Handle different field display
        case 'item_id':
        $field = ( 'ID:<strong>' . $this->table_fields_get_title($value) . '</strong>' );
          break;      
        case 'location_id':
        $field = ( '<strong>' . $this->table_fields_get_title($value) . '</strong>' );
          break;
        case 'edit':
          $field = "edit"; 
          break;         
        default:
          $field = $value;
      }
      return '<td> ' . $field . '</td>';
    }
  }

  private function table_fields_edit ( $itemID ) {

    //return '<span class="edit"><a href="  ' . $itemID; 
  }

   /**
   * Sorts the array, removes any leftovers
   *   
   * @return  array
   */ 
  private function table_fields_get_title( $itemID ) {
    
    return '<a href="' . get_the_permalink($itemID) . '">' . get_the_title($itemID) . '</a>';
    
  }

  /**
   * Returns a object of timeframes
   *
   * @param   array     $params       An array of optional parameters
   *              types       An array of portfolio item type slugs
   *              industries    An array of portfolio industry slugs
   *              quantity    Number of posts to return
   *
   * @return  object    A post object
   */
  private function get_Items() {

    $return = '';

    if ($this->postID) {
      global $wpdb;

      $sql = $wpdb->prepare( 'SELECT * FROM wpdev_cb_timeframes WHERE item_id = %s ORDER BY item_id DESC', $this->postID );
      $this->items = $wpdb->get_results($sql, ARRAY_A);

      if ( $this->items ) {
          return $this->items;
        } else { 
          return __(' No Timeframes configured ' );
        } 
    } else {
      return __(' Something went wrong ' );
    }
  }

  public function render_timeframes() {
    // $colDefinition = $this->set_Columns();
    $this->prepare();
    $tableheader = $this->table_header();
    $tablecolumns = $this->table_columns();
    echo ('<table class="wp-list-table widefat fixed striped timeframe">' . $tableheader . $tablecolumns . '</table>');
    // echo ('<table>');
    // foreach ($rows as $key => $value) {
    //   echo ('<tr>');
    //   echo ('<td class="'. $key . '">'. $value['timeframe_title']. '</td><td>' . $value['timeframe_title']);
    //   echo ('</tr>');
    // }
    // echo ('</table>');
  }

}
