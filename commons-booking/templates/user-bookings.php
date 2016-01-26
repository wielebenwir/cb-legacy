

  <div class="cb-headline"><?php echo __('My Bookings', 'commons-booking'); ?></div>
  <div class="cb-box">
    <?php $bookingsindex = count($user_bookings);
    while($bookingsindex) {
        $booking = $user_bookings[--$bookingsindex];
        if ($booking['status'] == 'confirmed') {
          $d_status = __('Confirmed', 'commons-booking' );
          $d_link = get_the_permalink ( $review_page_id ) . '?booking=' .$booking['hash']; 
          $d_button = '&nbsp; | <a href="'. $d_link .'">' . __( 'Details' ) . '</a>';
        } elseif ( $booking['status'] == 'canceled' ) {
          $d_button = '';
          $d_status = __('Canceled', 'commons-booking' );
        }  else {
          $d_button = '';
          $d_status = __('Pending', 'commons-booking' );
        }
     
          ?>

     <div class="cb-row">
     <strong><?php echo get_the_title( $booking['item_id'] ); ?></strong>: 
     <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_start'])); ?></span> - 
     <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_end'])); ?></span>
          <span class="align-right"><?php echo $d_button; ?></span>

    <span class="align-right <?php echo $booking['status']; ?>"><?php echo $d_status; ?></span>

    </div>
<?php } ?>
</div>
