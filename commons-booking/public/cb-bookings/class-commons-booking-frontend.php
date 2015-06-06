<?php

/**
 * Commons Booking
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * This class includes all frontend functions for bookings
 * *
 * @package Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 *
 */

class Commons_Booking_Frontend {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.0.1
     *
     * @var     string
     */
    const VERSION = '0.0.1';

    public function __construct() {
    	$this->user_id = get_current_user_id();  // get user id

    }


    public function collect_booking_data($date_start, $date_end, $item_id) {

    	global $wpdb;
    	$table_timeframe = $wpdb->prefix . 'cb_timeframes';
    	$table_codes = $wpdb->prefix . 'cb_codes';

    	if (!$this->user_id) {
    		// error message and exit
    	}


    	// get location_id & item_id from timeframe-database
    	 $booking_data[] = $wpdb->get_results($wpdb->prepare("SELECT item_id, location_id FROM " . $table_timeframe . " WHERE date_start <= '%s' AND date_end >= '%s'", $date_start, $date_end), ARRAY_A); // get dates from db
    	 $timeframe_data;


    	// get booking_code-id fromt codes database
    	 $booking_data[] = $wpdb->get_results($wpdb->prepare("SELECT id FROM " . $table_codes . " WHERE booking_date = '%s' AND item_id = '%s'", $date_start, $item_id), ARRAY_A); // get dates from 
    	 

    	 return $booking_data;

    }
 }
?>