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
    	$location_id = $this->get_booking_location_id( $date_start, $date_end, $item_id );
        // $this->get_booking_location_id ( $date_start, $date_end, $item_id );

    	//@TODO: check if identical booking is already in database and cancel booking proucess if its true

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
 * get all booking-dataa as array
 *
 * @return array
 */   
    public function get_booking( $booking_id ) {
    	
    	global $wpdb;
    	$table_bookings = $wpdb->prefix . 'cb_bookings';

    	$sqlresult = $wpdb->get_row("SELECT * FROM $table_bookings WHERE id = $booking_id", ARRAY_A);

    	$booking_data['id']				= $sqlresult['id'];
    	$booking_data['date_start']		= $sqlresult['date_start'];
    	$booking_data['date_end']		= $sqlresult['date_end'];
    	$booking_data['item']			= $this->get_item( $sqlresult['item_id'] );
    	$booking_data['code']			= $this->get_code( $sqlresult['code_id'] );
    	$booking_data['user']			= $this->get_booking_user( $sqlresult['user_id'] );
    	$booking_data['location']		= $this->get_location( $sqlresult['location_id']);
    	$booking_data['booking_time']	= $sqlresult['bookingtime'];
    	$booking_data['status']			= $sqlresult['status'];

    	return $booking_data;
    }

 /**
 * set status in booking table.
 * parameter: booking_id (id), status = new statu (e.g. "confirmed")
 *
 * @return array
 */   
    public function set_booking_status( $booking_id, $status ) {
    	
    	global $wpdb;
    	$table_bookings = $wpdb->prefix . 'cb_bookings';

		$wpdb->query(
			"
			UPDATE $table_bookings 
			SET status = '" . $status . "'
			WHERE id = $booking_id
			"
		);

		return;

    }

    public function render_bookingreview( ) {
          if (is_user_logged_in() ) {

           if ( !empty($_REQUEST['create']) && $_REQUEST['create'] == 1) { // we create a new booking

               if ( !empty($_REQUEST['date_start']) && !empty($_REQUEST['date_end']) && !empty($_REQUEST['timeframe_id']) && !empty($_REQUEST['item_id']) && !empty($_REQUEST['location_id']) && !empty($_REQUEST['_wpnonce']) ) { // all needed vars available

                  if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-review-nonce') ) die("Security check");

                     $this->date_start = ( $_REQUEST['date_start'] );  
                     $this->date_end = ( $_REQUEST['date_end'] );  

                     $this->nice_date_start = date_i18n( get_option( 'date_format' ), $this->date_start );
                     $this->nice_date_end = date_i18n( get_option( 'date_format' ), $this->date_end );

                     $this->location_id = ( $_REQUEST['location_id'] );  
                     $this->item_id = ( $_REQUEST['item_id'] );  
                     $this->timeframe_id = ( $_REQUEST['timeframe_id'] );  

                    include (commons_booking_get_template_part( 'booking', 'review', FALSE )); // include the template

                    // write to DB
                    $booking_id = $this->create_booking( date( 'Y-m-d', $this->date_start), date( 'Y-m-d', $this->date_end ), $this->item_id );
                    
                    include (commons_booking_get_template_part( 'booking', 'submit', FALSE )); // include the template

                    echo ("<h2>". $booking_id ."</h2>");

              } else { // not all needed vars available 
                echo "Error";
                die();
              }
            } else if ( !empty($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1 ) { // we confirm the booking 

                if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-confirm-nonce') ) die("Security check");

             
                echo ("confirmed!");
                $booking_id = ( $_REQUEST['booking_id'] );  

                $this->set_booking_status( $booking_id, 'confirmed' );


            } // end if confirm
          

          }else { // not logged in     
            echo "You ahve to be logged in.";
        } // end if logged in 




    }



}
?>