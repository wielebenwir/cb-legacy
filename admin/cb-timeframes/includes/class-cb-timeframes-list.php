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
 * Display the list of configured Timeframes in a metabox on the item edit screen.
 *
 * @package Commons_Booking_Timeframes_List
 * @author  Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking_Timeframes_List {

  private $postID; 
  private $colDefinition;
  private $rows;
  public $prefix;

   /**
   *
   * @since     0.0.1
   */
  public function __construct( $postID ) {
    $this->postID = $postID;
    $this->prefix = "commons-booking";
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
      'location_id' => __( 'Location', 'commons-booking' ),
      'date_start' => __( 'Start Date', 'commons-booking'),
      'date_end' => __( 'End Date', 'commons-booking'),
      'timeframe_title' => __('Note', 'commons-booking'),
      'edit' => __( 'Edit', 'commons-booking' )
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

    if ( ($this->rows) ) {

      foreach ($this->rows as $row => $cols) {

        foreach ($this->colDefinition as $headerkey => $headerval) { // loop through defined fields
          if ( isset($cols[$headerkey])) {  // check if col is in defined fields OR edit  
              $columns .= $this->table_fields ($headerkey, $cols[$headerkey]); // get table fields
          } elseif ($headerkey = "edit") {
              $columns .= $this->table_fields ($headerkey, $cols['id']);
          }
        }
        $columns = '<tr>' .$columns . '</tr>';
      }
      return ($columns);
    } else {
      return '<p>' . __( 'No timeframes set up yet.', 'commons-booking' ) . '</p>';
    }
  }

  /**
   * Handle field display
   *   
   * @return  mixed html
   */
  public function table_fields( $key, $value ) {

    if (array_key_exists($key, $this->colDefinition)) {
      switch ($key) {  // Handle different field display
        case 'item_id':
        $field = ( '<strong>' . $this->table_fields_get_link( $value ) . '</strong>' );
          break;      
        case 'location_id':
        $field = ( '<strong>' . $this->table_fields_get_link( $value ) . '</strong>' );
          break;
        case 'edit':
          $field = $this->table_fields_edit_button( $value ) . ' ' . $this->table_fields_delete_button( $value ) ; 
          break;                  
        default:
          $field = $value;
      }
      return '<td> ' . $field . '</td>';
    }
  }

   /**
   * Button to jump to timeframe table for editing //@TODO set path correctly
   *   
   * @return  html
   */ 
  private function table_fields_edit_button( $itemID ) {
    return '<a href="admin.php?page=cb_timeframes_edit&id=' . $itemID . '" class="button" style="visibility:visible">'.  __('Edit', 'commons-booking') . '</a>';
 
  }   
  /**
   * Button to jump to timeframe table for delete
   *   
   * @return  html
   */ 
  private function table_fields_delete_button( $itemID ) {
    return '<a href="admin.php?page=cb_timeframes&action=delete&id=' . $itemID . '" class="button" style="visibility:visible">'.  __('Delete', 'commons-booking') . '</a>';
 
  }

   /**
   * Button to jump to post type for editing
   *   
   * @param $itemID
   *
   * @return  html
   */ 
  private function table_fields_get_link( $itemID ) {    
    return '<a href="' . get_edit_post_link( $itemID ) . '">' . get_the_title( $itemID ) . '</a>';
    
  }
   /**
   * Jump to timeframe editing screen 
   *   
   *
   * @return  html
   */ 
  private function jump_to_timeframes_link() {    
     return ( '<a class="button" href="'. get_admin_url(get_current_blog_id()) . 'admin.php?page=cb_timeframes_edit&item_id='. $this->postID. '&new=1">' . __('Add new Timeframe', 'commons-booking') . '</a>' );    
  }


  /**
   * Gets objects from DB and displays message on fail
   *
   */
  private function get_Items() {

    $return = '';

    if ($this->postID) {
      global $wpdb;

      $table_name = $wpdb->prefix . 'cb_timeframes'; 
      $today = date('Y-m-d');
      $sql = $wpdb->prepare( "SELECT * FROM ". $table_name ." WHERE item_id = %d AND date_end > %s ORDER BY date_start ASC", $this->postID, $today );

      $this->items = $wpdb->get_results($sql, ARRAY_A);

      if ( $this->items ) {
          return $this->items;
        } else { 
          return FALSE;
        } 
    } else {
      return __('Post id not found.', 'commons-booking' );
    }
  }

  public function render_timeframes() {
    // $colDefinition = $this->set_Columns();
    $this->prepare();
    echo $this->jump_to_timeframes_link();
    $tableheader = $this->table_header();
    $tablecolumns = $this->table_columns();
    echo ('<table class="wp-list-table widefat fixed striped timeframe">' . $tableheader . $tablecolumns . '</table>');
  }

}
