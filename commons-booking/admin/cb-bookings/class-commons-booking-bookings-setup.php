<?php
/**
 *
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de
 * @author    Christian Wenzel <christian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Handles install & update of the Bookings Table
 *
 * @package Commons_Booking_Booking_Setup
 * @author  Florian Egermann <florian@wielebenwir.de>
 */


class Commons_Booking_Bookings_Setup {

  public $table_db_version = '0.1'; 

  public function __construct () {

    $this->table_db_version = '0.1';

  }

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
public function install()
{

    global $wpdb;
    $table_name = $wpdb->prefix . 'cb_bookings'; // do not forget about tables prefix

    // sql to create your table
    // NOTICE that:
    // 1. each field MUST be in separate line
    // 2. There must be two spaces between PRIMARY KEY and its name
    //    Like this: PRIMARY KEY[space][space](id)
    // otherwise dbDelta will not work
    $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        date_start date NOT NULL,
        date_end date NOT NULL,
        item_id int(11) NOT NULL,
        user_id int(11) NOT NULL,
        code_id int(11) NOT NULL,
        location_id int(11) NOT NULL,
        booking_time datetime NOT NULL,
        status varchar(50) NOT NULL,
        hash varchar(50) NOT NULL,
        comment text,
        PRIMARY KEY  (id)
      );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('commons_booking_bookings_table_db_version', $this->table_db_version);

    /**
     * [OPTIONAL] Example of updating to 0.1 version
     *
     * If you develop new version of plugin
     * just increment $this->table_db_version variable
     * and add following block of code
     *
     * must be repeated for each new version
     * in version 0.1 we change email field
     * to contain 200 chars rather 100 in version 1.0
     * and again we are not executing sql
     * we are using dbDelta to migrate table changes
     */
    
    $installed_ver = get_option('commons_booking_bookings_table_db_version');
    if ($installed_ver != $this->table_db_version) {
      $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        date_start date NOT NULL,
        date_end date NOT NULL,
        item_id int(11) NOT NULL,
        user_id int(11) NOT NULL,
        code_id int(11) NOT NULL,
        location_id int(11) NOT NULL,
        booking_time datetime NOT NULL,
        status varchar(50) NOT NULL,
        hash varchar(50) NOT NULL,
        comment text,
        PRIMARY KEY  (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('commons_booking_bookings_table_db_version', $this->table_db_version);
    }
}


/**
 * register_activation_hook implementation
 *
 * [OPTIONAL]
 * additional implementation of register_activation_hook
 * to insert some dummy data
 */
public function install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cb_bookings'; // do not forget about tables prefix

    $wpdb->insert($table_name, array(
        'date_start' => '2015-01-01'
    ));
}


/**
 * Trick to update plugin database, see docs
 */
public function update_db_check()
{
    if (get_site_option('cb_bookings_table_db_version') != $this->table_db_version) {
        cb_bookings_table_install();
    }
  }
}

?>