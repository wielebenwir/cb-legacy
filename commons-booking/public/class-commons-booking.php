<?php

/**
 * Commons Booking
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-commons-booking-admin.php`
 * *
 * @package Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking {

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   0.0.1
     *
     * @var     string
     */
    const VERSION = '0.0.1';

    /**
     * @TODO - Rename "commons-booking" to the name of your plugin
     *
     * Unique identifier for your plugin.
     *
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    0.0.1
     *
     * @var      string
     */
    protected static $plugin_slug = 'commons-booking';

    /**
     * @TODO - Rename "Plugin Name" to the name of your plugin
     *
     * Unique identifier for your plugin.
     *
     *
     * @since    0.0.1
     *
     * @var      string
     */
    protected static $Commons_Booking = 'Commons Booking';

    /**
     * Instance of this class.
     *
     * @since    0.0.1
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Array of cpts of the plugin
     *
     * @since    0.0.1
     *
     * @var      object
     */
    protected $cpts = array( 'cb_items', 'cb_locations' );

    /**
     * Array of capabilities by roles
     * 
     * @since 0.0.1
     * 
     * @var array
     */
    protected static $plugin_roles = array( // @TODO: User Roles 
        // 'editor' => array(
        //     'edit_bookings' => true,
        //     'edit_others_bookings' => true,            
        //     'edit_items' => true,
        //     'edit_others_items' => true,            
        //     'edit_locations' => true,
        //     'edit_others_locations' => true,
        // ),
        // 'author' => array(
        //     'edit_bookings' => true,
        //     'edit_others_bookings' => false,
        // ),
        // 'subscriber' => array(
        //     'edit_bookings' => false,
        //     'edit_others_bookings' => false,
        // ),
    );

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     0.0.1
     */
    private function __construct() {
        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );
        

        // Create all needed custom post types defined in class-commons-booking-cpt.php @TODO: find better place for this
        $type_items = new Commons_Booking_Items_CPT( $this->get_plugin_slug() );
        $type_items->register_taxonomy();
        $type_locations = new Commons_Booking_Locations_CPT( $this->get_plugin_slug() );

        $items = new Commons_Booking_Public_Items();
        $this->users = new Commons_Booking_Users();
        $this->settings = new Commons_Booking_Admin_Settings;

        // add CSS class
        add_filter( 'body_class', array( $this, 'add_cb_class' ), 10, 3 );

        add_filter( 'registration_redirect', 'cb_registration_redirect' );
        add_filter( 'register_url', array( $this, 'cb_register_url' ) );
        add_filter( 'login_url', array( $this, 'cb_user_url' ) );

        // show admin bar only for admins and editors
        if (!current_user_can('edit_posts')) {
            add_filter('show_admin_bar', '__return_false');
        }

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_calendar_js_vars' ) );

        /* 
         * Filter: Overwrite pages.
         */
        add_action( 'the_content', array( $this, 'overwrite_page' ) );   



                

    }

public function tester() {
        // $settings = new Commons_Booking_Admin_Settings; 
        // $p = "commons-booking";

        // $defaults = array(
        //     $p. '-settings-pages' => array(
        //       $p.'_item_page_select' => 59,
        //       $p.'_bookingconfirm_page_select' => 'hnny',
        //       $p.'_registration_page_select' => $user_reg_page,
        //     ),
        //     $p.'-settings-bookings' => array(
        //       $p.'_bookingsettings_maxdays' => '',
        //       $p.'_bookingsettings_allowclosed' => ''
        //     ),        
        //     $p.'-settings-codes' => array(
        //       $p.'_codes_pool' => 'Dies ist der Codes Pool',
        //     ),
        //     $p.'-settings-messages' => array(
        //       $p.'_messages_booking_pleaseconfirm' => '',
        //       $p.'_messages_booking_confirmed' => '',
        //       $p.'_messages_booking_canceled' => '',
        //     ),         
        //     $p.'-settings-mail' => array(
        //       $p.'_mail_confirmation_sender' => '',
        //       $p.'_mail_confirmation_subject' => '',
        //       $p.'_mail_confirmation_body' => '',
        //       $p.'_mail_registration_subject' => '',
        //       $p.'_mail_registration_body' => '',
        //     ), 
        // );

        // // check if setting is set, otherwise set it. 
        
        // $settings->set_defaults( $defaults );
    }
    /**
     *   Add main items list to page selected in settings
     *   Add bookings review to page selected in settings.
     * Add main plugin overview output to page selected in settings.
     *
     * @since    0.0.1
     *
     * @return    Mixed 
     */
    public function overwrite_page( $pageID ) {

        $settings_display = $this->settings->get('pages');

            if ( !empty( $settings_display[ 'item_page_select' ] ) && ( is_page( $settings_display[ 'item_page_select' ] ) ) ) {
                
                $items = new Commons_Booking_Public_Items;
                return $items->items_render();
            
            } elseif ( !empty( $settings_display[ 'bookingconfirm_page_select' ] ) && ( is_page( $settings_display[ 'bookingconfirm_page_select' ] ) ) ) {

                $bookingpage = new Commons_Booking_Booking;
                return $bookingpage->render_bookingreview();

            } elseif ( !empty( $settings_display[ 'user_page_select' ] ) && ( is_page( $settings_display[ 'user_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                // return $cb_user->custom_registration_function();
                return $cb_user->page_user();            

            } elseif ( !empty( $settings_display[ 'registration_page_select' ] ) && ( is_page( $settings_display[ 'registration_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                // return $cb_user->custom_registration_function();
                return $cb_user->custom_registration_function();

            } elseif (  is_singular( 'cb_items' ) ) {                             
                $item_id = get_the_ID();
                $timeframes = new Commons_Booking_Data();

                $timeframes_display = $timeframes->show_single_item_timeframes($item_id);
                $bookingbar_display = $timeframes->show_booking_bar(); 

            } else { 
                return get_the_content( $pageID );
            }
        }    



    /**
     * Return the plugin slug.
     *
     * @since    0.0.1
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return self::$plugin_slug;
    }    

    /**
     * Return the plugin name.
     *
     * @since    0.0.1
     *
     * @return    Plugin name variable.
     */
    public function get_Commons_Booking() {
        return self::$Commons_Booking;
    }

    /**
     * Return the version
     *
     * @since    0.0.1
     *
     * @return    Version const.
     */
    public function get_plugin_version() {
        return self::VERSION;
    }

    /**
     * Return the cpts
     *
     * @since    0.0.1
     *
     * @return    Cpts array
     */
    public function get_cpts() {
        return $this->cpts;
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.0.1
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    0.0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_activate();

                    restore_current_blog();
                }
            } else {
                self::single_activate();
            }
        } else {
            self::single_activate();
        }
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    0.0.1
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate( $network_wide ) {

        if ( function_exists( 'is_multisite' ) && is_multisite() ) {

            if ( $network_wide ) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ( $blog_ids as $blog_id ) {

                    switch_to_blog( $blog_id );
                    self::single_deactivate();

                    restore_current_blog();
                }
            } else {
                self::single_deactivate();
            }
        } else {
            self::single_deactivate();
        }
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    0.0.1
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site( $blog_id ) {

        if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
            return;
        }

        switch_to_blog( $blog_id );
        self::single_activate();
        restore_current_blog();
    }    

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    0.0.1
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col( $sql );
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    0.0.1
     */
    public static function single_activate() {
        //Requirements Detection System - read the doc in the library file
        require_once( plugin_dir_path( __FILE__ ) . 'includes/requirements.php' );
        new Plugin_Requirements( self::$Commons_Booking, self::$plugin_slug, array(
            'WP' => new WordPress_Requirement( '3.9.0' ),
                ) );

        // install the database tables 
        $timeframe_table = new Commons_Booking_Timeframes_Setup;
        $timeframe_table->install();        
       
        $codes_table = new Commons_Booking_Codes_Setup;
        $codes_table->install();
       
        $bookings_table = new Commons_Booking_Bookings_Setup;
        $bookings_table->install();

        $settings = new Commons_Booking_Admin_Settings; 

        $p = self::$plugin_slug;

        // create the necessary pages 
        $item_page = create_page(__( 'Items', $p ), $p.'_item_page_select');
        $user_page = create_page(__( 'User Page', $p ), $p.'_user_page_select');
        $user_reg_page = create_page(__( 'User Registration', $p ), $p.'_registration_page_select');
        $user_login_page = create_page(__( 'Login', $p ), $p.'_login_page_select');
        $booking_confirm_page = create_page(__( 'Booking Confirmation', $p ), $p.'_bookingconfirm_page_select');


        // insert the default settings array

        $defaults = array(
            $p. '-settings-pages' => array(
              $p.'_item_page_select' => $item_page,
              $p.'_user_page_select' => $user_page,
              $p.'_login_page_select' => $user_login_page,
              $p.'_registration_page_select' => $user_reg_page,
              $p.'_bookingconfirm_page_select' => $booking_confirm_page,
            ),
            $p.'-settings-bookings' => array(
              $p.'_bookingsettings_maxdays' => 3,
              $p.'_bookingsettings_allowclosed' => ''
            ),        
            $p.'-settings-codes' => array(
              $p.'_codes_pool' => '',
            ),
            $p.'-settings-messages' => array(
              $p.'_messages_booking_pleaseconfirm' => '',
              $p.'_messages_booking_confirmed' => '',
              $p.'_messages_booking_canceled' => '',
            ),         
            $p.'-settings-mail' => array(
              $p.'_mail_confirmation_sender' => '',
              $p.'_mail_confirmation_subject' => '',
              $p.'_mail_confirmation_body' => '',
              $p.'_mail_registration_subject' => '',
              $p.'_mail_registration_body' => '',
            ), 
        );

        // check if setting is set, otherwise set it. 
        
        $settings->set_defaults( $defaults );


        //Clear the permalinks
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.0.1
     */
    private static function single_deactivate() {
        
        //Clear the permalinks
        flush_rewrite_rules();
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.0.1
     */
    public function load_plugin_textdomain() {
        $domain = $this->get_plugin_slug();
        $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    0.0.1
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->get_plugin_slug() . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
        wp_enqueue_style( $this->get_plugin_slug() . '-plugin-calendar', plugins_url( 'assets/css/commons-booking.css', __FILE__ ), array(), self::VERSION );

        if ( is_singular ( 'cb_items' )) {
            wp_enqueue_style( $this->get_plugin_slug() . '-tooltip-css', plugins_url( 'assets/css/tooltipster.css', __FILE__ ), array(), self::VERSION );
        }       
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->get_plugin_slug() . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        if ( is_singular ( 'cb_items' )) {
            wp_enqueue_script( $this->get_plugin_slug() . '-tooltip-lib', plugins_url( 'assets/js/jquery.tooltipster.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
        }
    }
    /**
     * For calendar page: Print the PHP vars in the HTML of the frontend for access by JavaScript @TODO use settings api
     *
     * @since    0.0.1
     */
    public function enqueue_calendar_js_vars() {

        $s_bookings = get_option( $this->get_plugin_slug() . '-settings-bookings' ); 
        $maxdays = $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_maxdays'];
        
        $s_display = get_option( $this->get_plugin_slug() . '-settings-pages' ); 
        $bookingpage = get_permalink ( $s_display[ $this->get_plugin_slug() . '_bookingconfirm_page_select'] );

        $allowclosed = 0; // weird bug with checkbox in cmb2: if not set, the key is not in the array. 
        if ( isset( $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_allowclosed']) ) {
            $allowclosed = 1;
        }
        

        wp_localize_script( $this->get_plugin_slug() . '-plugin-script', 'cb_js_vars', array(
            'setting_maxdays' => $maxdays,
            'setting_booking_review_page' => $bookingpage,
            'setting_allowclosed' => $allowclosed,
            'text_start_booking' => __( 'Book here:', $this->get_plugin_slug() ),
            'text_choose' => __( 'Click pickup and return date(s):', $this->get_plugin_slug() ),
            'text_pickup' => __( 'Pickup date:', $this->get_plugin_slug() ),
            'text_return' => __( 'Return date:', $this->get_plugin_slug() ),
            'text_pickupreturn' => __( 'Pickup and return date:', $this->get_plugin_slug() ),
            'text_error_days' => __( 'Sorry, To many days between pickup and return, the maximum is: ', $this->get_plugin_slug() ),
            'text_error_timeframes' => __( 'Sorry, you can only book at one station.', $this->get_plugin_slug() ),
            'text_error_notbookable' => __( 'Sorry, this day is not bookable.', $this->get_plugin_slug() ),
            'text_error_bookedday' => __( 'Sorry, there must be no booked day between pickup and return.', $this->get_plugin_slug() ),
            'text_error_closedforbidden' => __( 'Sorry, you can´t book over closed days.', $this->get_plugin_slug() )
                )
        );
    }

    /**
     * Add class in the body on the frontend
     *
     * @since    0.0.1
     */
    public function add_cb_class( $classes ) {
        $classes[] = $this->get_plugin_slug();
        return $classes;
    }

    /**
     * Example for override the template system on the frontend @TODO: Cleanup
     *
     * @since    0.0.1
     */
    public function load_content_demo( $original_template ) {
        if ( is_singular( 'demo' ) && in_the_loop() ) {
            return pn_get_template_part( 'content', 'demo', false );
        } else {
            return $original_template;
        }
    }
   
    /**
     * NOTE:  Actions are points in the execution of a page or process
     *        lifecycle that WordPress fires.
     *
     *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.0.1
     */
    public function action_method_name() {
        // @TODO: Define your action hook callback here
    }

    /**
     * Redirect: Registration page
     *
     * @since    0.2
     */
    public function cb_registration_redirect() {

       $id = $this->settings->get('pages', 'user_page_select');
       $url = get_permalink( $id );
       return $url;
    }     
    /**
     * Redirect: User page
     *
     * @since    0.2
     */
    public function cb_user_url() {

       $id = $this->settings->get('pages', 'user_page_select');
       $url = get_permalink( $id );
       return $url;
    }    
    /**
     * Redirect: After registration
     *
     * @since    0.2
     */
    public function cb_register_url() {

       $id = $this->settings->get('pages', 'registration_page_select');
       $url = get_permalink( $id );
       return $url;
    }

    /**
     * NOTE:  Shortcode simple set of functions for creating macro codes for use
     * 		  in post content.
     *
     *        Reference:  http://codex.wordpress.org/Shortcode_API
     *
     * @since    0.0.1
     */
    public function shortcode_method_name() {
        // @TODO: Define your shortcode here
		// Check for the CMB2 Shortcode Button
		// In bundle with the boilerplate https://github.com/jtsternberg/Shortcode_Button
    }

}
