<?php 
/**
 * Template for location in booking 
 *
 * @since   0.0.1
 */
?>
<div class="cb-headline"><?php echo  __( ' Location information: ' ); ?><?php echo $location['name']; ?></div>
<div class="cb-booking-location cb-box">
  <div class="img" style="float:right;">
    <?php if ( has_post_thumbnail( $location['id'] ) ) { echo get_the_post_thumbnail( $location['id'], 'thumbnail' ); } ?>
  </div>
  <div class="cb-adress">
    <?php echo implode(', ', $location['address']); ?>
  </div>
  <div class="cb-contactinfo">
    <?php echo $location['contact']; ?>
  </div>
</div>