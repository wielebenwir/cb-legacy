<?php 
/**
 * Template for item description.
 *
 * @since   0.3
 */
?>
  <div class="cb-item cb-description">
    <?php echo $item_descr_short; ?> 
    <a href="#" class="cb-toggle"><?php echo __('Info...', 'commons-booking'); ?></a>
    <div class="full showhide">
      <?php echo $item_descr_full; ?>  
    </div>
  </div>
