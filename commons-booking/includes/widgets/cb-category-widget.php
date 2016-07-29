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
class CB_Category_Widget extends WP_Widget {

	function __construct() {

		parent::__construct(
			'cb_category_widget', // Base ID
			__( 'Commons Booking categories', 'commons-booking' ), // Name
			array( 'description' => __( 'List all item categories', 'commons-booking' ), ) // Args
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
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'New title', 'text_domain' );
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
		echo $args[ 'before_title' ];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		echo $args[ 'after_title' ];

		echo $this->render_categories();

		echo $args[ 'after_widget' ];
	}
	/**
	 * Get all item categories
	 *
	 **/
	function render_categories() {

		$args = array (
			'taxonomy' => 'cb_items_category',
			'echo' => FALSE,
			'title_li' => ''
			);

		$content = wp_list_categories( $args );

		return $content;

	}

}

// register CB_User_Widget 
function register_cb_category_widget() {
    register_widget( 'CB_Category_Widget' );
}
add_action( 'widgets_init', 'register_cb_category_widget' );

