<?php
/*
 * Handles the user registration & login process. 
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */
class Commons_Booking_Users extends Commons_Booking {


  public function __construct( ) {


    $this->plugin_slug = parent::$plugin_slug;
    $this->settings = new CB_Admin_Settings;
    $this->termsservices_url = $this->settings->get_settings('pages', 'termsservices_url');

    $this->registration_fields = array ( 
      'username', 
      'password', 
      'email', 
      'first_name', 
      'last_name', 
      'phone', 
      'address', 
      'terms_accepted' 
      );    

    $this->extra_profile_fields = array (       
       'first_name' => array ( 
          'field_name' => 'first_name', 
          'title' => __( 'First Name', $this->plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your first name', $this->plugin_slug ) 
          ),       
       'last_name' => array ( 
          'field_name' => 'last_name',
          'title' => __( 'Last Name', $this->plugin_slug ),  
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your last name', $this->plugin_slug ) 
          ),       
       'phone' => array ( 
          'field_name' => 'phone', 
          'title' => __( 'Phone Number', $this->plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your phone number', $this->plugin_slug ) 
          ),       
       'address' => array ( 
          'field_name' => 'address', 
          'title' => __( 'Addresss', $this->plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your Address', $this->plugin_slug ) 
          ),       
      'terms_accepted' => array ( 
          'title' => __( 'Terms and Conditions', $this->plugin_slug ), 
          'field_name' => 'terms_accepted', 
          'type' => 'checkbox', 
          'description' => __( 'I accept the terms & conditions' ), // @TODO add settings here
          'errormessage' => __('Please accept the terms & conditions', $this->plugin_slug ) 
          )
      );
    $this->mail_vars = array();
 
    $this->registration_fields_required = $this->registration_fields;

    $this->r_vars = array();


    }

  /**
   * Registration Form: Set terms & services String (Wrapped in URL)
   *
   * @since    0.6
   * 
   * @return string
   */
  public function get_termsservices_string() {
    if ( !empty ( $this->termsservices_url ) ) {
      $string = '<a href="' . $this->termsservices_url . '" target=_blank">' . __( 'Link to terms and services', $this->plugin_slug ) . '</a>';
    } else {
      $string = __( 'Accepted Terms & Conditions', $this->plugin_slug);      
    }
    return $string;
  }


  /**
   * Get the additional User fields
   *
   * @since    0.6.
   * 
   * @return array
   */
  public function get_extra_profile_fields() {
    return $this->extra_profile_fields;
  }
  
  /**
   * Sets a flat array of user field/value pairs
   *
   * @since    0.6
   * 
   */
  public function set_basic_user_vars( $user_id ) {
    
      $user_basic = get_user_by( 'id', $user_id );
      $user_meta = get_user_meta( $user_id );

      // transform from object to an array that the replace_template_tags functions expects
      $user_basic_array =  object_to_array ($user_basic);
      
      $user_meta_array = array();
      foreach ($user_meta as $key => $value) {
          $user_meta_array[$key] = $value[0];
      }

      // merge the arrays
      $this->user_vars = array_merge($user_basic_array['data'], $user_meta_array);
  }

  /**
   * Add addiotinal key/value pairs to the user_vars array  
   *
   * @since    0.5.3
   * 
   */
  public function add_user_vars( $key, $value ) {
      
      $this->user_vars[$key] = $value;
  }

  /**
   * Get the user_vars array  
   *
   * @since    0.5.3
   * 
   */
  public function get_user_vars( ) {

      return $this->user_vars;
  }

  /**
   * Set the activation url   
   *
   * @since    0.5.3
   * 
   */
  public function set_activation_url($key, $login) {

      $activation_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($login), 'login');
  }


  /**
   * Backend: Show the extra profile fields
   *
   * @since    0.2
   *
   */
  public function show_extra_profile_fields( $user ) { ?>

        <h3><?php _e ( 'Extra Fields', $this->plugin_slug ); ?> </h3>

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
                    <input type="checkbox" name="terms_accepted" id=" terms_accepted " disabled value="yes" <?php if (esc_attr( get_the_author_meta( "terms_accepted", $user->ID )) == "yes") echo "checked"; ?> /><?php __( 'Accepted Terms & Conditions', $this->plugin_slug); ?><br />
                </td>
            </tr>
        </table>
    <?php }

  /**
  * Backend: Update dte extra profile fields
  *
  * @since    0.2
  *
  */
  public function save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
      return false;

    update_user_meta( $user_id, 'phone', $_POST['phone'] );
    update_user_meta( $user_id, 'address', $_POST['address'] );
    // update_user_meta( $user_id, 'terms_accepted', $_POST['terms_accepted'] );
    update_user_meta( $user_id, 'confirmed', $_POST['confirmed'] );
  }


  /**
  * Frontend: User Page
  *
  * @since    0.2
  *
  */
  public function page_user() {
    
    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        echo __('Welcome, ', $this->plugin_slug  ) . $current_user->user_firstname . '!';
        echo '<span class="align-right"><a href="' . wp_logout_url( home_url() ) . '">' . __('Logout') . '</a></span>';

        $user_bookings = $this->get_user_bookings( $current_user->ID );

        if ( !empty ($user_bookings) ) {

          $review_page_id = $this->settings->get('pages', 'bookingconfirm_page_select');
          include (commons_booking_get_template_part( 'user', 'bookings', FALSE )); 

        } else {
          echo __( 'You haven´t booked anything yet.', $this->plugin_slug); 
        }

    } else { // Login Form and registration link

      include (commons_booking_get_template_part( 'user', 'login', FALSE )); 
     
    }
 }

/**
 * Get all booking-data as array
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
   * Sends the confirm booking email.
   *
   * @since    0.2
   *
   * @param $to email adress 
   */   
  public function send_mail( $to ) {

      $this->email_messages = $this->settings->get_settings( 'mail' ); // get email templates from settings page

      $body_template = ( $this->email_messages['mail_registration_body'] );  // get template
      $subject_template = ( $this->email_messages['mail_registration_subject'] );  // get template
    
      $headers = array('Content-Type: text/html; charset=UTF-8');

      $body = replace_template_tags( $body_template, $this->r_vars);
      $subject = replace_template_tags( $subject_template, $this->r_vars);

      wp_mail( $to, $subject, $body, $headers );

  }    

  /**
   * Sends the registration email.
   *
   * @since    0.2
   *
   * @param $to email adress 
   */   
  public function send_registration_mail() {

    $this->email_messages = $this->settings->get_settings( 'mail' ); // get email templates from settings page
    $body_template = ( $this->email_messages['mail_registration_body'] );  // get template
    $subject_template = ( $this->email_messages['mail_registration_subject'] );  // get template

    $vars = $this->user_vars;
    $headers = array('Content-Type: text/html; charset=UTF-8'); 

    $to = $vars['user_email'];
    $body = replace_template_tags( $body_template, $vars );
    $subject = replace_template_tags( $subject_template, $vars );

    wp_mail( $to, $subject, $body, $headers ); 

  }

}



// Overwrite the user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {

        if ( $deprecated !== null ) {
          _deprecated_argument( __FUNCTION__, '4.3.1' );
        }        

        $user = new WP_User( $user_id );
        $cb_user = new Commons_Booking_Users();

        global $wpdb, $wp_hasher;

        // The blogname option is escaped with esc_html on the way into the database in sanitize_option
        // we want to reverse this for the plain text arena of emails.
        $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);


        // Admin Message
 
        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
 
        $message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "<br>";
        $message .= sprintf(__('Username: %s'), $user_login) . "<br>";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "<br>";
 
        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
 
        // if notification disabled, return.
        if ( 'admin' === $notify || empty( $notify ) ) {
            return;
          }
 
        // Generate something random for a password reset key.
        $key = wp_generate_password( 20, false );

        /** This action is documented in wp-login.php */
        do_action( 'retrieve_password_key', $user->user_login, $key );

        // Now insert the key, hashed, into the DB.
        if ( empty( $wp_hasher ) ) {
          require_once ABSPATH . WPINC . '/class-phpass.php';
          $wp_hasher = new PasswordHash( 8, true );
        }
        $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );



        // User Message 

        $cb_user->set_basic_user_vars( $user_id );
        $activation_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');

        $cb_user->add_user_vars( 'ACTIVATION_URL', $activation_url );
        $cb_user->add_user_vars( 'USER_NAME', $user_login );
        $registered_user = $cb_user->get_user_vars();

        $cb_user->send_registration_mail( $registrated_user );

 
    }
}


