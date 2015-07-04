<?php

/**
 * Commons Booking Bookings Class
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
 * @package Commons_Booking_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 *
 */

class Commons_Booking_Booking {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.0.1
     *
     * @var     string
     */
    const VERSION = '0.0.1';

    public function __construct() {

        $this->settings = new Commons_Booking_Admin_Settings();

    	global $wpdb;
    	$this->user_id = get_current_user_id();  // get user id
    	$this->table_timeframe = $wpdb->prefix . 'cb_timeframes';
        $this->table_codes = $wpdb->prefix . 'cb_codes';
    	$this->table_bookings = $wpdb->prefix . 'cb_bookings';

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
 * Get a list of all booked days
 *
 * @return array
 */  
public function get_booked_days( $item_id ) {
    
    global $wpdb;

    $currentdate = date( 'Y-m-d');

    // get booking_code-id fromt codes database
     $sqlresult = $wpdb->get_results($wpdb->prepare(
        "
        SELECT date_start, date_end
        FROM " . $this->table_bookings . " 
        WHERE date_start > '%s' AND item_id = '%s'
        ", 
        $currentdate , $item_id), ARRAY_A); // get dates from 
     
     $booked_days = [];


     foreach ($sqlresult as $date) {
        // var_dump( $date ) ;
        $datediff = strtotime( $date['date_end'] ) - strtotime( $date['date_start'] );
        $datediff = floor( $datediff / ( 60*60*24 ));
        for($i = 0; $i < $datediff + 1; $i++){
            // echo date("Y-m-d", strtotime( $date['date_start'] . ' + ' . $i . 'day')) . "<br>";
            $thedate = date("Y-m-d", strtotime( $date['date_start'] . ' + ' . $i . 'day'));
            array_push( $booked_days, strtotime ($thedate) );
        }
     }
     return $booked_days;

}


 /**
 * Store all booking relevant data into booking-table, set status pending. Return booking_id
 *
 * @return array
 */   
    public function create_booking( $date_start, $date_end, $item_id ) {
    	
    	global $wpdb;
    	// $table_bookings = $wpdb->prefix . 'cb_bookings';

    	// get relevant data
    	$code_id = $this->get_booking_code_id( $date_start, $item_id );
    	$location_id = $this->get_booking_location_id( $date_start, $date_end, $item_id );

    	//@TODO: check if identical booking is already in database and cancel booking proucess if its true

    	$wpdb->insert( 
			$this->table_bookings, 
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
    	$booking_data['item_id']			= $sqlresult['item_id'];
    	$booking_data['code_id']			= $sqlresult['code_id'];
    	$booking_data['user_id']			= $sqlresult['user_id'];
    	$booking_data['location_id']		= $sqlresult['location_id'];
    	$booking_data['booking_time']	= $sqlresult['booking_time'];
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
/**
 * Sends the confirm booking email.
 * 
 *@param $to, $subject, $message
 * @return array
 */   
    private function send_mail( $to, $subject, $message ) {
    	
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $to, $subject, $body, $headers );

    }






    public function render_bookingreview( ) {
          if (is_user_logged_in() ) {

            $current_user = wp_get_current_user();

            $messages = $this->settings->get( 'messages' ); // get messages from settings page

            if ( !empty($_REQUEST['create']) && $_REQUEST['create'] == 1) { // we create a new booking

               if ( !empty($_REQUEST['date_start']) && !empty($_REQUEST['date_end']) && !empty($_REQUEST['timeframe_id']) && !empty($_REQUEST['item_id']) && !empty($_REQUEST['location_id']) && !empty($_REQUEST['_wpnonce']) ) { // all needed vars available

                  if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-review-nonce') ) die("Security check");

                    // DATA FROM FORM
                     $date_start = ( $_REQUEST['date_start'] );  
                     $date_end = ( $_REQUEST['date_end'] );  

                     $nice_date_start = date_i18n( get_option( 'date_format' ), $date_start );
                     $nice_date_end = date_i18n( get_option( 'date_format' ), $date_end );

                     $location_id = ( $_REQUEST['location_id'] );  
                     $item_id = ( $_REQUEST['item_id'] );  
                     $timeframe_id = ( $_REQUEST['timeframe_id'] );  

                     // DATA FROM DB
                    $data = new Commons_Booking_Data;

                    $user_id = get_current_user_id();

                    $item = $data->get_item( $item_id );
                    $location = $data->get_location( $location_id );
                    $user = $data->get_user( $user_id );

                    $msg = ( $messages['messages_booking_pleaseconfirm'] );  // get message part
                    echo replace_template_tags ( $msg, array( 
                        'item' => get_the_title ( $item_id ),
                        'username' => $current_user->display_name,
                        'email' => $current_user->user_email,
                        )); // replace template tags

                    include (commons_booking_get_template_part( 'booking', 'item', FALSE )); // Item: include the template

                    include (commons_booking_get_template_part( 'booking', 'review', FALSE )); // B Review: include the template

                    include (commons_booking_get_template_part( 'booking', 'location', FALSE )); // Location: include the template
                    
                    include (commons_booking_get_template_part( 'booking', 'user', FALSE )); // Location: include the template
                    
                    // write to DB
                    $booking_id = $this->create_booking( date( 'Y-m-d', $date_start), date( 'Y-m-d', $date_end ), $item_id );
                    
                    include (commons_booking_get_template_part( 'booking', 'submit', FALSE )); // include the template

                } else { // not all needed vars available 
                    echo "Error";
              }
            } else if ( !empty($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1 ) { // we confirm the booking 

                if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-confirm-nonce') ) die("Security check");

                    $booking_id = ( $_REQUEST['booking_id'] );  
                    $booking = $this->get_booking( $booking_id );

                    $this->set_booking_status( $booking_id, 'confirmed' ); // set booking status to confirmed

                    $date_start = ( $booking['date_start'] );  
                    $date_end = ( $booking['date_end'] ); 

                    $nice_date_start = date_i18n( get_option( 'date_format' ), strtotime( $date_start ) );
                    $nice_date_end = date_i18n( get_option( 'date_format' ),  strtotime( $date_end ) );

                    $location_id = ( $booking['location_id'] );  
                    $item_id = ( $booking['item_id'] );  

                    $code = $this->get_code( $booking['code_id'] );  

                    $data = new Commons_Booking_Data;

                    $user_id = get_current_user_id();

                    $item = $data->get_item( $item_id );
                    $location = $data->get_location( $location_id );
                    $user = $data->get_user( $user_id );

                    $msg = ( $messages['messages_booking_confirmed'] );  // get message
                    echo replace_template_tags ( $msg, array( 
                        'item' => get_the_title ( $item_id ),
                        'username' => $user['name'],
                        'useremail' => $user['email'],
                        )); // replace template tags

                    include (commons_booking_get_template_part( 'booking', 'item', FALSE )); // Item: include the template

                    include (commons_booking_get_template_part( 'booking', 'code', FALSE )); // Code: include the template

                    include (commons_booking_get_template_part( 'booking', 'review', FALSE )); // B Review: include the template

                    include (commons_booking_get_template_part( 'booking', 'location', FALSE )); // Location: include the template
                    
                    include (commons_booking_get_template_part( 'booking', 'user', FALSE )); // Location: include the template

            } // end if confirm
          

          }else { // not logged in     
            echo "You ahve to be logged in.";
        } // end if logged in 




    }



}
?>