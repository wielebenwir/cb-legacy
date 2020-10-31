<?php

/**
 * Commons Booking Bookings Class
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @author    Christian Wenzel <christian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * This class includes all frontend functions for bookings
 * *
 * @package Commons_Booking_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 *
 */

class CB_Booking {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.0.1
     *
     * @var     string
     */

    public function __construct() {

        $this->settings = new CB_Admin_Settings();
        $this->data = new CB_Data;
        $this->booking_comments = new CB_Booking_Comments;

        $this->prefix = 'commons-booking';

        global $wpdb;
        $this->user_id = get_current_user_id();  // get user id
        $this->table_timeframe = $wpdb->prefix . 'cb_timeframes';
        $this->table_codes = $wpdb->prefix . 'cb_codes';
        $this->table_bookings = $wpdb->prefix . 'cb_bookings';

        // Interface so that foreign classes can access this class'
        // functionalities via the wordpress action API
        // (https://developer.wordpress.org/reference/functions/add_action/ and
        // https://wordpress.stackexchange.com/questions/44708/using-a-plugin-class-inside-a-template)
        add_action( 'cb_booking_send_delete_emails', array ( $this, 'send_delete_emails' ), 10, 1 );
        add_action( 'cb_booking_send_location_change_emails', array ( $this, 'send_location_change_emails' ), 10, 2 );
    }

    /**
     * Can send deletion emails from another class via an action. The argument
     * is the booking_ID to send deletion emails for.
     *
     * Usage:
     *    <code>do_action( 'cb_booking_send_delete_emails', 42);</code>
     *
     * @param string $b_id The booking_ID
     * @return void
     */
    public function send_delete_emails( $b_id ) {
      $booking = $this->get_booking($b_id);
      $this->prepare_for_set_booking_vars($booking);
      // Set variable for template
      $this->set_booking_vars();
      $this->send_mail( $this->user['email'], true, 'deletion' );
      $email_addresses = $this->user['email'];
      if ( !empty( $this->recv_copies ) && $this->location_email ) {
        foreach ($this->location_email as $email) {
          $this->send_mail( $email, false, 'deletion' );
          $email_addresses = $email_addresses.', '.$email;
        }
      }
      $msg = __( 'Notification about the booking deletion has been sent to the following email addresses', 'commons-booking' );
      new Admin_Table_Message ( "$msg: $email_addresses.", 'updated' );
    }

    /**
     * Can send location change emails from another class via an action. The argument
     * is the booking_ID to send notification of the location change for. Use
     * this function after you have changed the location_id of the booking to
     * the new location so that the information on the new location is there.
     *
     * Usage:
     *    <code>do_action( 'cb_booking_send_location_change_emails', 42);</code>
     *
     * @param string $b_id The booking_ID
     * @param string $old_loc_id The ID of the old location that you might want
     *   to notify as well.
     * @return void
     */
    public function send_location_change_emails( $b_id, $old_loc_id = NULL ) {
      $booking = $this->get_booking($b_id);
      $this->prepare_for_set_booking_vars($booking);
      // Set variable for template
      $this->set_booking_vars(TRUE);
      $this->send_mail( $this->user['email'], true, 'location_change' );
      $email_addresses = $this->user['email'];
      if ( !empty( $this->recv_copies ) && $this->location_email ) {
        foreach ($this->location_email as $email) {
          $this->send_mail( $email, false, 'location_change' );
          $email_addresses = $email_addresses.', '.$email;
        }
      }
      if ($old_loc_id) {
        $old_location = $this->data->get_location( $old_loc_id );
        $old_recv_copies = ( $old_location['recv_copies'] );
        $old_location_email = (
          count( $old_location['contact']['email'] ) > 0 ?
          $old_location['contact']['email'] :
          NULL
        );
        if ( !empty( $old_recv_copies ) && $old_location_email ) {
          foreach ($old_location_email as $email) {
            $this->send_mail( $email, false, 'location_change' );
            $email_addresses = $email_addresses.', '.$email;
          }
        }
      }

      $msg = __( 'Notification about the changed location of booking %d has been sent to the following email addresses', 'commons-booking' );
      $msg = sprintf($msg, $b_id);
      new Admin_Table_Message ( "$msg: $email_addresses.", 'updated' );
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
 * Create a hash for booking url
 *
 * @return string - hash
 */
    public function create_hash( ) {

        $hash = uniqid();
        return $hash;
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
 /* NOT_CURRENTLY_USED, see CB_Data->get_location($location_id)
    public function get_location( $posts_id ) {

        global $wpdb;

        $sqlresult_posts = $wpdb->get_row("SELECT * FROM $wpdb->posts WHERE id = $posts_id", ARRAY_A);

        $item['location_title'] = $sqlresult_posts['post_title'];

        //get meta-data
        $sqlresult_meta = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE post_id = $posts_id AND meta_key = 'commons-booking_location_contactinformation'", ARRAY_A);

        $item['location_contactinformation'] = $sqlresult_meta['meta_value'];

        return $item;

    }
*/

 /**
 * Get a list of all booked days
 *
 * @return array
 */
public function get_booked_days( $item_id, $status= 'confirmed' ) {

    global $wpdb;

    // get booking_code-id fromt codes database
     $sqlresult = $wpdb->get_results($wpdb->prepare(
        "
        SELECT date_start, date_end
        FROM " . $this->table_bookings . "
        WHERE item_id = '%s' AND status = '%s'
        ",
        $item_id, $status), ARRAY_A); // get dates from

     $booked_days = array();

     foreach ($sqlresult as $date) {

        $datediff = strtotime( $date['date_end'] ) - strtotime( $date['date_start'] );
        $datediff = floor( $datediff / ( 60*60*24 ));

        for($i = 0; $i < $datediff + 1; $i++){
            $thedate = date("Y-m-d", strtotime( $date['date_start'] . ' + ' . $i . 'day'));
            array_push( $booked_days,  date( 'Y-m-d', strtotime($thedate) ) );
        }
     }
     return $booked_days;
}

 /**
 * Get an array of booked days including tooltips
 *
 * @return array
 */
public function get_booked_days_array( $item_id, $comments, $status= 'confirmed' ) {

    global $wpdb;

    // get booking_code-id fromt codes database
     $sqlresult = $wpdb->get_results($wpdb->prepare(
        "
        SELECT date_start, date_end, hash
        FROM " . $this->table_bookings . "
        WHERE item_id = '%s' AND status = '%s'
        ",
        $item_id, $status), ARRAY_A); // get dates from

     $booked_days = array();

     foreach ($sqlresult as $date) {

        $datediff = strtotime( $date['date_end'] ) - strtotime( $date['date_start'] );
        $datediff = floor( $datediff / ( 60*60*24 ));

        for($i = 0; $i < $datediff + 1; $i++){

            $comment = '';
            if ( isset( $comments[ $date['hash'] ] ) ) {
                $comment = $comments[ $date['hash'] ];
            }
            $day_comment = array_search( $date['hash'], $comments );
            // echo('$day_hash ' . $date['hash'] . ' ');

            $thedate = date("Y-m-d", strtotime( $date['date_start'] . ' + ' . $i . 'day'));

            $booked_days[ strtotime($thedate) ] = $comment;
        }
     }
     return $booked_days;
}


 /**
 * Store all booking relevant data into booking-table, set status pending. Return booking_id
 *
 * Called from booking_review_page() when $_POST
 *
 * @return id
 */
    public function create_booking( $date_start, $date_end, $item_id ) {

        global $wpdb;

        // get relevant dat
        $code_id = $this->get_booking_code_id( $date_start, $item_id );
        $location_id = $this->get_booking_location_id( $date_start, $date_end, $item_id );
        $hash = $this->create_hash();

            $wpdb->insert(
                $this->table_bookings,
                array(
                    'date_start'    => $date_start ,
                    'date_end'      => $date_end,
                    'item_id'       => $item_id,
                    'user_id'       => $this->user_id,
                    'code_id'       => $code_id,
                    'location_id'   => $location_id,
                    'booking_time'  => date('Y-m-d H:i:s'),
                    'status' => 'pending',
                    'hash' => $this->hash
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
    public function get_booking( $booking_id) {

        global $wpdb;
        $table_bookings = $wpdb->prefix . 'cb_bookings';

        $sqlresult = $wpdb->get_row("
            SELECT *
            FROM $table_bookings
            WHERE id = $booking_id",
            ARRAY_A);

        $booking_data['id']             = $sqlresult['id'];
        $booking_data['date_start']     = $sqlresult['date_start'];
        $booking_data['date_end']       = $sqlresult['date_end'];
        $booking_data['item_id']        = $sqlresult['item_id'];
        $booking_data['code_id']        = $sqlresult['code_id'];
        $booking_data['user_id']        = $sqlresult['user_id'];
        $booking_data['location_id']    = $sqlresult['location_id'];
        $booking_data['booking_time']   = $sqlresult['booking_time'];
        $booking_data['status']         = $sqlresult['status'];
        $booking_data['hash']           = $sqlresult['hash'];

        return $booking_data;
    }

     /**
     * Triggered by Cron: Delete pending bookings that are older than one day.
     *
     */
    public function delete_pending_bookings() {

        global $wpdb;
        $wpdb->query(
            "
            DELETE
            FROM wp_cb_bookings
            WHERE STATUS = 'pending'
            AND booking_time < NOW() - INTERVAL 1 DAY
            ");
    }

 /**
 * set status in booking table.
 * parameter: booking_id (id), status = new statu (e.g. "confirmed")
 *
 * @return array
 */
    private function set_booking_status( $booking_id, $status ) {

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
 * set hash in booking table.
 * parameter: booking_id (id), hash
 *
 * @return array
 */
    private function set_booking_hash( $booking_id, $hash ) {

        global $wpdb;
        $table_bookings = $wpdb->prefix . 'cb_bookings';

        $wpdb->query(
            "
            UPDATE $table_bookings
            SET hash = '" . $hash . "'
            WHERE id = $booking_id
            "
        );

        return;

    }
    /**
     * Sends the confirm booking email.
     *
     * Called from booking_confirmed_page()
     *
     * @param $to the email address of the recipient
     * @param $bcc also send a bcc copy to the specified bcc address?
     * @param $template_name name of the configured email subject/body template to use
     */
    public function send_mail( $to, $bcc = true, $template_name = 'confirmation' ) {

        $body_template = ( $this->email_messages["mail_{$template_name}_body"] );  // get template
        $subject_template = ( $this->email_messages["mail_{$template_name}_subject"] );  // get template

        $sender_from_email = $this->settings->get_settings( 'mail', 'mail_from');
        $sender_from_name = $this->settings->get_settings( 'mail', 'mail_from_name');
        $confirmation_bcc = $this->settings->get_settings( 'mail', 'mail_bcc');

        // if custom email adress AND name is specified in settings use them, otherwise fall back to standard
        if ( ! empty ( $sender_from_name ) && ! empty ( $sender_from_email )) {
            $headers[] = 'From: ' . $sender_from_name . ' <' . $sender_from_email . '>';
        }

        // if BCC sending is desired and BCC is specified, send a copy to the address
        if ( $bcc && ! empty ( $confirmation_bcc ) ) {
            $headers[] = 'BCC: ' . $confirmation_bcc . "\r\n";
        }

        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        $body = cb_replace_template_tags( $body_template, $this->b_vars);
        $subject = cb_replace_template_tags( $subject_template, $this->b_vars);

        wp_mail( $to, $subject, $body, $headers );

    }
    /**
    * Validation: Days already booked, allowed to book over closed days, max days
    *
    * @param $item_id, $date_start, $date_end, $location_id
    * @return bool
    */
    private function validate_days ( $item_id, $date_start, $date_end, $location_id ) {

        $booked_days = $this->get_booked_days ( $item_id, 'confirmed' );
        $between = cb_get_dates_between( $date_start, $date_end );
        $count_days = count ( $between);
        $max_days = $this->settings->get_settings( 'bookings', 'bookingsettings_maxdays');
        $allow_closed = $this->settings->get_settings( 'bookings', 'bookingsettings_allowclosed');
        $allow_closed_count = $this->settings->get_settings( 'bookings', '_bookingsettings_closeddayscount');
        $location = $this->data->get_location( $location_id );
        $closed_days = $location['closed_days'];

        // prevent error if setting isn´t set
        if ( empty ( $allow_closed_count ) ) {
            $allow_closed_count = 1;
        }

        $closed_days_count = 0;

        // add the closed days to maxdays
        foreach ( $between as $day ) {
            $weekday = date( "N", strtotime( $day )); // convert date to weekday #
            if ( is_array ( $closed_days ) && in_array( $weekday, $closed_days ) ) { // a closed day found
                if ( $allow_closed == "on" ) { // booking over closed days is allowed
                    $closed_days_count++;
                } else {
                    die ('Error: You are not allowed to book over closed days.');
                }
            }
        }
        $max_days = $max_days + ( $closed_days_count -1 + $allow_closed_count ); // closed days count as 1 day

        // check if days are already booked
        $matches = array_intersect( $between, $booked_days ); //

        // if date is already booked, or too many days selected
        if ( ! empty ( $matches ) ) {
            $error_string = 'Error: You are trying to book days that are already booked.';
            echo ( $error_string );
            return false;
        } elseif ( $count_days > $max_days ) {
            $error_string = '"Error: You have selected more days than allowed! Selected: $count_days / Maximum: $max_days"';
            echo ($error_string);
            return false;
         } else {
            return TRUE;
        }
    }
    /**
     * Check if entry already in database.
     *
     * @return BOOL
     */
    private function validate_creation ( ) {
        $pending_days = $this->get_booked_days ( $this->item_id, 'pending' );
        if ( in_array( $this->date_start, $pending_days ) OR in_array( $this->date_end, $pending_days ) ) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Prepare for setting all needed variables for template.
     *
     * @param OBJECT The booking object
     *
     */
    private function prepare_for_set_booking_vars( $booking ) {
      $this->email_messages = $this->settings->get_settings( 'mail' ); // get email templates from settings page

      $this->booking = $booking;
      $this->date_start = ( $booking['date_start'] );
      $this->date_end = ( $booking['date_end'] );
      $this->location_id = ( $booking['location_id'] );
      $this->item_id = ( $booking['item_id'] );
      $this->user_id = ( $booking['user_id'] );
      $this->booking_id = ( $booking['id'] );
      $this->location = $this->data->get_location( $this->location_id );

      $this->recv_copies = ( $this->location['recv_copies'] );
      $this->location_email = (
        count( $this->location['contact']['email'] ) > 0 ?
        $this->location['contact']['email'] :
        NULL
      );
      $this->hash = $booking['hash'];
    }

    /**
     * Set all needed variables for template.
     *
     * @param BOOL include code
     *
     */
    private function set_booking_vars( $include_code = FALSE ) {

        $this->item = $this->data->get_item( $this->item_id );
        $this->location = $this->data->get_location( $this->location_id );
        $this->user = $this->data->get_user( $this->user_id );

        $b_vars['date_start'] = date_i18n( get_option( 'date_format' ), strtotime($this->date_start) );
        $b_vars['date_end'] = date_i18n( get_option( 'date_format' ), strtotime($this->date_end) );
        $b_vars['date_end_timestamp'] = strtotime($this->date_end);
        $b_vars['item_name'] = get_the_title ($this->item_id );
        $b_vars['item_thumb'] = cb_get_thumb( $this->item_id );
        $b_vars['item_content'] =  get_post_meta( $this->item_id, 'commons-booking_item_descr', TRUE  );
        $b_vars['location_name'] = get_the_title ($this->location_id );
        $b_vars['location_content'] = get_the_content( $this->location_id  );
        $b_vars['location_address'] = $this->data->format_adress($this->location['address']);
        $b_vars['location_thumb'] = cb_get_thumb( $this->location_id );
        $b_vars['location_contact'] = $this->location['contact']['string'];
        $b_vars['location_openinghours'] = $this->location['openinghours'];

        $b_vars['page_confirmation'] = $this->settings->get_settings('pages', 'booking_confirmed_page_select');

        $b_vars['hash'] = $this->hash;
        // Request the booking page explicitly (if using get_the_permalink() w/o
        // argument, the wrong URL is returned when this code is run from an
        // external class by an action)
        $permalink = get_permalink( get_page_by_title( __('Booking', 'commons-booking') ) );
        $b_vars['url'] = add_query_arg( 'booking', $this->hash, $permalink );

        $b_vars['site_email'] = $this->email_messages['mail_from'];

        $b_vars['user_name'] = $this->user['name'];
        $b_vars['user_email'] = $this->user['email'];

        $b_vars['first_name'] = $this->user['first_name'];
        $b_vars['last_name'] = $this->user['last_name'];

        $b_vars['user_address'] = $this->user['address'];
        $b_vars['user_phone'] = $this->user['phone'];
        if ( $include_code ) {
            $b_vars['code'] = $this->get_code( $this->booking['code_id'] );
        }
        $this->b_vars = $b_vars;

    }

    private function get_booking_id_by_hash ( $hash ) {

        global $wpdb;
        $table_bookings = $wpdb->prefix . 'cb_bookings';

        $result = $wpdb->get_results( $wpdb->prepare(
            "
            SELECT id
            FROM " . $table_bookings . "
            WHERE HASH = '%s'
            ",  $hash), ARRAY_A
        );
        return $result;
    }


    public function booking_review_page() {

      if (is_user_logged_in() ) {

        $current_user = wp_get_current_user();

        $booking_messages = $this->settings->get_settings( 'messages' ); // get messages templates from settings page
        $this->email_messages = $this->settings->get_settings( 'mail' ); // get email templates from settings page

        if ( !empty($_POST['create']) && $_POST['create'] == 1) { // we create a new booking

           if ( !empty($_POST['date_start']) && !empty($_POST['date_end']) && !empty($_POST['timeframe_id']) && !empty($_POST['item_id']) && !empty($_POST['location_id']) && !empty($_POST['_wpnonce']) ) { // all needed vars available

              if (! wp_verify_nonce($_POST['_wpnonce'], 'booking-review-nonce') ) die ('Your session has expired');

                // DATA FROM FORM
                $this->date_start = date( 'Y-m-d', ( sanitize_text_field( $_POST['date_start'] ) ) );
                $this->date_end = date( 'Y-m-d', ( sanitize_text_field( $_POST['date_end'] ) ) );
                $this->location_id = sanitize_text_field( $_POST['location_id'] );
                $this->item_id = sanitize_text_field( $_POST['item_id'] );
                $this->timeframe_id = sanitize_text_field( $_POST['timeframe_id'] );

                $this->user_id = get_current_user_id();

                // Set Variable for Template

                // check if days are not already booked, and count <  maxdays
                if ( $this->validate_days( $this->item_id, $this->date_start, $this->date_end, $this->location_id )) {

                    $msg = ( $booking_messages['messages_booking_pleaseconfirm'] );  // get message part

                    $this->hash = $this->create_hash();
                    $this->booking_id = $this->create_booking( $this->date_start, $this->date_end, $this->item_id);
                    $this->set_booking_vars();

                    return cb_display_message( $msg, $this->b_vars ) . cb1_get_template_part( 'booking-review', $this->b_vars , true ) . cb1_get_template_part( 'booking-review-submit', $this->b_vars , true );

                } // end if validated - days

            } else { // not all needed vars present

                $debug_string = '';
                if ( WP_DEBUG ) { 
                    $debug_string .= empty( $_POST['date_start'])   ?  ' date_start is missing' : '';
                    $debug_string .= empty( $_POST['date_end'])     ?  ' date_end is missing' : '';
                    $debug_string .= empty( $_POST['timeframe_id']) ?  ' timeframe_id is missing' : '';
                    $debug_string .= empty( $_POST['item_id'])      ?  ' item_id is missing' : '';
                    $debug_string .= empty( $_POST['location_id'])  ?  ' location_id is missing' : '';
                 }

                return __( 'Error: Page called with missing variable(s).', 'commons-booking' ) . $debug_string;
            }
        } else { // page is called without flag "create booking"

            return __( 'Error: Page called without needed flag.', 'commons-booking' );
        }

    } else {
        return __( 'Error: You have to be logged in to view this page!', 'commons-booking' );

        }

    }

    public function booking_confirmed_page() {

        if (is_user_logged_in() ) {

            $current_user = wp_get_current_user();

            $booking_messages = $this->settings->get_settings( 'messages' ); // get messages templates from settings page

            // 1. Confirm the booking / 2. view the booking / 3. cancel the booking
            if ( !empty($_GET['booking']) ) { // we confirm the booking

                // DATA FROM URL
                $this->hash = sanitize_text_field ( $_GET['booking'] );

                if (! ctype_alnum( $this->hash ) ) {
                    die ("Wrong Code");
                }
                $temp = $this->get_booking_id_by_hash( $this->hash );

                if ( ( isset( $temp[0]['id'] ) && ! empty ( $temp[0]['id'] ) ) ) {
                   $b_id = $temp[0]['id'];
                } else {
                    return cb_display_message( "Error: Booking not found.", array(), FALSE );
                    die();
                }
                $user_id = get_current_user_id();
                $booking = $this->get_booking( $b_id );

                if ( ( $booking['user_id'] ==  $user_id ) ) { // user that booked or admin

                    $this->prepare_for_set_booking_vars($booking);

                    $allow_booking_comments = $this->settings->get_settings( 'bookings', 'bookingsettings_allow_comments');
                    $allow_booking_comments_message = $this->settings->get_settings( 'messages', 'messages_booking_comment_notice');
                    $comments = '';
                    $message_comments = '';

                    // Set Variable for Template
                    $this->set_booking_vars( TRUE );

                    // Finalise the booking
                    if ( $booking['status'] == 'pending' && $_GET['confirm'] == 1 ) {  // check if status is pending and confirm = 1

                        // check again if days are not already booked, and count <  maxdays (prevents double bookings)
                        if ( $this->validate_days( $this->item_id, $this->date_start, $this->date_end, $this->location_id ) ) {

                            $msg = ( $booking_messages[ 'messages_booking_confirmed' ] ); // Confirmation message

                            if ( $allow_booking_comments ) {
                                $message_comments = cb_display_message( $allow_booking_comments_message, $this->b_vars );
                            }

                            $this->set_booking_status( $booking['id'], 'confirmed' ); // set booking status to confirmed
                            $this->send_mail( $this->user['email'] );
                            if ( !empty( $this->recv_copies ) && $this->location_email ) {
                            foreach ($this->location_email as $email) {
                                $this->send_mail( $email, false );
                            }
                            }

                            // PRINT: Booking review, Cancel Button
                            $message = cb_display_message( $msg, $this->b_vars );

                            return $message . $message_comments . cb1_get_template_part( 'booking-review-code', $this->b_vars , true ) . cb1_get_template_part( 'booking-review', $this->b_vars , true ) . cb1_get_template_part( 'booking-review-cancel', $this->b_vars , true );

                        } else { // while pending, somebody else took the slot 
                            $string = __('Sorry, somebody else just booked this slot.', 'commons-booking' );
                            $msg = cb_display_message( $string, null, FALSE );
                            return $msg; 
                        } // end if $this->validate_days()

                    } elseif ( $booking['status'] == 'confirmed' && empty($_GET['cancel']) ) {
                        // booking is confirmed and we are not cancelling

                        // display cancel button only if currentdate <= booking end date
                        if ( date ('ymd', time() ) <= date ('ymd', $this->b_vars['date_end_timestamp'] ) ) {
                            $cancel_button = cb1_get_template_part( 'booking-review-cancel', $this->b_vars , true );
                        } else {
                            $cancel_button = '';
                        }

                        if ( $allow_booking_comments ) {
                            $this->booking_comments->set_post_id( $this->item_id );
                            $this->booking_comments->set_booking_hash( $this->hash );
                            $comments = $this->booking_comments->render_comments_form();
                        }

                        // PRINT: Code, Booking review, Cancel Button
                        return cb1_get_template_part( 'user-bar', array(), true ) .cb1_get_template_part( 'booking-review-code', $this->b_vars , true ) .  $comments . cb1_get_template_part( 'booking-review', $this->b_vars , true ) . $cancel_button;


                    } elseif ( $booking['status'] == 'confirmed' && !empty($_GET['cancel']) && $_GET['cancel'] == 1 ) {
                        // booking is confirmed and we are cancelling
                        
                        if ( date ('ymd', time() ) <= date ('ymd', $this->b_vars['date_end_timestamp'] ) ) { // booking end date is today or in the future

                            $msg = ( $booking_messages['messages_booking_canceled'] );  // get message

                            $this->set_booking_status( $booking['id'], 'canceled' ); // set booking status to canceled
                            $this->send_mail( $this->user['email'], true, 'cancelation' );

                            if ( !empty( $this->recv_copies ) && $this->location_email ) {
                            foreach ($this->location_email as $email) {
                                $this->send_mail( $email, false, 'cancelation' );
                                }
                            }
                        } else { // booking end date is in the past
                            $msg = __( 'Error: You are not allowed to cancel a booking that is in the past.', 'commons-booking');  // get message
                        }

                        return cb_display_message( $msg, $this->b_vars );

                    } else {
                        // canceled booking, page refresh

                        $msg = __( 'Error: Booking not found', 'commons-booking' ); // @TODO: set canceled message
                        return display_cb_message( $msg, array(), FALSE );

                    }

                } else {
                    die ('You have no right to view this page');
                }


            } // end if confirm
        } else { // not logged in
            return cb_display_message( "Error: You must be logged in to access this page.", array(), FALSE );

        }
    }
}
?>
