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
        $this->prefix = 'commons-booking';

    	global $wpdb;
    	$this->user_id = get_current_user_id();  // get user id
    	$this->table_timeframe = $wpdb->prefix . 'cb_timeframes';
        $this->table_codes = $wpdb->prefix . 'cb_codes';
    	$this->table_bookings = $wpdb->prefix . 'cb_bookings';

		if (!$this->user_id) {
            die ( ' No user id' );
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
        WHERE date_start > '%s' AND item_id = '%s' AND status = '%s'
        ", 
        $currentdate , $item_id, 'confirmed'), ARRAY_A); // get dates from 
     
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
    public function send_mail( $to ) {

        $body_template = ( $this->email_messages['mail_confirmation_body'] );  // get template
        $subject_template = ( $this->email_messages['mail_confirmation_subject'] );  // get template
    	
        $headers = array('Content-Type: text/html; charset=UTF-8');

        echo $body_template;

        $body = replace_template_tags( $body_template, $this->b_vars);
        $subject = replace_template_tags( $subject_template, $this->b_vars);
        var_dump($this->b_vars);

        wp_mail( $to, $subject, $body, $headers );

    }


    private function validate_booking ( $id, $date_start, $date_end ) {
        $booked_days = $this->get_booked_days ( $id );
        if ( in_array( $date_start, $booked_days ) OR in_array( $date_end, $booked_days ) ) {
            die ('Day already booked');
        } else {
            return TRUE;
        }
    }


    private function set_booking_vars( $include_code = FALSE ) {

        $data = new Commons_Booking_Data;

        $this->item = $data->get_item( $this->item_id );
        $this->location = $data->get_location( $this->location_id );
        $this->user = $data->get_user( $this->user_id );


        $b_vars['date_start'] = date_i18n( get_option( 'date_format' ), strtotime( $this->date_start ) );
        $b_vars['date_end'] = date_i18n( get_option( 'date_format' ), strtotime( $this->date_end ) );
        $b_vars['item_name'] = get_the_title ($this->item_id );
        $b_vars['item_thumb'] = get_thumb( $this->item_id ); 
        $b_vars['item_content'] = get_the_content( $this->item_id );
        $b_vars['location_name'] = get_the_title ($this->location_id );
        $b_vars['location_content'] = get_the_content( $this->location_id  );
        $b_vars['location_address'] = implode(', ', $this->location['address']);
        $b_vars['location_thumb'] = get_thumb( $this->location_id ); 
        $b_vars['location_contact'] = $this->location['contact']; 
        $b_vars['location_openinghours'] = $this->location['openinghours']; 
        
        $b_vars['site_email'] = $this->email_messages['mail_confirmation_sender']; 

        $b_vars['user_name'] = $this->user['name'];
        $b_vars['user_email'] = $this->user['email'];    
        $b_vars['user_address'] = $this->user['address'];    
        $b_vars['user_phone'] = $this->user['phone'];    
        if ( $include_code ) {
            $b_vars['code'] = $this->get_code( $this->booking['code_id'] ); 
        }
        $this->b_vars = $b_vars;

    }

    public function render_bookingreview( ) {
          if (is_user_logged_in() ) {

            $current_user = wp_get_current_user();

            $booking_messages = $this->settings->get( 'messages' ); // get messages templates from settings page
            $this->email_messages = $this->settings->get( 'mail' ); // get email templates from settings page

            if ( !empty($_REQUEST['create']) && $_REQUEST['create'] == 1) { // we create a new booking

               if ( !empty($_REQUEST['date_start']) && !empty($_REQUEST['date_end']) && !empty($_REQUEST['timeframe_id']) && !empty($_REQUEST['item_id']) && !empty($_REQUEST['location_id']) && !empty($_REQUEST['_wpnonce']) ) { // all needed vars available

                  if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-review-nonce') ) die ('Your session has expired');

                    // DATA FROM FORM
                    $this->date_start = ( $_REQUEST['date_start'] );  
                    $this->date_end = ( $_REQUEST['date_end'] );  
                    $this->location_id = ( $_REQUEST['location_id'] );  
                    $this->item_id = ( $_REQUEST['item_id'] );  
                    $this->timeframe_id = ( $_REQUEST['timeframe_id'] );  
                    $this->user_id = get_current_user_id();

                    // Set Variable for Template
                    $this->set_booking_vars();

                    // check if validated
                    if ( $this->validate_booking( $this->item_id, $this->date_start, $this->date_end )) {

                        $msg = ( $booking_messages['messages_booking_pleaseconfirm'] );  // get message part
                        echo replace_template_tags ( $msg, $this->b_vars); // replace template tags
                        // include the template
                        include ( 'views/booking-review.php' );
                                            
                        // write to DB
                        $booking_id = $this->create_booking( date( 'Y-m-d', $this->date_start), date( 'Y-m-d', $this->date_end ), $this->item_id );
                        // Include the submit button
                        include (commons_booking_get_template_part( 'booking', 'submit', FALSE )); // include the template

                    } // end if validated

                } else { // not all needed vars available 
                    echo "Error";
              }
            } else if ( !empty($_REQUEST['confirm']) && $_REQUEST['confirm'] == 1 ) { // we confirm the booking 

                if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-confirm-nonce') ) die("Security check");

                    // DATA FROM FORM
                    $booking_id = ( $_REQUEST['booking_id'] );  

                    // DATA FROM DB
                    $this->booking = $this->get_booking( $booking_id );
                    $this->date_start = ( $this->booking['date_start'] );  
                    $this->date_end = ( $this->booking['date_end'] ); 
                    $this->location_id = ( $this->booking['location_id'] );  
                    $this->item_id = ( $this->booking['item_id'] ); 
                    $this->user_id = ( $this->booking['user_id'] );
 
                    // Finalise the booking
                    $this->set_booking_status( $booking_id, 'confirmed' ); // set booking status to confirmed
                    // Set Variable for Template
                    $this->set_booking_vars( TRUE );
                    // Display the Message
                    $msg = ( $booking_messages['messages_booking_confirmed'] );  // get message
                    
                    echo replace_template_tags ( $msg, $this->b_vars ); // replace template tags

                    include (commons_booking_get_template_part( 'booking', 'code', FALSE )); // include the template
                  
                    include ( 'views/booking-review.php' );

                    $this->send_mail( $this->user['email'] );


            } // end if confirm
          

          } else { // not logged in     
            echo "You have to be logged in to book.";
        } // end if logged in 




    }



}
?>