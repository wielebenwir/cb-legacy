<?php 

  //@TODO: HASH, add prefix 

?>
<form id="confirm" action="<?php get_the_permalink(); ?>" method="GET">
  <input type="hidden" name="confirm" value="1">
  <input type="hidden" name="page_id" value="<?php echo $page_confirmed; ?>">
  <input type="hidden" name="booking" value="<?php echo $this->hash; ?>">
  <input type="submit" value="<?php echo __( 'Confirm my booking', 'commons-booking' ); ?> ">
</form>

