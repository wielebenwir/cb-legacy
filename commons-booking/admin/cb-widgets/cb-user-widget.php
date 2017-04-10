<?php

/**
 * Add a widget for user functions (login, logout, register, "my bookings")
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 * @since    0.9
 */

// Create custom widget class extending WP_Widget
class CB_User_Widget extends WP_Widget {

	function __construct() {
		
		$plugin = Commons_Booking::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->settings = $plugin->get_cb_settings();

		parent::__construct(
			'cb_user_widget', // Base ID
			__( 'Commons Booking user', 'commons-booking' ), // Name
			array( 'description' => __( 'User functions (login, logout, register, my bookings)', 'commons-booking' ), ) // Args
		);

	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : "";
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
	/**
	 * Output the widget.
	 *
	 **/
	public function widget( $args, $instance ) {

		// And here do whatever you want
		echo $args[ 'before_widget' ];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		echo $this->render_userinfo();

		echo $args[ 'after_widget' ];
	}
	/**
	 * All the user info.
	 *
	 **/
	function render_userinfo() {

		$content = "";

		if ( is_user_logged_in() ) {

		  	$current_user = wp_get_current_user();

			$bookings_page_id = $this->settings->get_settings('pages', 'user_bookings_page_select');
			$bookings_page_url = get_permalink( $bookings_page_id );

	  		$content .= sprintf( __( 'Welcome, %s.', 'commons-booking' ), $current_user->display_name);
			$content .= ' <ul>'; 
			$content .= sprintf( __( '<li><a href="%s">My Bookings</a></li>', 'commons-booking' ),  $bookings_page_url );
			$content .= sprintf( __( '<li><a href="%s">My Profile</a></li>', 'commons-booking' ),  get_edit_profile_url() );     
			$content .= sprintf( __( '<li><a href="%s">Log out</a></li>', 'commons-booking' ),  wp_logout_url() ); 
	    	$content .= '</ul>';   
	
	    } else {

	    	$content = __('You are not logged in.', 'commons-booking');
	    	$content .= "<ul>";
	    	$content .= sprintf(__('<li><a href="%s">Login</a></li>', 'commons-booking'), wp_login_url()  );
	    	$content .= sprintf(__('<li><a href="%s">Register</a></li>', 'commons-booking'), wp_registration_url()  );
	    	$content .= "</ul>";
	    }

		return $content;

	}

}

// register CB_User_Widget 
function register_cb_user_widget() {
    register_widget( 'CB_User_Widget' );
}
add_action( 'widgets_init', 'register_cb_user_widget' );

