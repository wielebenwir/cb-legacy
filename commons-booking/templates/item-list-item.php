<?php 
/**
 * Template: Item Description in item list view. 
 *
 * @since   0.6
 */
?>
<div class="cb-item-wrapper cb-box" id ="item_id<?=$attributes['id']; ?>"> <?php // div is closed in timeframes ?>
<?php if (is_singular()) { ?>
  <h2 class="cb-big"><a href="<?=$attributes['permalink'] ?>"><?=$attributes['title'] ?></a></h2>
    <div class="cb-list-item-description">
        <?php if ( has_post_thumbnail($attributes['id']) ) { ?>
          <div class="align-left">
            <?php echo get_the_post_thumbnail( $attributes['id'], 'thumbnail' ); ?>
          </div>
        <?php } ?>
        <?php echo ( isset( $attributes['meta']['commons-booking_item_descr'][0] ) ) ? $attributes['meta']['commons-booking_item_descr'][0] : __('No description provided.', 'commons-booking'); ?>
    </div>
<?php } else { // end if is singular ?>
  <div class="cb-list-item-description">
       <?php echo ( isset( $attributes['meta']['commons-booking_item_descr'][0] ) ) ? $attributes['meta']['commons-booking_item_descr'][0] : __('No description provided.', 'commons-booking'); ?>
  </div>
<?php } ?>

