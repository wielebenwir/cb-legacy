<?php

/**
 * Commons Booking
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @author    Christian Wenzel <christian@wielebenwir.de>
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
    	global $wpdb;
    	$this->user_id = get_current_user_id();  // get user id
    	$this->table_timeframe = $wpdb->prefix . 'cb_timeframes';
    	$this->table_codes = $wpdb->prefix . 'cb_codes';

		if (!$this->user_id) {
    		// error message and exit
    	}

    }

/**
 * Get location id based on booking-data and item
 *
 * @return array
 */
    public function get_booking_location_id( $date_start, $date_end, $item_id ) {
    	
    	global $wpdb;

    	// get location_id & item_id from timeframe-database
    	 $sqlresult = $wpdb->get_results($wpdb->prepare(
    	 	"
    	 	SELECT location_id 
	 		FROM " . $this->table_timeframe . " 
 			WHERE  date_start <= '%s' AND date_end >= '%s' AND item_id = '%s'
 			", 
 			$date_start, $date_end, $item_id), ARRAY_A); // get dates from db

    	 // @TODO: Insert check an error-handling if result-numer > 1

    	 return $sqlresult[0]['location_id'];

     }
    
 /**
 * Get booking-code based on start date and item-id
 *
 * @return array
 */   
    public function get_booking_code_id( $date_start, $item_id ) {
    	
    	global $wpdb;

    	// get booking_code-id fromt codes database
    	 $sqlresult = $wpdb->get_results($wpdb->prepare(
    	 	"
    	 	SELECT id AS booking_code_id
    	 	FROM " . $this->table_codes . " 
    	 	WHERE booking_date = '%s' AND item_id = '%s'
    	 	", 
    	 	$date_start, $item_id), ARRAY_A); // get dates from 
    	 
    	 // @TODO: Insert check an error-handling if result-numer > 1

    	 return $sqlresult[0]['booking_code_id'];

    }

 /**
 * Get bookinc-code text
 *
 * @return array
 */   
    public function get_code( $code_id ) {
    	
    	global $wpdb;

		$sqlresult = $wpdb->get_row("SELECT * FROM $this->table_codes WHERE id = $code_id", ARRAY_A);

    	return $sqlresult['bookingcode'];

    }


 /**
 * Get item-data title
 *
 * @return array
 */   
    public function get_item( $posts_id ) {
    	
    	global $wpdb;

		$sqlresult = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = $posts_id", ARRAY_A);

		$item['item_title'] = $sqlresult['post_title'];

    	return $item['item_title'];

    }

 /**
 * Get location-data
 *
 * @return array
 */   
    public function get_location( $posts_id ) {
    	
    	global $wpdb;

		$sqlresult_posts = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = $posts_id", ARRAY_A);

		$item['location_title'] = $sqlresult_posts['post_title'];

		//get meta-data
		$sqlresult_meta = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE post_id = $posts_id AND meta_key = 'commons-booking_location_contactinformation'", ARRAY_A);

		$item['location_contactinformation'] = $sqlresult_meta['meta_value'];

    	return $item;

    }

 /**
 * Get booking-userdata
 *
 * @return array
 */   
    public function get_booking_user( $user_id ) {
    	
    	$userdata = get_userdata( $user_id );

    	$user['name'] 			= $userdata->user_nicename;
    	$user['email'] 			= $userdata->user_email;
    	$user['address']		= "ADRESSFELD IN DATENBANK FEHLT"; //@TODO: Adresse in Userdaten integrieren
    	$user['phone']			= "TELEFONNUMMER-FELD IN DATENBANK FEHLT"; //@TODO: Telefonnummer in Userdaten integrieren

    	return $user;

    }




 /**
 * Store all booking relevant data into booking-table, set status pending. Return booking_id
 *
 * @return array
 */   
    public function create_booking( $date_start, $date_end, $item_id ) {
    	
    	global $wpdb;
    	$table_bookings = $wpdb->prefix . 'cb_bookings';

    	// get relevant data
    	$code_id = $this->get_booking_code_id( $date_start, $item_id );
    	$location_id = $this->get_booking_location_id ( $date_start, $date_end, $item_id );

    	$wpdb->insert( 
			$table_bookings, 
			array( 
				'date_start' 	=> $date_start , 
				'date_end' 		=> $date_end,
				'item_id' 		=> $item_id,
				'user_id' 		=> $this->user_id, 
				'code_id' 		=> $code_id,
				'location_id' 	=> $location_id,
				'booking_time' 	=> date('Y-m-d H:i:s'),
				'status' => 'pending'
			), 
				array( 
				'%s', 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s' 
			) 
		);

		return $wpdb->insert_id;
	}



 /**
 * get all booking-date as array
 *
 * @return array
 */   
    public function get_booking( $booking_id ) {

    	$booking_user = new WP_User;
    	
    	global $wpdb;
    	$table_bookings = $wpdb->prefix . 'cb_bookings';

    	$sqlresult = $wpdb->get_row("SELECT * FROM $table_bookings WHERE id = $booking_id", ARRAY_A);

    	$booking_data['id']				= $sqlresult['id'];
    	$booking_data['date_start']		= $sqlresult['date_start'];
    	$booking_data['date_end']		= $sqlresult['date_end'];
    	$booking_data['item']			= $this->get_item( $sqlresult['item_id'] );
    	$booking_data['user']			= $this->get_booking_user( $sqlresult['user_id'] );
    	$booking_data['location']		= $this->get_location( $sqlresult['location_id']);
    	$booking_data['booking_time']	= $sqlresult['booking_time'];
    	$booking_data['status']			= $sqlresult['status'];

    	return $booking_data;
    }

}
?>