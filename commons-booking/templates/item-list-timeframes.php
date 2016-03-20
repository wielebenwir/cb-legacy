<?php 
/**
 * Template: Timeframe for item list view. 
 *
 * @since   0.6
 */
?>
<?php // timeframes ?>
   <div class="cb-box">   
<?php foreach ( $attributes['timeframes'] as $tf ) { ?>
    <div class="cb-timeframe cb-row cb-small">
      <span class="cb-date"><?=$tf['date_range'] ?></span>
        <?=$tf['name'] ?>
        <span class="cb-timeframe-description"><?=$tf['timeframe_title'] ?></span>
        <span class="cb-button"><a href="<?php echo ( the_permalink() . '#timeframe' . $tf[ 'timeframe_id' ] );  ?>"> <?php echo __( 'Book here', $attributes['plugin_slug'] ); ?></a></span>
    </div>
<?php } // end foreach: timeframes ?>
  </div>
