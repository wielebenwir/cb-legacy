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
 * Admin related functions
 *
 * @package Commons_Booking_Admin
 * @author    Florian Egermann <florian@wielebenwir.de>
 */
class Commons_Booking_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     0.0.1
	 */
	private function __construct() {

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 */
		$plugin = Commons_Booking::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->Commons_Booking = $plugin->get_Commons_Booking();
		$this->version = $plugin->get_plugin_version();
		$this->cpts = $plugin->get_cpts();

		$this->settings = $plugin->get_plugin_settings();
		$this->users = $plugin->get_users();


		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_settings_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_helper_scripts' ) );
		// Load admin style in dashboard for the At glance widget
		add_action( 'admin_head-index.php', array( $this, 'enqueue_admin_styles' ) );

		// load the js for table filtering
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_table_filter' ) );
		// load the js for timepicker in timeframe edit screen
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_timeframe_edit_datepicker' ) );


		// At Glance Dashboard widget for your cpts
		add_filter( 'dashboard_glance_items', array( $this, 'cpt_dashboard_support' ), 10, 1 );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_settings' ) );		
		// Add the Entrys for Items, Timeframes, Codes, ...
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );


		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * CMB 2 library
		 */
		require_once( plugin_dir_path( __FILE__ ) . '/includes/CMB2/init.php' );
		require_once( plugin_dir_path( __FILE__ ) . '/includes/CMB2-field-Leaflet-Geocoder/cmb-field-leaflet-map.php' );
                require_once( plugin_dir_path( __FILE__ ) . '/includes/CMB2-field-Icon/cmb-field-icon.php' );

		// Definition of Custom meta boxes for items & Locations
		require_once( plugin_dir_path( __FILE__ ) . 'cb-items/includes/cb-items-metaboxes.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'cb-locations/includes/cb-locations-metaboxes.php' );


		// BOOKINGS Extend the Wordpress Admin Tables Interface
		require_once( plugin_dir_path( __FILE__ ) . 'cb-bookings/class-cb-bookings-table.php' );
	

		// CODES Extend the Wordpress Admin Tables Interface
		require_once( plugin_dir_path( __FILE__ ) . 'cb-codes/class-cb-codes-table.php' );
		// CODES: Functions
		require_once( plugin_dir_path( __FILE__ ) . 'cb-codes/class-cb-codes.php' );
		// CODES: Generator
		require_once( plugin_dir_path( __FILE__ ) . 'cb-codes/includes/class-cb-codes-generate.php' );


		// TIMEFRAMES the admin table for timeframes & codes
		require_once( plugin_dir_path( __FILE__ ) . 'cb-timeframes/cb-timeframes.php' );

		// Admin messages
		require_once( plugin_dir_path( __FILE__ ) . '/includes/class-admin-table-messages.php' );
		
		//  item metabox
		$items_metabox = new Commons_Booking_Items_Metabox ();
		// locations metabox
		$locations_metabox = new Commons_Booking_Locations_Metaboxes ();
		// Call users
		$cb_users = $this->users;

		// add meta boxes
    add_action( 'add_meta_boxes', array( $items_metabox, 'cb_items_add_timeframe_meta_box'));
    add_filter( 'cmb2_meta_boxes', array( $items_metabox, 'cb_item_descr_metaboxes' ) );
    add_filter( 'cmb2_meta_boxes', array( $items_metabox, 'cb_item_icon_metaboxes' ) );
    add_filter( 'cmb2_meta_boxes', array( $locations_metabox, 'add_metabox' ) );

    // add user profile fields 
  	add_action( 'personal_options_update', array( $cb_users , 'save_extra_profile_fields' ));
	add_action( 'edit_user_profile_update', array( $cb_users , 'save_extra_profile_fields' ));		
	add_action( 'edit_user_profile', array( $cb_users , 'show_extra_profile_fields' ));		


	// Remove Wordpress styles
	add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
 

    // Login/Registration/Profile Customization. Applied only if $settings->customize = TRUE
    $enable_customprofile = $this->settings->get_settings( 'advanced', 'enable_customprofile');
    if ( !empty ($enable_customprofile) ) {
    	add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_user_profile_styles' ) );
    	add_action( 'admin_init', array( $cb_users, 'cb_redirect_prevent_dashboard' ) );
    }



		/*
		 * Debug mode
		 */
		require_once( plugin_dir_path( __FILE__ ) . 'includes/debug.php' );
		$debug = new Pn_Debug( $this );
		$debug->log( __( 'Plugin Loaded', $this->plugin_slug ) );

	  /*
	   * Load Wp_Admin_Notice for the notices in the backend
	   * 
	   * First parameter the HTML, the second is the css class
	   */
	  if ( !class_exists( 'WP_Admin_Notice' ) ) {
	    require_once( plugin_dir_path( __FILE__ ) . 'includes/WP-Admin-Notice/WP_Admin_Notice.php' );
	  }
			
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
	 * Register and enqueue admin-table-specific JavaScript.
	 *
	 * @since     0.0.1
	 *
	 */
	public function enqueue_admin_table_filter() {


		wp_enqueue_script( $this->plugin_slug . 'admin-table-filters', plugins_url( 'assets/js/tableFilter.js', __FILE__ ), array( 'jquery'), Commons_Booking::VERSION, true );

	}		
	/**
	 * Register and enqueue the date picker for the timeframe edit sceen
	 *
	 * @since     0.0.1
	 *
	 */
	public function enqueue_timeframe_edit_datepicker() {

			wp_enqueue_style( $this->plugin_slug . '-datePicker-styles', plugins_url( 'assets/css/datePicker.css', __FILE__ ), Commons_Booking::VERSION );
			wp_enqueue_script( $this->plugin_slug . 'timeframe_edit_datePicker', plugins_url( 'assets/js/datePicker.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), Commons_Booking::VERSION, true );
	}	

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		wp_enqueue_style( $this->plugin_slug . '-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array( ), Commons_Booking::VERSION );
	}
	/**
	 * Register and enqueue user profile cleanup styles.
	 *
	 * @since     0.6
	 *
	 */
	public function enqueue_user_profile_styles() {

		// enque css cleanup for profile screen
    $currentScreen = get_current_screen();
    if ( $currentScreen->id === "profile") {     
        wp_enqueue_style( $this->plugin_slug . '-profile-cleanup', plugins_url( 'assets/css/profile-cleanup.css', __FILE__ ), array(), Commons_Booking::VERSION );
    }  

		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
	}



	function deregister_styles() {
		wp_deregister_style( 'wp-pagenavi' );
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_settings_scripts() {
		if ( !isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen->id == 'settings_page_commons-booking' || $screen->id == 'cb_items' ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'jquery-ui-tabs' ), Commons_Booking::VERSION );
		}
	}	
	/**
	 * Register and enqueue admin-specific JavaScript helpers.
	 *
	 * @since     0.0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_helper_scripts() {

			wp_enqueue_script( $this->plugin_slug . '-helper-script', plugins_url( 'assets/js/cb-helpers.js', __FILE__ ), array( 'jquery' ), Commons_Booking::VERSION );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.0.1
	 */
	public function add_plugin_admin_settings() {
		/*
		 * Menu in Plugin Settings
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
				__( 'Commons Booking Settings', 'commons-booking' ), __( 'Commons Booking', 'commons-booking' ), 'manage_options', $this->plugin_slug, array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'cb-settings/views/settings.php' );
	}

	/**
	 * Add settings action link to the plugins page. 
	 *
	 * @since    0.0.1
	 */
	public function add_action_links( $links ) {
		return array_merge(
				array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings' ) . '</a>',
			'manual' => '<a href="http://dein-lastenrad.de/index.php?title=Introduction" target="_blank">' . __( 'Manual' ) . '</a>'
				), $links
		);
	}

	/**
	 * Add the counter of your CPTs in At Glance widget in the dashboard<br>
	 * NOTE: add in $post_types your cpts, remember to edit the css style (admin/assets/css/admin.css) for change the dashicon<br>
	 *
	 *        Reference:  http://wpsnipp.com/index.php/functions-php/wordpress-post-types-dashboard-at-glance-widget/
	 *
	 * @since    0.0.1
	 */
	public function cpt_dashboard_support( $items = array() ) {
		$post_types = $this->cpts;
		foreach ( $post_types as $type ) {
			if ( !post_type_exists( $type ) ) {
				continue;
			}
			$num_posts = wp_count_posts( $type );
			if ( $num_posts ) {
				$published = intval( $num_posts->publish );
				$post_type = get_post_type_object( $type );
				$text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $published, $this->plugin_slug );
				$text = sprintf( $text, number_format_i18n( $published ) );
				if ( current_user_can( $post_type->cap->edit_posts ) ) {
					$items[] = '<a class="' . $post_type->name . '-count" href="edit.php?post_type=' . $post_type->name . '">' . sprintf( '%2$s', $type, $text ) . "</a>\n";
				} else {
					$items[] = sprintf( '%2$s', $type, $text ) . "\n";
				}
			}
		}
		return $items;
	}


	 /**
	 * NOTE:     Add Menus
	 *
	 * @since    0.0.1
	 */	

    public function add_plugin_admin_menu( ) {

    $capability = 'publish_pages'; // Restrict access to whole menu to users with this capabilty

    /*
     * 3. Timeframes
     */
	    $this->plugin_screen_hook_suffix = add_menu_page(
	        __( 'Timeframes', 'commons-booking' ),   // page_title
	        __( 'Timeframes', 'commons-booking' ),   // menu_title
	        $capability,                              // capability
	        'cb_timeframes',                            // menu_slug
	        'cb_timeframes_table_page_handler',       // function
	        'dashicons-calendar-alt',                 // icon_url
	        33                                        // position
	        );
	    
	    // Editing or adding entries $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function
	    $this->plugin_screen_hook_suffix = add_submenu_page(
	        'cb_timeframes',                                      // parent_menu_slug
	        __( 'Add/Edit Timeframes', 'commons-booking' ),      // page_title
	        __( 'Add Timeframe', 'commons-booking' ),            // menu_title
	        $capability,                                          // capability
	        'cb_timeframes_edit',                                 // menu_slug
	        'cb_timeframes_table_form_page_handler'               // function
	        );	    

	    /*
	     * 4. Bookings
	     */
	    $this->plugin_screen_hook_suffix = add_menu_page(
	        __( 'Bookings', 'commons-booking' ),         // page_title
	        __( 'Bookings', 'commons-booking' ),         // menu_title
	        $capability,                              // capability
	        'cb_bookings',                              // menu_slug
	        'commons_booking_bookins_table_handler', 	// function
	        'dashicons-tag',                						// icon_url
	        34                                        // position
	        );    
	     /*
	     * 5. Codes
	     */
	    $this->plugin_screen_hook_suffix = add_menu_page(
	        __( 'Codes', $this->plugin_slug ),        // page_title
	        __( 'Codes', $this->plugin_slug ),        // menu_title
	        $capability,                              // capability
	        'cb_codes',                               // menu_slug
	        'commons_booking_codes_table_handler',    // function
	        'dashicons-admin-network',                // icon_url
	        35                                        // position
	        );
	  }

	/**
	 * Filter for change the folder of Contextual Help
	 * 
	 * @since     0.0.1
	 *
	 * @return    string    the path
	 */
	public function help_docs_dir( $paths ) {
		$paths[] = plugin_dir_path( __FILE__ ) . '../help-docs/';
		return $paths;
	}

	/**
	 * Filter for change the folder image of Contextual Help
	 * 
	 * @since     0.0.1
	 *
	 * @return    string    the path
	 */
	public function help_docs_url( $paths ) {
		$paths[] = plugin_dir_path( __FILE__ ) . '../help-docs/img';
		return $paths;
	}

}
?>