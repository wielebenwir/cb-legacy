<?php

/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Commons Booking
 * @author    Florian Egermann <florian@macht-medien.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 *
 * @wordpress-plugin
 * Plugin Name:       Commons Booking
 * Plugin URI:        @TODO
 * Description:       @TODO
 * Version:           1.0.0
 * Author:            Florian Egermann
 * Author URI:        @TODO
 * Text Domain:       commons-booking
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * WordPress-Plugin-Boilerplate-Powered: v1.1.0
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/* ----------------------------------------------------------------------------*
 * Public-Facing Functionality
 * ---------------------------------------------------------------------------- */

/*
 * Load library for simple and fast creation of Taxonomy and Custom Post Type
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/Taxonomy_Core/Taxonomy_Core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/CPT_Core/CPT_Core.php' );
require_once( plugin_dir_path( __FILE__ ) . 'public/includes/class-commons-booking-cpt.php' );

/*
 * Load template system
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/template.php' );

/*
 * Load Widget boilerplate
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/Widgets-Helper/wph-widget-class.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/widgets/sample.php' );

/*
 * Load Fake Page class
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/fake-page.php' );

new Fake_Page(
		array(
	'slug' => 'fake_slug',
	'post_title' => 'Fake Page Title',
	'post content' => 'This is the fake page content'
		)
);

/*
 * Load Language wrapper function for WPML/Ceceppa Multilingua/Polylang
 */
require_once( plugin_dir_path( __FILE__ ) . 'includes/language.php' );


require_once( plugin_dir_path( __FILE__ ) . 'public/class-commons-booking.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 * @TODO:
 *
 * - replace Commons_Booking with the name of the class defined in
 *   `class-commons-booking.php`
 */
register_activation_hook( __FILE__, array( 'Commons_Booking', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Commons_Booking', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Commons_Booking with the name of the class defined in
 *   `class-commons-booking.php`
 */
add_action( 'plugins_loaded', array( 'Commons_Booking', 'get_instance' ) );

/* ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 * ---------------------------------------------------------------------------- */

/*
 * @TODO:
 *
 * - replace `class-commons-booking-admin.php` with the name of the plugin's admin file
 * - replace Commons_Booking_Admin with the name of the class defined in
 *   `class-commons-booking-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && (!defined( 'DOING_AJAX' ) || !DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-commons-booking-admin.php' );
	add_action( 'plugins_loaded', array( 'Commons_Booking_Admin', 'get_instance' ) );
}
