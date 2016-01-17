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
    const VERSION = '0.5.3';

    /**
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
    static $plugin_slug = 'commons-booking';    
    /**
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
        

        $items = new Commons_Booking_Public_Items();
        $this->users = new Commons_Booking_Users();
        $this->settings = new CB_Admin_Settings;

        $this->user_fields = $this->users->get_extra_profile_fields();

        // Create all needed custom post types
        $type_locations = new CB_Locations_CPT( self::$plugin_slug );
        $type_items = new CB_items_CPT( self::$plugin_slug );


        // add CSS class
        add_filter( 'body_class', array( $this, 'add_cb_class' ), 10, 3 );

        // Redirects after registration
        // add_filter( 'registration_redirect', 'cb_registration_redirect' );
        // add_filter( 'register_url', array( $this, 'cb_register_url' ) );
        // add_filter( 'login_url', array( $this, 'cb_user_url' ) );


        /* 
         * USER REGISTRATION, PROFILE FIELDS AND EDITING.
         */ 

        // Registration: Form fields 
        add_action( 'register_form', array( $this, 'cb_register_add_fields' ) );
        // Registration: Validation
        add_filter( 'registration_errors', array( $this, 'cb_registration_errors' ), 10, 3 );
        // Registration: Write meta
        add_action( 'user_register', array( $this, 'cb_user_register' ) );
        // Registration: Send Email
        add_action('user_register', array( $this, 'send_register_mail' ) );

        // show admin bar only for admins and editors
        if (!current_user_can('edit_posts')) {
            add_filter('show_admin_bar', '__return_false');
        }

        // Editing User Profile: Form fields 
        add_action( 'show_user_profile', array( $this->users, 'show_extra_profile_fields' ) );
        add_action( 'profile_update', array( $this, 'cb_user_profile_redirect' ), 12 );


        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_calendar_js_vars' ) );

        add_shortcode( 'cb_items', array( $this, 'item_shortcode' ) );



        /* 
         * Filter: Overwrite pages.
         */
        add_action( 'the_content', array( $this, 'cb_content' ) ); 

    }

    /*
    * Redirects User after Profile update
    *
    * @since    0.6
    *
    */
    function cb_user_profile_redirect() {
        
        wp_redirect( trailingslashit( home_url() ) );
        exit;
        
    }


    /*
    *   Adds the fields to the wordpress registration page
    *
    * @since    0.6
    *
    */

    public function cb_register_add_fields() {

      foreach ($this->user_fields as $field) {

            $row = ( ! empty( $_POST[ $field['field_name'] ] ) ) ? trim( $_POST[ $field['field_name'] ] ) : '';
            
            ?>
            <p>
                <label for="<?php _e( $field['field_name'] ) ?>"><?php _e( $field['title'] ) ?><br />
                <?php if ( $field['type'] == 'checkbox' ) { ?>
                    <input type="checkbox" name="<?php _e( $field['field_name'] ) ?>" id="<?php _e( $field['field_name'] ) ?>" value="yes" <?php if ( $_POST[ $field['field_name'] ]  == "yes") echo "checked"; ?> /><?php _e( $field['description'] ) ?><br />
                    <?php } else { ?>
                    <input type="text" name="<?php _e( $field['field_name'] ) ?>" id="<?php _e( $field['field_name'] ) ?>" class="input" value="<?php echo esc_attr( wp_unslash( $_POST[ $field['field_name'] ] ) ); ?>" size="25" /><?php _e( $field['description'] ) ?>
                    <? } ?>
                </label>
            </p>
            <?php

         }

        }

    /*
    *   Adds error handling
    *
    * @since    0.6
    *
    * @return    object 
    */
    public function cb_registration_errors( $errors, $username, $email) {

        foreach ($this->user_fields as $field) {

            if ( $field['type'] == 'checkbox' ) {
                if ( !isset( $_POST[ $field[ 'field_name' ]]) ) {
                    $errors->add( $field[ 'field_name' ] . '_error', $field[ 'errormessage' ]);
                }
            } else {
                if ( empty( $_POST[ $field[ 'field_name' ] ] ) || ! empty( $_POST[ $field[ 'field_name' ] ] ) && trim( $_POST[ $field[ 'field_name' ] ] ) == '' ) {
                    $errors->add( $field[ 'field_name' ] . '_error', $field[ 'errormessage' ]);
                }
            }
        }
        return $errors;
    }

    /*
    *   Write user meta 
    *
    * @since    0.6
    *
    * @return    object 
    */
    public function cb_user_register( $user_id ) {

        foreach ($this->user_fields as $field) {
            if ( !empty( $_POST[ $field[ 'field_name' ]] ) ) {
                update_user_meta( $user_id, $field[ 'field_name' ], trim( $_POST[ $field[ 'field_name' ]] ) );
                }
        }
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
        // public function send_register_mail( $user_id ) {

        //     $user_basic = get_user_by( 'id', $user_id );
        //     $user_meta = get_user_meta( $user_id );


        //     // transform from object to an array that the replace_template_tags functions expects
        //     $user_basic_array =  object_to_array ($user_basic);
            
        //     $user_meta_array = array();
        //     foreach ($user_meta as $key => $value) {
        //         $user_meta_array[$key] = $value[0];
        //     }

        //     // merge the arrays
        //     $uservars = array_merge($user_basic_array['data'], $user_meta_array);

        //     $this->email_messages = $this->settings->get( 'mail' ); // get email templates from settings page
        //     $body_template = ( $this->email_messages['mail_registration_body'] );  // get template
        //     $subject_template = ( $this->email_messages['mail_registration_subject'] );  // get template
      
        //     $headers = array('Content-Type: text/html; charset=UTF-8'); 

        //     $to = $uservars['user_email'];

        //     $body = replace_template_tags( $body_template, $uservars);
        //     $subject = replace_template_tags( $subject_template, $uservars);

        //     wp_mail( 'hallo@fleg.de', $subject, $body, $headers );

        // }


     /**
     *   Add main items list to page selected in settings
     *   Add bookings review to page selected in settings.
     * Add main plugin overview output to page selected in settings.
     *
     * @since    0.0.1
     *
     * @return    Mixed 
     */
    public function cb_content( $page_content ) {

        $settings_display = $this->settings->get('pages');

            if ( !empty( $settings_display[ 'item_page_select' ] ) && ( is_page( $settings_display[ 'item_page_select' ] ) ) ) {
                
                $items = new Commons_Booking_Public_Items;
                $args = array ();
                return  $page_content.$items->output( $args );
            
            } elseif ( !empty( $settings_display[ 'bookingconfirm_page_select' ] ) && ( is_page( $settings_display[ 'bookingconfirm_page_select' ] ) ) ) {

                $bookingpage = new Commons_Booking_Booking;
                return $page_content.$bookingpage->render_bookingreview();

            } elseif ( !empty( $settings_display[ 'user_page_select' ] ) && ( is_page( $settings_display[ 'user_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                return $page_content.$cb_user->page_user();            

            } elseif ( !empty( $settings_display[ 'registration_page_select' ] ) && ( is_page( $settings_display[ 'registration_page_select' ] ) ) ) {

                $cb_user = new Commons_Booking_Users;
                // return $cb_user->custom_registration_function();
                return $page_content.$cb_user->custom_registration_function();

            } elseif (  is_singular( 'cb_items' ) ) {                             

                $item_id = get_the_ID();
                $timeframes = new Commons_Booking_Data();
                return $page_content . $timeframes->render_item_single_timeframes($item_id) . $timeframes->show_booking_bar();

            } else { 
                return $page_content;
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
     * Return path to plugin base.
     *
     * @since     0.4.5
     *
     * @return    string    
     */
    public function get_plugin_dir() {

        $path = plugin_dir_path( __FILE__ ) . '../';
        return $path;
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

        $p = self::$plugin_slug;

        // create the default pages 
        $item_page = create_page(__( 'Items', $p ), $p.'_item_page_select');
        $user_page = create_page(__( 'User Page', $p ), $p.'_user_page_select');
        $user_reg_page = create_page(__( 'User Registration', $p ), $p.'_registration_page_select');
        $booking_confirm_page = create_page(__( 'Booking', $p ), $p.'_bookingconfirm_page_select');

        // set defaults, set pages, update wp_options
        $settings = new CB_Admin_Settings(); 

        $settings->set_defaults( $item_page, $user_page , $user_reg_page,  $booking_confirm_page );
        $settings->apply_defaults();

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
    // public function cb_register_url() {

    //    $id = $this->settings->get('pages', 'registration_page_select');
    //    $url = get_permalink( $id );
    //    return $url;
    // }

    /**
     * NOTE:  Shortcode simple set of functions for creating macro codes for use
     *        in post content.
     *
     *        Reference:  http://codex.wordpress.org/Shortcode_API
     *
     * @since    1.0.0
     */
    function item_shortcode( $atts ) {
        $a = shortcode_atts( array(
            'p' => '',
            'cat' => '',
            'posts_per_page' => 10, 
            'post_type' => 'cb_items', 
            'orderby' => 'title', 
            'order' => 'DESC'
        ), $atts );

        $items = new Commons_Booking_Public_Items;
        return  $items->output( $a );
    }

}