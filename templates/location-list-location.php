<?php 
/**
 * Template: Location Description in location list view. 
 *
 * @author Annesley Newholm <annesley_newholm@yahoo.it>
 * @since  0.9.2.5
 */
?>
<div class="cb-location-wrapper cb-box vcard" id="location_id<?php the_ID(); ?>"> <?php // div is closed below ?>
  <!-- TEMPLATE: location-list-location -->
  <?php if (is_singular()) { ?>
    <h2 class="cb-big"><a class="url fn org" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <div class="align-right"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
    <div class="cb-list-location-description cb-popup"><?php the_attribute( $attributes, 'address/description' ); ?></div>
    <div class="cb-list-location-openinghours cb-popup"><?php the_post_custom( 'commons-booking_location_openinghours' ); ?></div>
    <div class="cb-list-location-content"><?php the_post_content(); ?></div>
  <?php } else { // end if is singular ?>
    <div class="cb-list-location-description description cb-popup"><?php the_post_content(); ?></div>
  <?php } ?>

  <div class="cb-list-location-location adr">
    <div class="geo">
      <span class="latitude"><?php    the_attribute( $attributes, 'address/latitude' );  ?></span>,
      <span class="longitude"><?php   the_attribute( $attributes, 'address/longitude' ); ?></span>
      <span class="icon"><?php        the_attribute( $attributes, 'address/icon_url' );  ?></span>
      <span class="icon-shadow"><?php the_attribute( $attributes, 'address/icon_shadow_url' );  ?></span>
    </div>
  </div>

  <div class="cb-list-location-items cb-popup">
    <?php
      // --------------------------------------------- Item list --------------------------------------------- 
      // TODO: item list for this location for today / a given date
      // TODO: this should be a post loop
      if ( isset( $attributes['items'] ) && is_array( $attributes['items'] ) ) {
        foreach ( $attributes['items'] as $item ) {
          echo ( cb_get_template_part( 'item-list-timeframes-location', $item['template_vars'] ) );
        }
      }
    ?>
  </div>
  <?php // div is closed here because we do not have any further content ?>
</div>