<?php

// Create custom widget class extending WPH_Widget
class CB_User_Widget extends WPH_Widget {

	function __construct() {
		
		$plugin = Commons_Booking::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$this->settings = $plugin->get_cb_settings();

		// Configure widget array
		$args = array(
			'label' => __( 'User Widget', $this->plugin_slug ),
			'description' => __( 'User functions: Login/Logout, My bookings, Profile', $this->plugin_slug ),
		);

		// Configure the widget fields
		// Example for: Title ( text ) and Amount of posts to show ( select box )
		$args[ 'fields' ] = array(
			// Title field
			array(
				// field name/label									
				'name' => __( 'Title', $this->plugin_slug ),
				// field description					
				'desc' => __( 'Enter the widget title.', $this->plugin_slug ),
				// field id		
				'id' => 'title',
				// field type ( text, checkbox, textarea, select, select-group )								
				'type' => 'text',
				// class, rows, cols								
				'class' => 'widefat',
				// default value						
				'std' => __( 'My account', $this->plugin_slug ),
				/*
				  Set the field validation type/s
				  ///////////////////////////////

				  'alpha_dash'
				  Returns FALSE if the value contains anything other than alpha-numeric characters, underscores or dashes.

				  'alpha'
				  Returns FALSE if the value contains anything other than alphabetical characters.

				  'alpha_numeric'
				  Returns FALSE if the value contains anything other than alpha-numeric characters.

				  'numeric'
				  Returns FALSE if the value contains anything other than numeric characters.

				  'boolean'
				  Returns FALSE if the value contains anything other than a boolean value ( true or false ).

				  ----------

				  You can define custom validation methods. Make sure to return a boolean ( TRUE/FALSE ).
				  Example:

				  'validate' => 'my_custom_validation',

				  Will call for: $this->my_custom_validation( $value_to_validate );

				 */
				'validate' => 'alpha_dash',
				/*

				  Filter data before entering the DB
				  //////////////////////////////////

				  strip_tags ( default )
				  wp_strip_all_tags
				  esc_attr
				  esc_url
				  esc_textarea

				 */
				'filter' => 'strip_tags|esc_attr'
			),
			// Output type checkbox
			array(
				'name' => __( 'Output as list', $this->plugin_slug ),
				'desc' => __( 'Wraps posts with the <li> tag.', $this->plugin_slug ),
				'id' => 'list',
				'type' => 'checkbox',
				// checked by default: 
				'std' => 1, // 0 or 1
				'filter' => 'strip_tags|esc_attr',
			),
				// add more fields
		); // fields array
		// create widget
		$this->create_widget( $args );
	}

	// Output function

	function widget( $args, $instance ) {

		// And here do whatever you want
		$out = $args[ 'before_title' ];
		$out .= $instance[ 'title' ];
		$out .= $args[ 'after_title' ];

		$out .= $this->render_userinfo();

		echo apply_filters( 'widget_text', $out );
	}

	function render_userinfo() {

		$content = "";
	  	$current_user = wp_get_current_user();
		// $option = get_option( 'commons-booking-settings-pages');
		// $id = $option['commons-booking_user_bookings_page_select'];
		$bookings_page_id = $this->settings->get_settings('pages', 'user_bookings_page_select');
		$bookings_page_url = get_permalink( $bookings_page_id );

		$content .= '<div class="textwidget">'; 
  		$content .= sprintf( __( 'Welcome, %s. ', 'commons-booking' ), $current_user->display_name);
    	$content .= sprintf( __( '<a href="%s">My Bookings</a> ', 'commons-booking' ),  $bookings_page_url );
    	$content .= sprintf( __( '<a href="%s">My Profile</a> ', 'commons-booking' ),  get_edit_profile_url() );     
    	$content .= sprintf( __( '<a href="%s">Log out</a> ', 'commons-booking' ),  wp_logout_url() ); 
    	$content .= '</div>';   
    	// printf(__('<a href="%s" class="cb-button">Logout</a> ', 'commons-booking' ),  wp_logout_url( home_url() ) );


		if ( ! is_user_logged_in() ) { // Not logged in
    		$content .=  wp_login_url();
    		$content .=  wp_registration_url();
    		// Not logged in.
		} else {

	  		$userinfo = sprintf( '__(Logged in as %s) ', $current_user->display_name );

    		// Logged in.
		}

		return $content;

	}

}

// Register widget
if ( !function_exists( 'cb_widget_register' ) ) {

	function cb_widget_register() {
		register_widget( 'CB_User_Widget' );
	}

	add_action( 'widgets_init', 'cb_widget_register', 1 );
}
