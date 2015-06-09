<?php 
/**
 * Template for the booking calendar location header.
 *
 * @since   0.0.1
 */
?>
<div class="cb-location">
  <span class="cb-date"><?php echo $timeframe_date; ?></span>
  <h2><?php echo $location[ 'name']; ?></h2>
  <p class="cb-comment"><?php echo $timeframe_comment; ?></p>
  <?php if (!$location[ 'contact_hide' ] ) { 
    echo ( '<p class="cb-contact">' . $location[ 'contact' ] . '</p>' ); 
    } ?>
  <div class="cb-address"><?php echo ( implode( ' ', $location[ 'address' ] ) ); ?></div>
</div>