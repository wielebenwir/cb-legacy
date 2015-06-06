<?php
/**
 * Define table
 */



/**
 * $cb_timeframes_table_db_version - holds current database version
 * and used on plugin update to sync database tables
 */
global $cb_timeframes_table_db_version;
$cb_timeframes_table_db_version = '0.1'; // version changed from 1.0 to 0.1

/**
 * register_activation_hook implementation
 *
 * will be called when user activates plugin first time
 * must create needed database tables
 */
function cb_timeframes_table_install()
{
    global $wpdb;
    global $cb_timeframes_table_db_version;

    $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

    // sql to create your table
    // NOTICE that:
    // 1. each field MUST be in separate line
    // 2. There must be two spaces between PRIMARY KEY and its name
    //    Like this: PRIMARY KEY[space][space](id)
    // otherwise dbDelta will not work
    $sql = "CREATE TABLE " . $table_name . " (
      id int(11) unsigned NOT NULL AUTO_INCREMENT,
      timeframe_title text,
      item_id int(11) DEFAULT NULL,
      location_id int(11) DEFAULT NULL,
      date_start date DEFAULT NULL,
      date_end date DEFAULT NULL,
      PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    // add_option('cb_timeframes_table_db_version', $cb_timeframes_table_db_version);

    /**
     * [OPTIONAL] Example of updating to 0.1 version
     *
     * If you develop new version of plugin
     * just increment $cb_timeframes_table_db_version variable
     * and add following block of code
     *
     * must be repeated for each new version
     * in version 0.1 we change email field
     * to contain 200 chars rather 100 in version 1.0
     * and again we are not executing sql
     * we are using dbDelta to migrate table changes
     */
    
    // $installed_ver = get_option('cb_timeframes_table_db_version');
    // if ($installed_ver != $cb_timeframes_table_db_version) {
    //     $sql = "CREATE TABLE " . $table_name . " (
    //       id int(11) unsigned NOT NULL AUTO_INCREMENT,
    //       timeframe_title text,
    //       item_id int(11) DEFAULT NULL,
    //       location_id int(11) DEFAULT NULL,
    //       date_start date DEFAULT NULL,
    //       date_end date DEFAULT NULL,
    //       PRIMARY KEY (id)
    //     );";

    //     require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //     dbDelta($sql);

    //     // notice that we are updating option, rather than adding it
    //     update_option('cb_timeframes_table_db_version', $cb_timeframes_table_db_version);
    // }
}

register_activation_hook(__FILE__, 'cb_timeframes_table_install');

/**
 * register_activation_hook implementation
 *
 * [OPTIONAL]
 * additional implementation of register_activation_hook
 * to insert some dummy data
 */
function cb_timeframes_table_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cb_timeframes'; // do not forget about tables prefix

    $wpdb->insert($table_name, array(
        'timeframe_title' => 'Test'
    ));
}

register_activation_hook(__FILE__, 'cb_timeframes_table_install_data');

/**
 * Trick to update plugin database, see docs
 */
function cb_timeframes_table_update_db_check()
{
    global $cb_timeframes_table_db_version;
    if (get_site_option('cb_timeframes_table_db_version') != $cb_timeframes_table_db_version) {
        cb_timeframes_table_install();
    }
}

add_action('plugins_loaded', 'cb_timeframes_table_update_db_check');

?>