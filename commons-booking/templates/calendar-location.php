<?php 
/**
 * Template for the booking calendar location header.
 *
 * @since   0.0.1
 */
?>
<div class="cb location">
  <span class="cb date"><?php echo $location_date; ?></span>
  <h2><?php echo $location_name; ?></h2>
  <p class="cb comment"><?php echo $timeframe_comment; ?></p>
  <p class="cb contact"><?php echo $location_contact; ?></p>
  <p class="cb geo"><?php echo ( 'lat: ' . $location_geo['latitude'] . ' long: ' . $location_geo['longitude'] ); ?></p>

</div>