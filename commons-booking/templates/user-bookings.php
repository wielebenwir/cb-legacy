

  <div class="cb-headline"><?php echo __('My Bookings'); ?></div>
  <div class="cb-box">
    <?php foreach ($user_bookings as $booking) { ?>
      
      <?php 
        if ($booking['status'] == 'confirmed') {
          $d_link = get_the_permalink ( $review_page_id ) . '?booking=' .$booking['hash']; 
          $d_button = '| <a href="'. $d_link .'">' . __( 'Details' ) . '</a>';
        } else {
          $d_button = '';
        }
          ?>

     <div class="cb-row">
     <strong><?php echo get_the_title( $booking['item_id'] ); ?></strong>: 
     <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_start'])); ?></span> - 
     <span class="cb-date"><?php echo date( 'd.m.y', strtotime($booking['date_end'])); ?></span>
          <span class="align-right"><?php echo $d_button; ?></span>

    <span class="align-right <?php echo $booking['status']; ?>"><?php echo $booking['status']; ?></span>

    </div>
<?php } ?>
</div>
