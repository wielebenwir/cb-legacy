<?php 
/**
 * Template for the booking calendar cell.
 *
 * @since   0.0.1
 */
?>
<li id="<?php echo $counter; ?>" class="tooltip <?php echo $display_day . ' '. $class ; ?>" title="This is my image tooltip message!">
  <span class="cb-date"><?php echo $display_date; ?></span>
  <span class="cb-day"><?php echo $display_day; ?></span>
</li>