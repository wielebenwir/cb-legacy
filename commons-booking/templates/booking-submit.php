<?php 

  

?>
<form id="confirm">
<input type="hidden" name="confirm" value="1">
<input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
<?php wp_nonce_field('booking-confirm-nonce'); ?>
<input type="submit" method="post" action="" value="<?php echo __( 'Confirm my booking' ); ?> ">
</form>

