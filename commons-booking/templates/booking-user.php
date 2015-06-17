<?php 
/**
 * Template for Booking User Display. 
 *
 */
?>
<div class="cb-headline"><?php echo __( ' Your information ' ); ?></div>
<div class="cb-booking-user cb-box">
  <div><?php echo __( ' Full name: ' ); ?><strong><?php echo $user['name']; ?></strong></div>
  <div><?php echo __( ' Email: ' ) ?><strong><?php echo $user['email']; ?></strong></div>
  <div><?php echo __( ' Address: ' ) ?><strong><?php echo $user['address']; ?></strong></div>
  <div><?php echo __( ' Phone number: ' ) ?><strong><?php echo $user['phone']; ?></strong></div>
</div>
