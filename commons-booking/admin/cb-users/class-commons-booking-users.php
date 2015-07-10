<?php
/*
 * Registers a metabox for display of timeframe entries on the item edit screen
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */
class Commons_Booking_Users {


  public function __construct() {

    $this->plugin_slug = 'commons-booking';

    $this->registration_fields = array ( 'username', 'password', 'email', 'first_name', 'last_name', 'phone', 'address', 'terms_accepted' );
    $this->registration_fields_required = $this->registration_fields;

    // include Wordpress error class
    $this->reg_errors = new WP_Error;

    $this->settings = new Commons_Booking_Admin_Settings();

    $this->r_vars = array();


    }

  /**
   * Backend: Show the extra profile fields
   *
   * @since    0.2
   *
   */
  public function show_extra_profile_fields( $user ) { ?>

        <h3><?php _e ( ' Extra Fields', $this->plugin_slug ); ?> </h3>

        <table class="form-table">
            <tr>
                <th><label for="phone"><?php _e ( 'Phone number', $this->plugin_slug ); ?></label></th>
                <td>
                    <input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
                </td>
            </tr>               
            <tr>
                <th><label for="address"><?php _e ( 'Address', $this->plugin_slug ); ?></label></th>
                <td>
                    <input type="textarea" name="address" id="address" value="<?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?>" class="regular-text" /><br />
                </td>
            </tr>            
            <tr>
                <th><label for="terms_accepted"><?php _e ( 'Terms and conditions', $this->plugin_slug ); ?></label></th>
                <td>
                    <input type="checkbox" name="terms_accepted" id=" terms_accepted " value="yes" <?php if (esc_attr( get_the_author_meta( "terms_accepted", $user->ID )) == "yes") echo "checked"; ?> /><?php echo __(' Accepted Terms & Conditions', $this->plugin_slug); ?><br />

                </td>
            </tr>

        </table>
    <?php }

  /**
   * Backend: Save extra profile fields
   *
   * @since    0.2
   *
   */
    public function save_extra_profile_fields( $user_id ) {

      if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

      update_user_meta( $user_id, 'phone', $_POST['phone'] );
      update_user_meta( $user_id, 'address', $_POST['address'] );
      update_user_meta( $user_id, 'terms_accepted', $_POST['terms_accepted'] );
      update_user_meta( $user_id, 'confirmed', $_POST['confirmed'] );
    }

  /**
   * Frontend: Include the registration form template
   *
   * @since    0.2
   *
   */
    public function registration_form( ) {

      if ( is_user_logged_in() ) {
          echo 'Welcome, registered user!';
      } else {

        $registration_enabled = get_option('users_can_register');

        if( $registration_enabled ) {
          include (commons_booking_get_template_part( 'user', 'registration', FALSE )); 
        } else {
          echo __('Sorry, registration is not allowed', $this->plugin_slug );
        } // end if enabled

      } // end if is_logged_in
    }

  /**
   * Frontend: Include the registration form template
   *
   * @since    0.2
   *
   * @param $values array of submitted values
   *
   */
    public function registration_validation( $values )  {  


      $req = $this->registration_fields_required;

      // check if required
      foreach ($values as $key => $value) {
        if ( in_array( $key, $req) && empty( $value ) ) {
          $this->reg_errors->add('field', __('Required form field is missing: ', $this->plugin_slug ) . $key );
        }
      }
      // check username length
      if ( 4 > strlen( $values['username'] ) ) {
        $this->reg_errors->add( 'username_length', __('Username too short. At least 4 characters is required', $this->plugin_slug ) );
      }

      // check username exists
      if ( username_exists( $values['username'] ) ) {
        $this->reg_errors->add('user_name', __('Sorry, that username already exists!', $this->plugin_slug) );
      }      
      // check if email exists
      if ( email_exists( $values['email'] ) ) {
        $this->reg_errors->add('email', __('Sorry, that email already exists!', $this->plugin_slug ) );
      }

      // check for needed username length
      if ( 5 > strlen( $values['password'] ) ) {
          $this->reg_errors->add( 'password', __('Password length must be greater than 5', $this->plugin_slug ) );
      }

      // check if checkbox is set
      if ( $values['terms_accepted'] != 'yes' ) {
          $this->reg_errors->add( 'terms_accepted', __('You must accept the terms', $this->plugin_slug ) );
      } 

      // error, so display message
      if ( is_wp_error( $this->reg_errors ) ) {
 
          foreach ( $this->reg_errors->get_error_messages() as $error ) {
            echo ('<p class="cb-error">');
            echo __( '<strong>Error:</strong> ' .$error );
            echo ('</p>');
               
          }
       
      }

    }
  /**
   * Frontend: Write to database
   *
   * @since    0.2
   *
   */
    public function complete_registration() {

        if ( 1 > count( $this->reg_errors->get_error_messages() ) ) {
            $userdata = array(
            'user_login'    =>   $this->r_vars['user_name'],
            'user_email'    =>   $this->r_vars['email'],
            'user_pass'     =>   $this->r_vars['password'],
            'first_name'    =>   $this->r_vars['first_name'],
            'last_name'     =>   $this->r_vars['last_name'],
            'phone'         =>   $this->r_vars['phone'],
            'address'       =>   $this->r_vars['address'],
            'terms_accepted'=>   'yes',
            'confirmed'     =>   FALSE
            );
            $user = wp_insert_user( $userdata );

            update_user_meta( $user, 'phone', $userdata['phone'] );
            update_user_meta( $user, 'address', $userdata['address'] );
            update_user_meta( $user, 'terms_accepted', $userdata['terms_accepted'] );
            update_user_meta( $user, 'confirmed', $userdata['confirmed'] );

            echo __( 'Thanks! Registration is complete. We´ve sent you an email with your Account information. ', $this->plugin_slug );
        }
    }


  /**
   * Frontend: Main registration function
   *
   * @since    0.2
   *
   */
    public function page_user() {
      
      if ( is_user_logged_in() ) {

          $current_user = wp_get_current_user();
          echo __ ('Welcome, ', $this->plugin_slug  ) . $current_user->user_firstname . '!';

          $user_bookings = $this->get_user_bookings( $current_user->ID );

          if ( $user_bookings ) {

            $review_page_id = $this->settings->get('pages', 'bookingconfirm_page_select');
            include (commons_booking_get_template_part( 'user', 'bookings', FALSE )); 

          } else {
            echo __(' You haven´t booked anything yet.'); 
          }

      } else { // Login Form and registration link

        include (commons_booking_get_template_part( 'user', 'login', FALSE )); 
       
      }
   }

/**
 * get all booking-dataa as array
 *
 * @return array
 */   
    public function get_user_bookings( $user_id) {
      
      global $wpdb;
      $table_bookings = $wpdb->prefix . 'cb_bookings';

      $sqlresult = $wpdb->get_results("SELECT * FROM $table_bookings WHERE user_id = $user_id", ARRAY_A);          

      return $sqlresult;
    }




  /**
   * Frontend: Main registration function
   *
   * @since    0.2
   *
   */
    public function custom_registration_function() {

        if ( isset( $_POST['submit'] ) ) {

          // check for nonce
          if (! isset( $_POST['user_nonce'] ) || ! wp_verify_nonce( $_POST['user_nonce'], 'create_user' ) ) { 

            die ('Error: Session expired.');

          } else { // register

            if ( isset( $_POST[ 'terms_accepted' ] ) ) {              
              $accepted = 'yes'; 
              } else {
                $accepted = 'no'; 
              }
 
            $values = array (
              'username' => $_POST['username'],
              'password' => $_POST['password'],
              'email' => $_POST['email'],
              'first_name' => $_POST['first_name'],
              'last_name' => $_POST['last_name'],
              'phone' => $_POST['phone'],
              'address' => $_POST['address'],
              'terms_accepted' => $accepted
             );

            $this->registration_validation( $values );
            

            $this->r_vars['user_name']   =   sanitize_user( $_POST['username'] );
            $this->r_vars['password']   =   esc_attr( $_POST['password'] );
            $this->r_vars['email']      =   sanitize_email( $_POST['email'] );
            $this->r_vars['first_name'] =   sanitize_text_field( $_POST['first_name'] );
            $this->r_vars['last_name']  =   sanitize_text_field( $_POST['last_name'] );
            $this->r_vars['phone']      =   sanitize_text_field( $_POST['phone'] );
            $this->r_vars['address']    =   sanitize_text_field( $_POST['address'] );
     
            // call @function complete_registration to create the user
            // only when no WP_error is found
            $this->complete_registration();
            $this->send_mail( $this->r_vars['email'] );
          }
        } elseif ( 1 > count( $this->reg_errors->get_error_messages() ) ) { 
    
        $this->registration_form();
      }
    }
    /**
     * Sends the confirm booking email.
     *
     * @since    0.2
     *
     * @param $to email adress 
     */   
    public function send_mail( $to ) {

        $this->email_messages = $this->settings->get( 'mail' ); // get email templates from settings page

        $body_template = ( $this->email_messages['mail_registration_body'] );  // get template
        $subject_template = ( $this->email_messages['mail_registration_subject'] );  // get template
      
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $body = replace_template_tags( $body_template, $this->r_vars);
        $subject = replace_template_tags( $subject_template, $this->r_vars);

        wp_mail( $to, $subject, $body, $headers );

    }

}
