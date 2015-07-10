<?php // ITEM  ?>
<div class="cb-headline"><?php echo  __( ' Your Booking: ' ); ?><a href="<?php echo get_permalink($item['ID']); ?>"><?php echo get_the_title ($item['ID']); ?></a></div>
  <div class="cb-booking-item cb-box">
  <div class="img" style="float:right;"><?php if ( has_post_thumbnail( $item['ID'] ) ) { echo get_the_post_thumbnail( $item['ID'], 'thumbnail' ); } ?></div>
  <div class="right"><?php echo get_the_content( $item['ID'] ); ?></div>
</div>

<?php // REVIEW ?>
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

<?php //USER ?>
<div class="cb-headline"><?php echo __( ' Your information ' ); ?></div>
<div class="cb-booking-user cb-box">
  <div><?php echo __( ' Full name: ' ); ?><strong><?php echo $user['name']; ?></strong></div>
  <div><?php echo __( ' Email: ' ) ?><strong><?php echo $user['email']; ?></strong></div>
  <div><?php echo __( ' Address: ' ) ?><strong><?php echo $user['address']; ?></strong></div>
  <div><?php echo __( ' Phone number: ' ) ?><strong><?php echo $user['phone']; ?></strong></div>
</div>

<?php //LOCATION ?>
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