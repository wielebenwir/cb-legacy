<?php

/**
 * Commons Booking
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@macht-medien.de>
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
 * @author    Florian Egermann <florian@macht-medien.de>
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
        $type_items = new Commons_Booking_Items_CPT();
        $type_items->register_taxonomy();
        // new Commons_Booking_Items_CPT();
        $type_locations = new Commons_Booking_Locations_CPT();



        add_filter( 'body_class', array( $this, 'add_pn_class' ), 10, 3 );

        //Override the template hierarchy for load /templates/content-demo.php
        add_filter( 'template_include', array( $this, 'load_content_demo' ) );

        // Load public-facing style sheet and JavaScript.
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js_vars' ) );



        /* 
         * Add Table to predefined page
         */


        add_action( 'the_content', array( $this, 'wpse83525_filter_the_title' ) );

        // add_filter( 'the_content', function( $title ) { return '<b>' . $title . '</b>'; } );


        /* 
         * Define custom functionality.
         * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        add_action( '@TODO', array( $this, 'action_method_name' ) );
        add_filter( '@TODO', array( $this, 'filter_method_name' ) );
        add_shortcode( '@TODO', array( $this, 'shortcode_method_name' ) );
    }




       public function wpse83525_filter_the_title() {
        // $pageID = get_option( 'commons-booking-settings-display' );
        var_dump($pageID );
            // if ( is_page_template( 'page-courses.php' ) ) {
            //     return 'Custom Title';
            // }
            // return $title;
            return 'Custom Title';

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
    private static function single_activate() {
        //Requirements Detection System - read the doc in the library file
        require_once( plugin_dir_path( __FILE__ ) . 'includes/requirements.php' );
        new Plugin_Requirements( self::$Commons_Booking, self::$plugin_slug, array(
            'WP' => new WordPress_Requirement( '3.9.0' ),
                ) );

        // @TODO: Define activation functionality here

        $timeframe_table = new Commons_Booking_Timeframes_Setup;
        $timeframe_table->install();        
        $codes_table = new Commons_Booking_Codes_Setup;
        $codes_table->install();
        $bookings_table = new Commons_Booking_Bookings_Setup;
        $bookings_table->install();



        // global $wp_roles;
        // if ( !isset( $wp_roles ) ) {
        //     $wp_roles = new WP_Roles;
        // }

        // foreach ( $wp_roles->role_names as $role => $label ) {
        //     //if the role is a standard role, map the default caps, otherwise, map as a subscriber
        //     $caps = ( array_key_exists( $role, self::$plugin_roles ) ) ? self::$plugin_roles[ $role ] : self::$plugin_roles[ 'subscriber' ];

        //     //loop and assign
        //     foreach ( $caps as $cap => $grant ) {
        //         //check to see if the user already has this capability, if so, don't re-add as that would override grant
        //         if ( !isset( $wp_roles->roles[ $role ][ 'capabilities' ][ $cap ] ) ) {
        //             $wp_roles->add_cap( $role, $cap, $grant );
        //         }
        //     }
        // }
        //Clear the permalinks
        flush_rewrite_rules();
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    0.0.1
     */
    private static function single_deactivate() {
        // @TODO: Define deactivation functionality here
        
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
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    0.0.1
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->get_plugin_slug() . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
    }

    /**
     * Print the PHP var in the HTML of the frontend for access by JavaScript
     *
     * @since    0.0.1
     */
    public function enqueue_js_vars() {
        wp_localize_script( $this->get_plugin_slug() . '-plugin-script', 'pn_js_vars', array(
            'alert' => __( 'Hey! You have clicked the button!', $this->get_plugin_slug() )
                )
        );
    }

    /**
     * Add class in the body on the frontend
     *
     * @since    0.0.1
     */
    public function add_pn_class( $classes ) {
        $classes[] = $this->get_plugin_slug();
        return $classes;
    }

    /**
     * Example for override the template system on the frontend
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
     * NOTE:  Filters are points of execution in which WordPress modifies data
     *        before saving it or sending it to the browser.
     *
     *        Filters: http://codex.wordpress.org/Plugin_API#Filters
     *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    0.0.1
     */
    public function filter_method_name() {
        // @TODO: Define your filter hook callback here
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
