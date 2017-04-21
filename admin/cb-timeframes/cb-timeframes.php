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
 * Include all necessary files for Timeframes
 *
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

// @TODO Re-Organize.

// Install
require_once( plugin_dir_path( __FILE__ ) . 'cb-timeframes-setup.php' );

// List
require_once( plugin_dir_path( __FILE__ ) . 'cb-timeframes-list.php' );

// Edit 
require_once( plugin_dir_path( __FILE__ ) . 'cb-timeframes-edit.php' );

// Displays the List of Timeframes at  Item edit screen
require_once( plugin_dir_path( __FILE__ ) . 'includes/class-cb-timeframes-list.php' );

?>