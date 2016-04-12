<?php 
/**
 * Template: Timeframe for item list view. 
 *
 * @since   0.6
 */
?>
<?php // timeframes ?>
<div class="cb-table">
  <?php foreach ( $attributes['timeframes'] as $tf ) { ?>
    <div class="cb-row" id="timeframe_id_<?=$tf[ 'timeframe_id' ]; ?>">
        <span><a href="<?php echo ( the_permalink($attributes['item']['ID']) . '#timeframe' . $tf[ 'timeframe_id' ] );  ?>" class="cb-button align-right"> <?php echo __( 'Book here', 'commons-booking'); ?></a></span>
        <span class="cb-date"><?=$tf['date_range'] ?></span> <span class="cb-timeframe-title"><?=$tf['timeframe_title'] ?></span>
        <span class="cb-big"><?=$tf['name'] ?></span>
        <span><?=$tf['address'] ?></span>
    </div>
  <?php } // end foreach: timeframes ?>
  </div>
</div><?php //closes .cb-box ?>