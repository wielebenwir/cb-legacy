<?php 
/**
 * Template: Item Description in location list view. 
 *
 * filter::the_content
 *   => Commons_Booking::cb_content() /cb_[post_type]s/[post_name] (SINGLE post)
 *     => CB_Data::render_location_single() 
 *       => using location-single template
 *
 * @author Annesley Newholm <annesley_newholm@yahoo.it>
 * @since  0.9.2.5
 */
?>
<!-- TEMPLATE: location-single -->
<div class="commons-init commons-booking-hcard-map"><?php _e( 'map loading', 'commons-booking' ); ?>...</div>

<?php //for the map if any ?>
<div class="vcard">
  <a class="url fn org cb-meta-info" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
  <div class="cb-list-location-description cb-popup"><?php the_attribute( $attributes, 'address/description' ); ?></div>
  <div class="cb-list-location-openinghours cb-popup"><?php the_post_custom( 'commons-booking_location_openinghours' ); ?></div>

  <div class="cb-list-location-location adr">
    <div class="geo">
        <span class="latitude"><?php  the_attribute( $attributes, 'address/latitude' );  ?></span>,
        <span class="longitude"><?php the_attribute( $attributes, 'address/longitude' ); ?></span>
        <span class="icon"><?php      the_attribute( $attributes, 'address/icon_url' );  ?></span>
    </div>
  </div>
</div>

<div class="cb-list-location-items cb-popup">
  <?php
    // --------------------------------------------- Item list --------------------------------------------- 
    if ( isset( $attributes['items'] ) && is_array( $attributes['items'] ) ) {
      foreach ( $attributes['items'] as $item ) {
        echo ( cb_get_template_part( 'item-list-timeframes-location', $item['template_vars'], TRUE ) );
      }
    }
  ?>
</div>
