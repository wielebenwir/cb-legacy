<?php 

  //@TODO: HASH 

?>
<form id="cancel" action="<?php the_permalink(); ?>" method="GET">
  <input type="hidden" name="cancel" value="1">
  <input type="hidden" name="booking" value="<?php echo $this->hash; ?>">
  <input type="submit" value="<?php echo __( 'Cancel my booking', 'commons-booking' ); ?> ">
</form>

