<?php 
/**
 * Template for item lists.
 *
 * @since   0.0.1
 */
?>
<div class="cb-item cb-list">
  <div class="cb-headline"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
  <div class="align-right">
    <?php if ( has_post_thumbnail( $post->ID ) ) { echo get_the_post_thumbnail( $post->ID, 'thumbnail' ); } ?>
 </div>
 <div class="right"><?php echo get_post_meta( get_the_ID(), 'commons-booking_item_descr', true );  //@TODO: PREFIX ?></div>
</div>