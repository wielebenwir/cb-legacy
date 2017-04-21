<?php
/*
 * Handles Mailing 
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

class CB_Mail extends Commons_Booking {

  	public function __construct( ) {

		$this->settings = Parent::get_cb_settings();

		var_dump($this->settings);

	    // $this->settings = Commons_Booking::get_cb_settings();
	    
	    $this->html = TRUE;

		$this->sender_mail_from = $this->return_mail_from();
		$this->sender_mail_from_name = $this->return_mail_from_name();
		$this->body = '';
		$this->subject = '';
		$this->recipient = '';
		$this->headers = array();

	}

	public function cb_send_mail() {

		$this->set_headers();

		$to = $this->recipient;
		$subject = $this->subject;
		$body = $this->body;
		$headers = $this->headers;

    	wp_mail( $to, $subject, $body, $headers ); 

	}

	public function set_headers ( ) {

		if ( $this->validate_mail_settings() ) {
			
			$from_name = $this->return_mail_from_name();
			$from_email = $this->return_mail_from();
			
			$this->headers[] = 'From: ' . $from_name . '<' . $from_email . '>';			
		}

		if ( $this->html ) {

			$this->headers[] = 'Content-Type: text/html; charset=UTF-8';
		}
	}

	public function set_subject ( $subject ) {
		$this->subject = $subject;
	}

	public function set_body ( $body ) {
		$this->body = $body;
	}	
	public function set_recipient ( $recipient ) {
		$this->recipient = $recipient;
	}

	private function return_mail_from () {

    	return $this->settings->get_settings('mail', 'mail_from');	
	}	

	private function return_mail_from_name () {

    	return $this->settings->get_settings('mail', 'mail_from_name');	
	}

	private function validate_mail_settings() {

        if ( ! empty ( $this->sender_mail_from ) && ! empty ( $this->sender_mail_from_name )) {
        	return TRUE;
        } else {
        	return FALSE;
        }
	}

}
