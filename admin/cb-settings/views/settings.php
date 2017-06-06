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

  <div id="tabs" class="settings-tab">
    <ul>
      <li><a href="#tabs-welcome"><?php _e( 'Welcome', 'commons-booking'  ); ?></a></li>
      <li><a href="#tabs-display"><?php _e( 'Pages', 'commons-booking'  ); ?></a></li>
      <li><a href="#tabs-bookingsettings"><?php _e( 'Booking', 'commons-booking'  ); ?></a></li>
      <li><a href="#tabs-codes"><?php _e( 'Codes', 'commons-booking' ); ?></a></li>
      <li><a href="#tabs-mail"><?php _e( 'E-Mails', 'commons-booking' ); ?></a></li>
      <li><a href="#tabs-messages"><?php _e( 'Messages', 'commons-booking' ); ?></a></li>
      <li><a href="#tabs-advanced"><?php _e( 'Advanced', 'commons-booking' ); ?></a></li>
    </ul>

    <div id="tabs-welcome" class="wrap">

      
    <?php echo '<img src="' . plugins_url( 'commons-booking/assets/cb-logo.png' ) . '" > '; ?>

      <h1>Aloha!</h1>
      <p> Commons Booking - Version <?php echo (Commons_Booking::VERSION); ?> </p>
      <p><?php _e('To get started: Read the <a href="http://dein-lastenrad.de/index.php?title=Commons_Booking_Benutzerhandbuch">user manual (german)</a>', 'commons-booking'); ?>, <?php _e('ask questions in the <a href="http://forum.dein-lastenrad.de/index.php?p=/categories/buchungs-software">discussion forum (german)</a> and file bugs in the <a href="https://github.com/wielebenwir/commons-booking/issues">bug tracker (english)</a>', 'commons-booking'); ?></p>
        <p><?php _e('The future of this plugin depends on your contribution. Want to help? <a href="http://www.wielebenwir.de/verein/unterstutzen">Donate</a> and/or join the <a href="https://github.com/wielebenwir/commons-booking">development team</a>.', 'commons-booking'); ?>
      <p><?php _e('<a href="http://www.wielebenwir.de/projekte/commons-booking">Official Plugin Website</a>', 'commons-booking'); ?></p>
      <p><?php _e('All the best, Florian Egermann / <a href="http:://www.wielebenwir.de">wielebenwir e.V. </a>', 'commons-booking'); ?></p>

    </div>
    <div id="tabs-display" class="wrap">
      <?php

      $option_fields_pages = array(
        'id' => $this->plugin_slug . '_options_pages',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name'             => __( 'Theme', 'commons-booking' ),
            'desc'             => __( 'Choose a theme.', 'commons-booking' ),
            'id'               => $this->plugin_slug . '_theme_select',
            'type'             => 'select',
            'show_option_none' => false,
            'default'          => 'standard',
            'options'          => theme_select(),
          ), 
          array(
            'name'             => __( 'Items Page', 'commons-booking' ),
            'desc'             => __( 'Display list of items on this page', 'commons-booking' ),
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
            'name'             => __( 'Booking Review Page', 'commons-booking' ),
            'desc'             => __( 'Shows the pending booking, prompts for confimation.', 'commons-booking' ),
            'id'               => $this->plugin_slug . '_booking_review_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),           
          array(
            'name'             => __( 'Booking Confirmed Page', 'commons-booking' ),
            'desc'             => __( 'Shows the confirmed booking.', 'commons-booking' ),
            'id'               => $this->plugin_slug . '_booking_confirmed_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),           
          array(
            'name'             => __( 'My Bookings Page', 'commons-booking' ),
            'desc'             => __( 'Lists user´s bookings.', 'commons-booking' ),
            'id'               => $this->plugin_slug . '_user_bookings_page_select',
            'type'             => 'select',
            'show_option_none' => true,
            'default'          => 'none',
            'options'          => pages_dropdown(),
          ),                     
          array(
            'name'             => __( 'Link to terms & services', 'commons-booking' ),
            'desc'             => __( 'Full URL to PDF or page (e.g. http://www.kasimir-lastenrad.de/AGB.PDF)', 'commons-booking' ),
            'id'               => $this->plugin_slug . '_termsservices_url',
            'type'             => 'text',
            'default'          => ''
          ),                             
        ),
      );

      cmb2_metabox_form( $option_fields_pages, $this->plugin_slug . '-settings-pages' );
      ?>
    </div>
    <div id="tabs-bookingsettings" class="wrap">
      <?php

      $option_fields_bookingsettings = array(
        'id' => $this->plugin_slug . '_options_bookingsettings',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Maximum booking days', 'commons-booking' ),
            'desc' => __( 'Users can only book this many days at once', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_maxdays',
            'type' => 'text_small',
          ),           
          array(
            'name' => __( 'Calendar period', 'commons-booking' ),
            'desc' => __( 'Set how many days are displayed on the booking calendar (starting from today)', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_daystoshow',
            'type' => 'text_small',
          ),           
          array(
            'name' => __( 'Show week days', 'commons-booking' ),
            'desc' => __( 'Show the week days (Mon, Tue, ...) above the calendar.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_calendar_render_daynames',
            'type' => 'checkbox',
          ),          
          array(
            'name' => __( 'Allow booking over closed days', 'commons-booking' ),
            'desc' => __( 'Any number of closed days just counts as one booked day. <br>E.g. If you have a weekend specified as "closed" in the location editor, user will be able book from friday till monday.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_allowclosed',
            'type' => 'checkbox',
          ), 
          array(
            'name' => __( 'Closed days counting', 'commons-booking' ),
            'desc' => __( 'Count any number of closed days as one day. (If not enabled, they count as 0 days. Booking over closed days must be enabled)', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_closeddayscount',
            'type' => 'checkbox',
          ),         
          array(
            'name' => __( 'Allow booking comments', 'commons-booking' ),
            'desc' => __( 'Give users the ability to add a comment on the booking review page. <br>The comment be shown as a tooltip on the calendar. <br>NOTE: You must enable comments in WP Settings and for the item.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_bookingsettings_allow_comments',
            'type' => 'checkbox',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_bookingsettings, $this->plugin_slug . '-settings-bookings' );
      ?>
    </div>
    <div id="tabs-codes" class="wrap">
      <?php

      $option_fields_codes = array(
        'id' => $this->plugin_slug . '_options-codes',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Codes', 'commons-booking' ),
            'desc' => __( 'Enter comma-seperated Codes here. For examples see <a href="http://dein-lastenrad.de/index.php?title=Settings#Codes" target="_blank">the Commons Booking Plugin manual</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_codes_pool',
            'type' => 'textarea',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_codes, $this->plugin_slug . '-settings-codes' );
      ?>

    </div>   
    <div id="tabs-advanced" class="wrap">
      <?php

      $option_fields_advanced = array(
        'id' => $this->plugin_slug . '_options-advanced',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Customize Login and Registration pages', 'commons-booking' ),
            'desc' => __( 'Clean up the user registration & profile page, prevent users from accessing the dashbaord, hide superfluous fields (aim, website, etc), enable redirects.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_enable_customprofile',
            'type' => 'checkbox',
          ),       
        ),
      );

      cmb2_metabox_form( $option_fields_advanced, $this->plugin_slug . '-settings-advanced' );
      ?>

    </div>       
    <div id="tabs-messages" class="wrap">
      <?php

      $option_fields_messages = array(
        'id' => $this->plugin_slug . '_options-messages',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          array(
            'name' => __( 'Message Booking Review', 'commons-booking' ),
            'desc' => __( 'The message that appears after the user clicks "Book now" on the calendar. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_messages_booking_pleaseconfirm',
            'type' => 'textarea',
          ),
          array(
            'name' => __( 'Message Booking Submitted', 'commons-booking' ),
            'desc' => __( 'The message that appears after the user has confirmed the booking. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_messages_booking_confirmed',
            'type' => 'textarea',
          ),          
          array(
            'name' => __( 'Message Booking Canceled', 'commons-booking' ),
            'desc' => __( 'The message that appears after the user has canceled the booking. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_messages_booking_canceled',
            'type' => 'textarea',
          ),
          array(
            'name' => __( 'Message: Invite users to leave a comment', 'commons-booking' ),
            'desc' => __( 'Appears after the user has confirmed the booking. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_messages_booking_comment_notice',
            'type' => 'textarea',
          ),
        ),
      );

      cmb2_metabox_form( $option_fields_messages, $this->plugin_slug . '-settings-messages' );
      ?>

    </div>  
    <div id="tabs-mail" class="wrap">
      <?php

      $option_fields_mail = array(
        'id' => $this->plugin_slug . '_options-mail',
        'show_on' => array( 'key' => 'options-page', 'value' => array( $this->plugin_slug ), ),
        'show_names' => true,
        'fields' => array(
          // array(
          //   'name' => __( 'Email address', 'commons-booking' ),
          //   'desc' => __( 'The reply to address (make sure this exists)', 'commons-booking' ),
          //   'id' => $this->plugin_slug . '_mail_confirmation_sender',
          //   'type' => 'text',
          // ),
          array(
            'name' => __( 'Booking Confirmation from: Name', 'commons-booking' ),
            'desc' => __( 'eg. Commons Booking', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_from_name',
            'type' => 'text',
          ),  
          array(
            'name' => __( 'Booking Confirmation from: Address', 'commons-booking' ),
            'desc' => __( 'eg. commmons-booking@wielebenwir.de. Some hosts require that this address exists.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_from',
            'type' => 'text',
          ),            
          array(
            'name' => __( 'Booking Confirmation: Send a copy to:', 'commons-booking' ),
            'desc' => __( 'e.g. admin@wielebenwir.de.', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_bcc',
            'type' => 'text',
          ),                           
          array(
            'name' => __( 'Booking Confirmation email subject', 'commons-booking' ),
            'desc' => __( 'The subject of the confirmation Email. You can use <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_confirmation_subject',
            'type' => 'text',
          ),            
          array(
            'name' => __( 'Booking confirmation email body', 'commons-booking' ),
            'desc' => __( 'The body of the confirmation email. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Settings:Template_Tags" target="_blank">Template tags</a>. ', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_confirmation_body',
            'type' => 'textarea',
          ),  
          array(
            'name' => __( 'Registration email subject', 'commons-booking' ),
            'desc' => __( 'The subject of the registration Email. You can use <a href="http://dein-lastenrad.de/index.php?title=Registration_Mail_Template_Tags" target="_blank">Template tags</a>', 'commons-booking' ),
            'id' => $this->plugin_slug . '_mail_registration_subject',
            'type' => 'text',
          ),         
          array(
            'name' => __( 'Registration email body', 'commons-booking' ),
            'desc' => __( 'The body of the registration confirmation email. You can use HTML & <a href="http://dein-lastenrad.de/index.php?title=Registration_Mail_Template_Tags" target="_blank">Template tags</a>. ', 'commons-booking' ),
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
