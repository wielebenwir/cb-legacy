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
     *s
     * @var     string
     */
    const VERSION = '0.9.4.8';

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
    protected static $plugin_roles = array( // Not using user Roles 
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

    public $settings; 

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     0.0.1
     */
    private function __construct() {
        // Load plugin text domain
        add_action( 'init', array( $this, 'load_plugin_textdomain' ), 0 );

        // Activate plugin when new blog is added
        add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ), 0 );
        
        // Register items & locations custom post types
        add_action( 'init', array( $this, 'register_cpts' ) );
      
        $this->items = new CB_Public_Items();
        $this->data = new CB_Data();
        $this->users = new CB_Users();
        $this->settings = new CB_Admin_Settings();
        $this->bookings = new CB_Booking();
        $this->booking_comments = new CB_Booking_Comments();

        // add CSS class
        add_filter( 'body_class', array( $this, 'add_cb_class' ), 10, 3 );
     
        add_action( 'comment_post', array( $this->booking_comments, 'save_comment_meta_data' ) );
        add_filter( 'comment_post_redirect', array( $this->booking_comments, 'after_comment_redirect' ) );   

        /* 
         * USER REGISTRATION, PROFILE FIELDS AND EDITING.
         */ 

        // Registration: Form fields 
        add_action( 'register_form', array( $this->users, 'registration_add_fields' ) );
        // Registration: Validation
        add_filter( 'registration_errors', array( $this->users, 'registration_set_errors' ), 10, 3 );
        // Registration: Write meta
        add_action( 'user_register', array( $this->users, 'registration_add_meta' ) );
        add_action( 'personal_options_update', array( $this->users , 'save_extra_profile_fields' ));


        // show admin bar only for admins and editors
        if (!current_user_can('edit_posts')) {
            add_filter('show_admin_bar', '__return_false');
        }

        // Redirect Customization. Applied only if $settings->enable_redirect = TRUE
        $enable_customprofile = $this->settings->get_settings( 'advanced', 'enable_customprofile');

        if ( !empty ($enable_customprofile) ) {
            add_filter( 'login_headertitle', array( $this->users, 'cb_login_header_title' ) );  // @TODO RETIRE ME
            add_filter( 'login_headerurl', array( $this->users, 'cb_login_custom_site_url' ) );  
            add_filter( 'login_redirect', array( $this->users, 'cb_login_redirect'), 10, 3 );
            add_action( 'profile_update', array( $this->users, 'cb_user_profile_redirect'), 12);
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_cleanup_styles' ) );
        }
    
        // Editing User Profile: Add extra form fields 
        add_action( 'show_user_profile', array( $this->users, 'show_extra_profile_fields' ) );
 

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_theme_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_calendar_js_vars' ) );

        add_shortcode( 'cb_items', array( $this, 'item_shortcode' ) );
        add_shortcode( 'cb_item_categories', array( $this, 'item_category_shortcode' ) );

        /* 
         * CRON.
         */ 

        // CRON: Run daily, delete pending bookings older than 1 day
        if ( ! wp_next_scheduled( 'cb_cron_delete_pending' ) ) {
            wp_schedule_event( time(), 'daily', 'cb_cron_delete_pending' );
        }

        add_action( 'cb_cron_delete_pending', array( $this->bookings, 'delete_pending_bookings' ) );

        /* 
         * Filter: Overwrite pages.
         */
        add_filter( 'the_content', array( $this, 'cb_content' ) ); 

    }

    public function register_cpts() {

        $item_labels = array(
            'name'               => __( 'Items', 'commons-booking' ),
            'singular_name'      => __( 'Item', 'commons-booking' ),
            'menu_name'          => __( 'Items', 'commons-booking' ),
            'name_admin_bar'     => __( 'Item', 'commons-booking' ),
            'add_new'            => __( 'Add New', 'commons-booking' ),
            'add_new_item'       => __( 'Add New Item', 'commons-booking' ),
            'new_item'           => __( 'New Item', 'commons-booking' ),
            'edit_item'          => __( 'Edit Item', 'commons-booking' ),
            'view_item'          => __( 'View Item', 'commons-booking' ),
            'all_items'          => __( 'All Items', 'commons-booking' ),
            'search_items'       => __( 'Search Items', 'commons-booking' ),
            'parent_item_colon'  => __( 'Parent Items:', 'commons-booking' ),
            'not_found'          => __( 'No Items found.', 'commons-booking' ),
            'not_found_in_trash' => __( 'No Items found in Trash.', 'commons-booking' )
        );

        $item_args = array(
            'labels'             => $item_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'cb-items' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'show_in_nav_menus'  => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
        );

        register_post_type( 'cb_items', $item_args );

        register_taxonomy(
            'cb_items_category',
            'cb_items',
            array(
                'label' => __( 'Category' ),
                'rewrite' => array( 'slug' => 'cb-item-cat' ),
                'hierarchical' => true,
            )
        );


        $location_labels = array(
            'name'               => __( 'Locations', 'commons-booking' ),
            'singular_name'      => __( 'Location', 'commons-booking' ),
            'menu_name'          => __( 'Locations', 'commons-booking' ),
            'name_admin_bar'     => __( 'Location', 'commons-booking' ),
            'add_new'            => __( 'Add New', 'commons-booking' ),
            'add_new_item'       => __( 'Add New Location', 'commons-booking' ),
            'new_item'           => __( 'New Location', 'commons-booking' ),
            'edit_item'          => __( 'Edit Location', 'commons-booking' ),
            'view_item'          => __( 'View Location', 'commons-booking' ),
            'all_items'          => __( 'All Location', 'commons-booking' ),
            'search_items'       => __( 'Search Locations', 'commons-booking' ),
            'parent_item_colon'  => __( 'Parent Locations:', 'commons-booking' ),
            'not_found'          => __( 'No Locations found.', 'commons-booking' ),
            'not_found_in_trash' => __( 'No Locations found in Trash.', 'commons-booking' )
        );

        $location_args = array(
            'labels'             => $location_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'cb-locations' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail' )
        );

        register_post_type( 'cb_locations', $location_args );
        // flush rewrite rules
        flush_rewrite_rules();
    }
     /**
     *   We add content to all the plugin pages.
     *
     * @since    0.3.
     *
     * @return    Mixed 
     */
    public function cb_content( $page_content ) {

        $cb_main_query = FALSE;
        if ( in_the_loop() && is_singular() && is_main_query() ) {
            $cb_main_query = TRUE;
        }

        $settings_display = $this->settings->get_settings('pages'); // get array of page ids from settings
        $post_id = get_the_ID();

            // items page
            if ( !empty( $settings_display[ 'item_page_select' ] ) && ( is_page( $settings_display[ 'item_page_select' ] ) ) ) {
                
                $args = array ();
                return  $page_content . '<div class="cb-wrapper">' . $this->items->output( $args ) . '</div>';
            
            // booking review page
            } elseif ( !empty( $settings_display[ 'booking_review_page_select' ] ) &&  $cb_main_query && ( is_page( $settings_display[ 'booking_review_page_select' ] ) ) ) {
                
                return $page_content . '<div class="cb-wrapper">' . $this->bookings->booking_review_page() . '</div>';            

            // booking confirmed page
            } elseif ( !empty( $settings_display[ 'booking_confirmed_page_select' ] ) && $cb_main_query && ( is_page( $settings_display[ 'booking_confirmed_page_select' ] ) ) ) {

                return $page_content . '<div class="cb-wrapper">' . $this->bookings->booking_confirmed_page() . '</div>';

            // user: bookings list
            } elseif ( !empty( $settings_display[ 'user_bookings_page_select' ] ) && ( is_page( $settings_display[ 'user_bookings_page_select' ] ) ) ) {

                return $page_content . '<div class="cb-wrapper">' . $this->users->render_user_bookings_page( ) . '</div>';            

            // user: single item with timeframes & calendar
            } elseif (  is_singular( 'cb_items' ) ) {                             

                $item_id = get_the_ID();
                return $page_content . '<div class="cb-wrapper">' . $this->data->render_item_single( $item_id ) . '</div>' . $this->data->render_booking_bar() ;

            // items as wordpress archive
            } elseif ( ( is_post_type_archive ( 'cb_items' ) ) OR ( is_tax( 'cb_items_category' ) ) ) { // list of items 

                return '<div class="cb-wrapper">' . $this->data->render_item_list( ) . '</div>';
            
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
    public static function get_plugin_dir() {

        $path = plugin_dir_path( __FILE__ );
        return $path;
    }    
    /**
     * Return path to plugin base.
     *
     * @since     0.9
     *
     * @return    string    
     */
    public static function get_plugin_url() {

        $url = plugin_dir_url( __FILE__ );
        return $url;
    }
    /**
     * Return path to templates.
     *
     * @since     0.6
     *
     * @return    string    
     */
    public function get_template_dir() {

        $path = plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . 'templates/';
        return $path;
    }    
    /**
     * Return admin settings.
     *
     * @since     0.6
     *
     * @return    object    
     */
    public function get_plugin_settings() {

        $settings = $this->settings;
        return $settings;
    }    

    /**
     * Return users.
     *
     * @since     0.6
     *
     * @return    object    
     */
    public function get_users() {

        $users = $this->users;
        return $users;
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
            'WP' => new WordPress_Requirement( '4.3.1' ),
                ) );

        // install the database tables 
        $timeframe_table = new Commons_Booking_Timeframes_Setup;
        $timeframe_table->install();        
       
        $codes_table = new Commons_Booking_Codes_Setup;
        $codes_table->install();
       
        $bookings_table = new Commons_Booking_Bookings_Setup;
        $bookings_table->install();

        // create the default pages 
        $item_page = create_page(__( 'Items', 'commons-booking' ), self::$plugin_slug.'_item_page_select');
        $user_bookings_page = create_page(__( 'My Bookings', 'commons-booking' ), self::$plugin_slug.'_user_bookings_page_select');
        $booking_confirmed_page = create_page(__( 'Booking', 'commons-booking' ), self::$plugin_slug.'_booking_confirmed_page_select');
        $booking_view_page = create_page(__( 'Confirm booking', 'commons-booking' ), self::$plugin_slug.'_booking_review_page_select');

        // set defaults, set pages, update wp_options
        $settings = new CB_Admin_Settings(); 

        $settings->set_defaults( $item_page, $user_bookings_page , $booking_confirmed_page, $booking_view_page );
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
    }

    /**
     * Register and enqueue public-facing theme style sheet.
     *
     * @since    0.9
     */
    public function enqueue_theme_styles() {
       
       // get selected theme from settings
        $theme_name = $this->settings->get_settings( 'pages', 'theme_select');

        // if not set
        if ( empty ( $theme_name ) ) {
            $theme_name = 'standard';
        }

        // construct the url
        $url = $this->get_plugin_url() . '/assets/css/themes/' . $theme_name . '/' . $theme_name . '.css';

        //enque the theme css
        wp_enqueue_style( $this->get_plugin_slug() . '-plugin-themes', $url , array(), self::VERSION );           
    }


    public function enqueue_cleanup_styles() {
        wp_enqueue_style( $this->get_plugin_slug() . '-profile-cleanup-tml', plugins_url( 'assets/css/profile-cleanup-tml.css', __FILE__ ), array(), self::VERSION );

    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts() {

        wp_enqueue_script( $this->get_plugin_slug() . '-comment-length-script', plugins_url( 'assets/js/comment-length.js', __FILE__ ), array( 'jquery' ), self::VERSION );

        if ( is_singular ( 'cb_items' )) {
            wp_enqueue_script( $this->get_plugin_slug() . '-tooltip-lib', plugins_url( 'assets/js/jquery.tooltipster.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );           
            wp_enqueue_script( $this->get_plugin_slug() . '-selectonic-lib', plugins_url( 'assets/js/selectonic.min.js', __FILE__ ), array( 'jquery' ), self::VERSION );
            wp_enqueue_script( $this->get_plugin_slug() . '-calendar-script', plugins_url( 'assets/js/calendar.js', __FILE__ ), array( 'jquery' ), self::VERSION );
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
        $bookingpage = get_permalink ( $s_display[ $this->get_plugin_slug() . '_booking_review_page_select'] );

        $allowclosed = 0; // weird bug with checkbox in cmb2: if not set, the key is not in the array. 
        if ( isset( $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_allowclosed']) ) {
            $allowclosed = 1;
        }        

        $closed_days_count = 0;
        if ( isset( $s_bookings[ $this->get_plugin_slug() . '_bookingsettings_closeddayscount']) ) {
            $closed_days_count = 1;
        }
        
        wp_localize_script( $this->get_plugin_slug() . '-calendar-script', 'cb_js_vars', array(
            'setting_maxdays' => $maxdays,
            'setting_booking_review_page' => $bookingpage,
            'setting_allowclosed' => $allowclosed,
            'setting_closed_days_count' => $closed_days_count,
            'text_start_booking' => __( 'Book here:', 'commons-booking' ),
            'text_choose' => __( 'Click the days you want to book', 'commons-booking' ),
            'text_pickup' => __( 'Pickup date:', 'commons-booking' ),
            'text_return' => __( 'Return date:', 'commons-booking' ),
            'text_pickupreturn' => __( 'Pickup and return date:', 'commons-booking' ),
            'text_error_days' => __( 'Sorry, too many days between pickup and return, the maximum is: ', 'commons-booking' ),
            'text_error_timeframes' => __( 'Sorry, you can only book at one station.', 'commons-booking' ),
            'text_error_notbookable' => __( 'Sorry, this day is not bookable.', 'commons-booking' ),
            'text_error_sequential' => __( 'Please select sequential days.', 'commons-booking' ),
            'text_error_bookedday' => __( 'Sorry, there must be no booked day between pickup and return.', 'commons-booking' ),
            'text_error_closedforbidden' => __( 'Sorry, you can´t book over closed days.', 'commons-booking' )
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

       $id = $this->settings->get_settings('pages', 'user_bookings_page_select');
       $url = get_permalink( $id );
       return $url;
    }     
    /**
     * Redirect: User page
     *
     * @since    0.2
     */
    public function cb_user_url() {
       $id = $this->settings->get_settings('pages', 'user_bookings_page_select');
       $url = get_permalink( $id );
       return $url;
    }    

    /**
     * Redirect: User page
     *
     * @since    0.2
     */
    public function get_cb_settings() {
       return $this->settings;
    }

    /**
     * Shortcode: Items
     *
     *        Reference:  http://codex.wordpress.org/Shortcode_API
     *
     * @since    0.8
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

        $items = new CB_Public_Items;
        return  $items->output( $a );
    }    

    /**
     * Shortcodes
     *
     *        Reference:  http://codex.wordpress.org/Shortcode_API
     *
     * @since    0.8
     */
    function item_category_shortcode( $atts ) {
        $args = shortcode_atts( array(
            'taxonomy' => 'cb_items_category',
            'echo' => FALSE,
            'title_li' => '',
            'orderby' => 'title', 
            'order' => 'DESC'
        ), $atts );

        $content = wp_list_categories( $args );

        return $content;
    }

}
