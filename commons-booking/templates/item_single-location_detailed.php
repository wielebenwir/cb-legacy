<?php 
/**
 * Template for the booking calendar location header.
 *
 * @since   0.0.1
 */
?>
<div class="cb-location">
  <span class="cb-date"><?php echo $timeframe_date; ?></span>
  <div class="cb-location-name"><?php echo $location[ 'name']; ?><?php if (!empty ( $timeframe_comment ) ) { echo ' - ' .$timeframe_comment; } ?></div>
  <div class="cb-address cb-row"><?php echo ( implode( ' ', $location[ 'address' ] ) ); ?></div>
  <?php if (!$location[ 'contact_hide' ] ) { 
    echo ( '<div class="cb-contact cb-row">' . $location[ 'contact' ] . '</div>' ); 
    } ?>
</div>