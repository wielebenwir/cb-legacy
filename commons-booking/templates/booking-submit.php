<?php 

  //@TODO: HASH, add prefix 

?>
<form id="confirm" action="<?php the_permalink(); ?>" method="GET">
  <input type="hidden" name="confirm" value="1">
  <input type="hidden" name="booking" value="<?php echo $this->hash; ?>">
  <input type="submit" value="<?php echo __( 'Confirm my booking', 'commons-booking' ); ?> ">
</form>

