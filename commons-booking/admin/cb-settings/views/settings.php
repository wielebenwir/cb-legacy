<?php
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Template for the settings screen (Admin -> Commons Booking settings)
 *
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

?>

<div class="wrap">

  <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

  <div id="tabs">
    <ul>
      <li><a href="#tabs-welcome"><?php _e( 'Welcome', $this->plugin_slug  ); ?></a></li>
      <li><a href="#tabs-display"><?php _e( 'Pages', $this->plugin_slug  ); ?></a></li>
      <li><a href="#tabs-bookingsettings"><?php _e( 'Booking', $this->plugin_slug  ); ?></a></li>
      <li><a href="#tabs-codes"><?php _e( 'Codes', $this->plugin_slug ); ?></a></li>
      <li><a href="#tabs-mail"><?php _e( 'E-Mails', $this->plugin_slug ); ?></a></li>
      <li><a href="#tabs-messages"><?php _e( 'Messages', $this->plugin_slug ); ?></a></li>
      <li><a href="#tabs-advanced"><?php _e( 'Advanced', $this->plugin_slug ); ?></a></li>
    </ul>

    <div id="tabs-welcome">

      
    <?php echo '<img src="' . plugins_url( 'commons-booking/assets/cb-logo.png' ) . '" > '; ?>

      <h1>Aloha!</h1>
      <p> Commons Booking Version <?php echo (Commons_Booking::VERSION); ?> </p>
      <p>For first steps, see the <a href="http://dein-lastenrad.de/index.php?title=First_Steps">Wiki</a>, if you find bugs, please  <a href="http://forum.dein-lastenrad.de/index.php?p=/categories/buchungs-software">report them here</a>, and <a href="http://www.wielebenwir.de/verein/unterstutzen">donate</a></p>
      <p>All the best, Florian & <a href="http:://www.wielebenwir.de">wielebenwir e.V. </a></p>

    </div>
    <div id="tabs-display">
      <?php

      $option_fields_pages = array(
        'id' => $this->plugin_slug . '_options_pages',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name'             => __( 'Items Page', $this->plugin_slug ),
            'desc'             => __( 'Display list of items on this page', $this->plugin_slug ),
            'id'               => $this->plugin_slug . '_item_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),            
          // array(
          //   'name'             => __( 'Locations Page', $this->plugin_slug ),
          //   'desc'             => __( 'Display list of Locations on this page', $this->plugin_slug ),
          //   'id'               => $this->plugin_slug . '_location_page_select',
          //   'type'             => 'select',
          //   'show_option_none' => true,
          //   'default'          => 'none',
          //   'options'          => pages_dropdown(),
          // ),            
          array(
            'name'             => __( 'Booking Review Page', $this->plugin_slug ),
            'desc'             => __( 'Shows the pending booking, prompts for confimation.', $this->plugin_slug ),
            'id'               => $this->plugin_slug . '_booking_review_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),           
          array(
            'name'             => __( 'Booking Confirmed Page', $this->plugin_slug ),
            'desc'             => __( 'Shows the confirmed booking.', $this->plugin_slug ),
            'id'               => $this->plugin_slug . '_booking_confirmed_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),           
          array(
            'name'             => __( 'My Bookings Page', $this->plugin_slug ),
            'desc'             => __( 'Lists user´s bookings.', $this->plugin_slug ),
            'id'               => $this->plugin_slug . '_user_bookings_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),                     
          array(
            'name'             => __( 'Link to terms & services', $this->plugin_slug ),
            'desc'             => __( 'Full URL to PDF or page (e.g. http://www.kasimir-lastenrad.de/AGB.PDF)', $this->plugin_slug ),
            'id'               => $this->plugin_slug . '_termsservices_url',
            'type'             => 'text',
            'default'          => ''
          ),                             
        ),
      );

      cmb2_metabox_form( $option_fields_pages, $this->plugin_slug . '-settings-pages' );
      ?>
    </div>
    <div id="tabs-bookingsettings">
      <?php

      $option_fields_bookingsettings = array(
        'id' => $this->plugin_slug . '_options_bookingsettings',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Maximum booking days', $this->plugin_slug ),
            'desc' => __( 'Users can only book this many days at once', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_bookingsettings_maxdays',
            'type' => 'text_small',
          ),           
          array(
            'name' => __( 'Days to show', $this->plugin_slug ),
            'desc' => __( 'Set how many days are displayed on the booking calendar (starting form today)', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_bookingsettings_daystoshow',
            'type' => 'text_small',
          ),          
          array(
            'name' => __( 'Allow booking over closed days', $this->plugin_slug ),
            'desc' => __( 'Any number of closed days just counts as one booked day. <br>E.g. If you have a weekend specified as "closed" in the location editor, user will still be able book from friday till monday.', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_bookingsettings_allowclosed',
            'type' => 'checkbox',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_bookingsettings, $this->plugin_slug . '-settings-bookings' );
      ?>
    </div>
    <div id="tabs-codes">
      <?php

      $option_fields_codes = array(
        'id' => $this->plugin_slug . '_options-codes',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Codes', $this->plugin_slug ),
            'desc' => __( 'Enter comma-seperated Codes here. For examples see <a href="http://dein-lastenrad.de/index.php?title=Settings#Codes" target="_blank">the Commons Booking Plugin manual</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_codes_pool',
            'type' => 'textarea',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_codes, $this->plugin_slug . '-settings-codes' );
      ?>

    </div>   
    <div id="tabs-advanced">
      <?php

      $option_fields_advanced = array(
        'id' => $this->plugin_slug . '_options-advanced',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Customize Login and Registration pages', $this->plugin_slug ),
            'desc' => __( 'Hide superfluous fields on login/registration pages.', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_enable_customcss',
            'type' => 'checkbox',
          ),          
          array(
            'name' => __( 'Enable redirects', $this->plugin_slug ),
            'desc' => __( 'Enable redirects after Registration & Login.', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_enable_redirect',
            'type' => 'checkbox',
          ),          
          array(
            'name' => __( 'Show timeframe title', $this->plugin_slug ),
            'desc' => __( 'Show the timeframe title on the items list and calendar.', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_show_timeframe_title',
            'type' => 'checkbox',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_advanced, $this->plugin_slug . '-settings-advanced' );
      ?>

    </div>       
    <div id="tabs-messages">
      <?php

      $option_fields_messages = array(
        'id' => $this->plugin_slug . '_options-messages',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Message Booking Review', $this->plugin_slug ),
            'desc' => __( 'The message that appears after the user clicks "Book now" on the calendar. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_messages_booking_pleaseconfirm',
            'type' => 'textarea',
          ),
          array(
            'name' => __( 'Message Booking Submitted', $this->plugin_slug ),
            'desc' => __( 'The message that appears after the user has confirmed the booking. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_messages_booking_confirmed',
            'type' => 'textarea',
          ),          
          array(
            'name' => __( 'Message Booking Canceled', $this->plugin_slug ),
            'desc' => __( 'The message that appears after the user has canceled the booking. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_messages_booking_canceled',
            'type' => 'textarea',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_messages, $this->plugin_slug . '-settings-messages' );
      ?>

    </div>  
    <div id="tabs-mail">
      <?php

      $option_fields_mail = array(
        'id' => $this->plugin_slug . '_options-mail',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Email address', $this->plugin_slug ),
            'desc' => __( 'The reply to address (make sure this exists)', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_mail_confirmation_sender',
            'type' => 'text',
          ),          
          array(
            'name' => __( 'Confirmation email subject', $this->plugin_slug ),
            'desc' => __( 'The subject of the confirmation Email. You can use <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_mail_confirmation_subject',
            'type' => 'text',
          ),            
          array(
            'name' => __( 'Confirmation email body', $this->plugin_slug ),
            'desc' => __( 'The body of the confirmation email. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>. ', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_mail_confirmation_body',
            'type' => 'textarea',
          ),  
          array(
            'name' => __( 'Registration email subject', $this->plugin_slug ),
            'desc' => __( 'The subject of the registration Email. You can use <a href="http://dein-lastenrad.de/index.php?title=Registration_Mail_Template_Tags" target="_blank">Template tags</a>', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_mail_registration_subject',
            'type' => 'text',
          ),         
          array(
            'name' => __( 'Registration email body', $this->plugin_slug ),
            'desc' => __( 'The body of the registration confirmation email. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Registration_Mail_Template_Tags" target="_blank">Template tags</a>. ', $this->plugin_slug ),
            'id' => $this->plugin_slug . '_mail_registration_body',
            'type' => 'textarea',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_mail, $this->plugin_slug . '-settings-mail' );
      ?>

    </div>
  </div>
</div>
