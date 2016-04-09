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
 * Handles install & update of the Codes Table
 *
 * @package Commons_Booking_Codes_Setup
 * @author  Florian Egermann <florian@wielebenwir.de>
 */


class Commons_Booking_Codes_Setup {

  public $table_db_version = '0.1'; // version changed from 1.0 to 0.1

  public function __construct () {

    $this->table_db_version = '0.1'; // version changed from 1.0 to 0.1

  }

public function install()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_codes'; // do not forget about tables prefix

    // sql to create your table
    // NOTICE that:
    // 1. each field MUST be in separate line
    // 2. There must be two spaces between PRIMARY KEY and its name
    //    Like this: PRIMARY KEY[space][space](id)
    // otherwise dbDelta will not work
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) AUTO_INCREMENT,
      bookingcode text CHARACTER SET utf8,        
      item_id int(11) DEFAULT NULL,
      booking_date date DEFAULT NULL,
      PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('commons_booking_codes_table_db_version', $this->table_db_version);

    /**
     * [OPTIONAL] Example of updating to 0.1 version
     *
     * If you develop new version of plugin
     * just increment $cb_codes_$this->table_db_version variable
     * and add following block of code
     *
     */
    $installed_ver = get_option('cb_codes_$this->table_db_version');
    if ($installed_ver != $this->table_db_version) {
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) AUTO_INCREMENT,
      bookingcode text CHARACTER SET utf8,        
      item_id int(11) DEFAULT NULL,
      booking_date date DEFAULT NULL,
      PRIMARY KEY  (id)
    );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

       // notice that we are updating option, rather than adding it
        update_option('commons_booking_codes_table_db_version', $this->table_db_version);
    }
}


/**
 * register_activation_hook implementation
 *
 * [OPTIONAL]
 * additional implementation of register_activation_hook
 * to insert some dummy data
 */
public function cb_codes_table_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cb_codes'; // do not forget about tables prefix

}


/**
 * Trick to update plugin database, see docs
 */
public function cb_codes_table_update_db_check()
{
    if (get_site_option('cb_codes_table_db_version') != $this->table_db_version) {
        cb_codes_table_install();
    }
}
}
?>