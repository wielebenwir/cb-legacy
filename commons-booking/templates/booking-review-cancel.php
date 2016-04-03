<?php 
/**
 * Template for Cancel Booking Button. 
 *
 * @since 0.6
 *
 */
?>
<form id="cancel" action="<?php the_permalink(); ?>" method="GET">
  <input type="hidden" name="cancel" value="1">
  <input type="hidden" name="page_id" value="<?php echo $attributes['page_confirmation']; ?>">
  <input type="hidden" name="booking" value="<?php echo $attributes['hash']; ?>">
  <input type="submit" id="wp-submit" class="cb-button warning" value="<?php echo __( 'Cancel my booking', 'commons-booking' ); ?>" >
</form>
