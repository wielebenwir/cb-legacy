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
    }

  /**
   * Frontend: Include the registration form template
   *
   * @since    0.2
   *
   */
    public function registration_form() {

        include (commons_booking_get_template_part( 'user', 'registration', FALSE )); 

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
          $this->reg_errors->add('field', 'Required form field is missing: ' . $key );
        }
      }
      // check username length
      if ( 4 > strlen( $values['username'] ) ) {
        $this->reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
      }

      // check username exists
      if ( username_exists( $values['username'] ) ) {
        $this->reg_errors->add('user_name', 'Sorry, that username already exists!');
      }      
      // check if email exists
      if ( email_exists( $values['email'] ) ) {
        $this->reg_errors->add('email', 'Sorry, that email already exists!');
      }

      // check for needed username length
      if ( 5 > strlen( $values['password'] ) ) {
          $this->reg_errors->add( 'password', 'Password length must be greater than 5' );
      }

      // check if checkbox is set
      if ( $values['terms_accepted'] != 'yes' ) {
          $this->reg_errors->add( 'terms_accepted', 'You must accept the terms' );
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
            'user_login'    =>   $this->username,
            'user_email'    =>   $this->email,
            'user_pass'     =>   $this->password,
            'first_name'    =>   $this->first_name,
            'last_name'     =>   $this->last_name,
            'phone'         =>   $this->phone,
            'address'       =>   $this->address,
            'terms_accepted'=>   'yes',
            );
            $user = wp_insert_user( $userdata );
            echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
        }
    }
  /**
   * Frontend: Main registration function
   *
   * @since    0.2
   *
   */
    public function custom_registration_function() {

        if ( isset($_POST['submit'] ) ) {

          // check for nonce
          if (! isset( $_POST['user_nonce'] ) || ! wp_verify_nonce( $_POST['user_nonce'], 'create_user' ) ) { 

            die ('You shouldn´t be here');

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
             
            $this->username   =   sanitize_user( $_POST['username'] );
            $this->password   =   esc_attr( $_POST['password'] );
            $this->email      =   sanitize_email( $_POST['email'] );
            $this->first_name =   sanitize_text_field( $_POST['first_name'] );
            $this->last_name  =   sanitize_text_field( $_POST['last_name'] );
            $this->phone      =   sanitize_text_field( $_POST['phone'] );
            $this->address    =   sanitize_text_field( $_POST['address'] );
     
            // call @function complete_registration to create the user
            // only when no WP_error is found
            $this->complete_registration();
          }
        } else { 
        
        $this->registration_form();
      }
    }
}
