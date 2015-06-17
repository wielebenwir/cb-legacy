<?php 
/**
 * Template for item in booking 
 *
 * @since   0.0.1
 */
?>
<div class="cb-headline"><?php echo  __( ' Your Booking: ' ); ?><a href="<?php echo get_permalink($item['ID']); ?>"><?php echo get_the_title ($item['ID']); ?></a></div>
  <div class="cb-booking-item cb-box">
  <div class="img" style="float:right;"><?php if ( has_post_thumbnail( $item['ID'] ) ) { echo get_the_post_thumbnail( $item['ID'], 'thumbnail' ); } ?></div>
  <div class="right"><?php echo get_the_content( $item['ID'] ); ?></div>
</div>