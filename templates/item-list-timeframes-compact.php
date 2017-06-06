<?php 
/**
 * Template: Timeframe for item list view. 
 *
 * @since   0.6
 */
?>
<?php // timeframes ?>
<div class="cb-table">
  <!-- TEMPLATE: item-list-timeframes-compact -->
  <?php foreach ( $attributes['timeframes'] as $tf ) { ?>
    <div class="cb-row" id="timeframe_id_<?=$tf[ 'timeframe_id' ]; ?>">
        <a href="<?php echo ( get_the_permalink($attributes['item']['ID']) . '#timeframe' . $tf[ 'timeframe_id' ] );  ?>" class="cb-button align-right"> <?php echo __( 'Book here', 'commons-booking'); ?></a>
        <span class="cb-date"><?=$tf['date_range'] ?></span> <span class="cb-timeframe-title"><?=$tf['timeframe_title'] ?></span>
        <span class="cb-location-name"><?=$tf['name'] ?></span>
        <span class="cb-address"><?=$tf['address'] ?></span>
    </div>
  <?php } // end foreach: timeframes ?>
  </div>
</div><?php //closes .cb-box ?>