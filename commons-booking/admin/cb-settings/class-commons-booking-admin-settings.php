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
 * Class for settings, get admin defined values from backend.  
 *
 * @package Commons_Booking_Data
 * @author  Florian Egermann <florian@macht-medien.de>
 */
class Commons_Booking_Admin_Settings {

  public $setting_page;
  public $setting_name;

/**
 * Constructor.
 *
 */
  public function __construct() {
    $this->prefix = 'commons-booking';
}  

/**
 * Get settings from backend. Return either full array or specified setting
 * If array, remove the prefix for easier retrieval
 *
 *@param setting_page: name of the page (cmb metabox name)
 *@param (optional) name of the setting
 *@param (optional) key/value pairs of the template tags
 *
 *@return string / array
 */
  public function get( $setting_page, $setting_name = "") {
    global $wpdb;
    $page = get_option( $this->prefix . '-settings-' .$setting_page ); 
    if ( $setting_name ) {
      return $page [ $this->prefix . '_'. $setting_name ];
    } else { // grabbing all entries
     
      foreach($page as $key => $value) { // remove the prefix 
            $clean = str_replace( $this->prefix. '_', '', $key); 
            $clean_array[$clean] = $value; 
        }
      return $clean_array;
    }
  }

/**
 * Get settings from backend. Return either full array or specified setting
 * If array, remove the prefix for easier retrieval
 *
 *@param setting_page: name of the page (cmb metabox name)
 *@param (optional) name of the setting
 *
 *@return string
 */
  public function replace_template_tags( $string, $tags_array ) {
    foreach($tags_array as $key => $value){
        $string = str_replace('{'.strtoupper($key).'}', $value, $string);
    }
    return $string;
  }


}

?>