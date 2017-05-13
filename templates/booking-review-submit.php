<?php 
/**
 * Template for Booking Submit Button. 
 *
 * @since 0.6
 *
 */
?>
<form id="confirm" action="<?php echo esc_url ( get_permalink( $attributes['page_confirmation'] ) ); ?>" method="GET">
  <input type="hidden" name="confirm" value="1">
  <input type="hidden" name="page_id" value="<?php echo esc_attr( $attributes['page_confirmation'] ); ?>">
  <input type="hidden" name="booking" value="<?php echo esc_attr ( $attributes['hash'] ); ?>">
  <input type="submit" id="wp-submit" class="cb-button" value="<?php echo __( 'Confirm my booking', 'commons-booking' ); ?> ">
</form>

