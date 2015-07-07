<?php
/*
 * Registers a metabox for display of timeframe entries on the item edit screen
 * @package   Commons_Booking_Admin
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */
class Commons_Booking_Users {

  /**
   * Hook into the appropriate actions when the class is constructed.
   */
  public function __construct() {

    $this->plugin_slug = 'commons-booking';

    $this->registration_fields = array ( 'username', 'password', 'email', 'first_name', 'last_name', 'phone', 'address', 'terms_accepted' );

    // include Wordpress error class
    $this->reg_errors = new WP_Error;


    }

    public function sayhello() {
      return "hello";
    }


/**
 * Add items list output to page selected in settings.
 *
 * @since    0.0.1
 *
 * @return    Mixed 
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
   * Add items list output to page selected in settings.
   *
   * @since    0.0.1
   *
   * @return    Mixed 
   */

    public function save_extra_profile_fields( $user_id ) {

      if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

      update_user_meta( $user_id, 'phone', $_POST['phone'] );
      update_user_meta( $user_id, 'address', $_POST['address'] );
      update_user_meta( $user_id, 'terms_accepted', $_POST['terms_accepted'] );
    }


    public function registration_form() {

        include (commons_booking_get_template_part( 'user', 'registration', FALSE )); 

    }


    public function registration_validation( $username, $password, $email, $first_name, $last_name, $phone, $address, $terms_accepted )  {  


      if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        $this->reg_errors->add('field', 'Required form field is missing');
      }
      if ( 4 > strlen( $username ) ) {
        $this->reg_errors->add( 'username_length', 'Username too short. At least 4 characters is required' );
      }

      if ( username_exists( $username ) ) {
        $this->reg_errors->add('user_name', 'Sorry, that username already exists!');
      }

      if ( 5 > strlen( $password ) ) {
          $this->reg_errors->add( 'password', 'Password length must be greater than 5' );
      }

      if ( is_wp_error( $this->reg_errors ) ) {
 
          foreach ( $this->reg_errors->get_error_messages() as $error ) {
           
              echo '<div>';
              echo '<strong>ERROR</strong>:';
              echo $error . '<br/>';
              echo '</div>';
               
          }
       
      }

    }

    public function complete_registration() {

        if ( 1 > count( $this->reg_errors->get_error_messages() ) ) {
            $userdata = array(
            'user_login'    =>   $this->username,
            'user_email'    =>   $this->email,
            'user_pass'     =>   $this->password,
            'user_url'      =>   $this->website,
            'first_name'    =>   $this->first_name,
            'last_name'     =>   $this->last_name,
            'phone'         =>   $this->phone,
            'address'       =>   $this->address,
            'terms_accepted'=>   $this->terms_accepted,
            );
            $user = wp_insert_user( $userdata );
            echo 'Registration complete. Goto <a href="' . get_site_url() . '/wp-login.php">login page</a>.';   
        }
    }

    public function custom_registration_function() {

        if ( isset($_POST['submit'] ) ) {
            $this->registration_validation(
            $_POST['username'],
            $_POST['password'],
            $_POST['email'],
            $_POST['fname'],
            $_POST['lname'],
            $_POST['phone'],
            $_POST['address'],
            $_POST['terms_accepted']
            );
             
            $this->username   =   sanitize_user( $_POST['username'] );
            $this->password   =   esc_attr( $_POST['password'] );
            $this->email      =   sanitize_email( $_POST['email'] );
            $this->first_name =   sanitize_text_field( $_POST['fname'] );
            $this->last_name  =   sanitize_text_field( $_POST['lname'] );
            $this->phone      =   sanitize_text_field( $_POST['phone'] );
            $this->address    =   sanitize_text_field( $_POST['address'] );
     
            // call @function complete_registration to create the user
            // only when no WP_error is found
            $this->complete_registration();
        }
     
        $this->registration_form();
    }



}
