<?php 
/**
 * Template for Booking Review Display. 
 *
 */
?>
<div class="cb-headline"><?php echo __( ' Pickup & Return ' ); ?></div>
<div class="cb-booking-review cb-box">
  <div>
    <?php echo __( ' Pickup at: ' ); ?><strong><?php echo get_the_title($location_id ); ?></strong>
  </div>
  <div>
    <?php echo __( ' Pickup date:' ) ?> <span class="cb-date"><?php echo $nice_date_start; ?></span>
  </div>
  <div>
    <?php echo __( ' Return date: ' ) ?><span class="cb-date"><?php echo $nice_date_end; ?></span>
  </div>
</div>