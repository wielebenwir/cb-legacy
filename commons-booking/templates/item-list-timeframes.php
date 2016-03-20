<?php 
/**
 * Template: Timeframe for item list view. 
 *
 * @since   0.6
 */
?>
<?php // timeframes ?>

<?php foreach ( $attributes['timeframes'] as $tf ) { ?>
  <div class="cb-box">
      <span class="cb-button"><a href="<?php echo ( the_permalink() . '#timeframe' . $tf[ 'timeframe_id' ] );  ?>"> <?php echo __( 'Book here', $attributes['plugin_slug'] ); ?></a></span>
      <span class="cb-timeframe-title"><?=$tf['timeframe_title'] ?></span>
      <span class="cb-date"><?=$tf['date_range'] ?></span> – <span class=""><?=$tf['name'] ?></span>  
  </div>
<?php } // end foreach: timeframes ?>
