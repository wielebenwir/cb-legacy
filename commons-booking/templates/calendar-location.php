<?php 
/**
 * Template for the booking calendar location header.
 *
 * @since   0.0.1
 */
?>
<div class="cb-location">
  <span class="cb-date"><?php echo $timeframe_date; ?></span>
  <div class="cb-location-name cb-headline"><?php echo $location[ 'name']; ?> – <?php echo $timeframe_comment; ?></div>
  <div class="cb-address"><?php echo ( implode( ' ', $location[ 'address' ] ) ); ?></div>
  <?php if (!$location[ 'contact_hide' ] ) { 
    echo ( '<div class="cb-contact">' . $location[ 'contact' ] . '</div>' ); 
    } ?>
</div>