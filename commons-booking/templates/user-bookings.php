<?php 
/**
 * Template for bookings list. 
 *
 * @since   0.5
 */
?>  

  <div class="cb-table">

  <?php foreach ($attributes['bookings'] as $booking) { ?>
    
     <div class="cb-row">
    <a href="<?php echo esc_url( add_query_arg ('booking', $booking['hash'], $attributes['review_page_link'] ) ); ?>" class="cb-button align-right <?php echo $booking['status']; ?>"><?php _e( 'Show booking', 'commons-booking'); ?></a>
       <span class="cb-big"><?php echo get_the_title( $booking['item_id'] ); ?></span>
      <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_start'])); ?></span> - 
      <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_end'])); ?></span>
    </div>
<?php } ?>
</div>
