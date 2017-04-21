<?php 
/**
 * Template for Booking Code Display. 
 *
 * @since 0.6
 *
 */
?>
<div class="cb-headline"><?php echo  __( 'Booking Comment', 'commons-booking' ); ?></div>
<div class="cb-table">
  <div class="cb-row">
       <?php echo display_cb_message( __('Thank you for leaving a comment!', 'commons-booking') ); ?>

  <span class="cb-row-title"><?php echo __( 'Your comment:', 'commons-booking' ); ?></span>

  <?php echo $attributes['comment'] ; ?></strong>
  </div>
</div>