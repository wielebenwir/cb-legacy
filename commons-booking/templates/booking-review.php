<?php 
/**
 * Template for the main booking review page: Item info, Booking info, User Info
 *
 * @since 0.3
 *
 */
?>
<?php // ITEM  ?>
<div class="cb-headline"><?php echo  __( 'Your Booking:', $attributes['plugin_slug'] ) . ' ' . $attributes['item_name']; ?></div>
  <div class="cb-booking-item cb-table">
  <div class="cb-row"><?php echo $attributes['item_content']; ?></div>
</div>

<?php // REVIEW ?>
<div class="cb-headline"><?php echo __( ' Pickup & Return ', $attributes['plugin_slug'] ); ?></div>
<div class="cb-booking-review cb-table">
  <div class="cb-row">
    <span class="cb-row-title"><?php echo __( 'Pickup at:', $attributes['plugin_slug'] ); ?></span><?php echo $attributes['location_name']; ?>
  </div>
  <div class="cb-row">
    <span class="cb-row-title"><?php echo __( 'Pickup date:', $attributes['plugin_slug'] ); ?></span><span class="cb-date"><?php echo $attributes['date_start'] ?></span>
  </div>
  <div class="cb-row">
    <span class="cb-row-title"><?php echo __( 'Return date:', $attributes['plugin_slug'] ); ?></span> <span class="cb-date"><?php echo $attributes['date_end']; ?></span>
  </div>  
  <div class="cb-row">
    <span class="cb-row-title"><?php echo __( 'Opening hours:', $attributes['plugin_slug'] ); ?></span> <span class="cb-date"><?php echo $attributes['location_openinghours']; ?></span>
  </div>
</div>

<?php //USER ?>
<div class="cb-headline"><?php echo __( ' Your information ', $attributes['plugin_slug'] ); ?></div>
<div class="cb-booking-user cb-table">
  <div class="cb-row"><span class="cb-row-title"><?php echo __( 'Full name:', $attributes['plugin_slug'] ); ?></span><?php echo ( $attributes['first_name'] . ' ' . $attributes['last_name'] ); ?></div>
  <div class="cb-row"><span class="cb-row-title"><?php echo __( 'Email:', $attributes['plugin_slug'] ); ?></span><?php echo $attributes['user_email']; ?></div>
  <div class="cb-row"><span class="cb-row-title"><?php echo __( 'Address:', $attributes['plugin_slug'] ); ?></span><?php echo $attributes['user_address']; ?></div>
  <div class="cb-row"><span class="cb-row-title"><?php echo __( 'Phone number:', $attributes['plugin_slug'] ); ?></span><?php echo $attributes['user_phone']; ?></div>
</div>

<?php //LOCATION ?>
<div class="cb-headline"><?php echo  __( ' Location information ', $attributes['plugin_slug'] ); ?></div>
<div class="cb-booking-location cb-table">
  <div class="cb-row">
    <span class="cb-row-title"><?php _e('Address:', 'commons-booking'); ?></span><?php echo $attributes['location_address']; ?>
  </div>
  <div class="cb-row">
  <span class="cb-row-title"><?php _e('Contact:', 'commons-booking'); ?></span><?php echo $attributes['location_contact']; ?>
  </div>
</div>
