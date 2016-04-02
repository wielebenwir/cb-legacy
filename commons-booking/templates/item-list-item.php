<?php 
/**
 * Template: Item Description in item list view. 
 *
 * @since   0.6
 */
?>
<div class="cb-list-item">
   <?php echo ( isset( $attributes['commons-booking_item_descr'][0] ) ) ? $attributes['commons-booking_item_descr'][0] : __('No description provided.', 'commons-booking'); ?>
</div>