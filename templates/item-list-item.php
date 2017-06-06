<?php 
/**
 * Template: Item Description in item list view. 
 *
 * @since   0.6
 *
 * filter::shortcode cb_items
 *   => Commons_Booking::item_shortcode()
 *     => CB_Public_Items::output($query_args)         
 *       => foreach $post: CB_Data::render_[post_type]_list($each_post) (SELECTED posts)
 *         => this template
 *
 * filter::the_content
 *   Commons_Booking::cb_content() /[post_type]_page_select (ALL posts)
 *     => CB_Public_Items::output(NO ARGS) -> select all items
 *       => foreach $post: CB_Data::render_[post_type]_list($each_post)
 *         => this template
 *
 * filter::the_content
 *   => Commons_Booking::cb_content() /cb_[post_type]s/[post_name] (SINGLE post)
 *     => CB_Data::render_[post_type]_list($single_post) 
 *       => this template
 *
 */
?>
<div class="cb-item-wrapper cb-box" id="item_id<?=$attributes['id']; ?>"> <?php // div is closed in timeframes ?>
  <!-- TEMPLATE: item-list-item -->
<?php if (is_singular()) { ?>
  <h2 class="cb-big"><a href="<?=$attributes['permalink'] ?>"><?=$attributes['title'] ?></a></h2>
  <div class="cb-list-item-description">
      <?php if ( has_post_thumbnail($attributes['id']) ) { ?>
        <div class="align-left">
          <?php echo get_the_post_thumbnail( $attributes['id'], 'thumbnail' ); ?>
        </div>
      <?php } ?>
      <?php echo ( isset( $attributes['meta']['commons-booking_item_descr'][0] ) ? $attributes['meta']['commons-booking_item_descr'][0] : __('No description provided.', 'commons-booking') ); ?>
  </div>
<?php } else { // end if is singular ?>
  <div class="cb-list-item-description">
       <?php echo ( isset( $attributes['meta']['commons-booking_item_descr'][0] ) ? $attributes['meta']['commons-booking_item_descr'][0] : __('No description provided.', 'commons-booking') ); ?>
  </div>
<?php } ?>

