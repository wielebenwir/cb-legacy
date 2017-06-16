<?php 
/**
 * Template: Timeframe for item list view. 
 *
 * @since   0.6
 */
 
$item_id = $attributes['item']['ID'];
?>
<?php // timeframes ?>
<div class="cb-table">
  <!-- TEMPLATE: item-list-timeframes-compact -->
  <?php foreach ( $attributes['timeframes'] as $tf ) { ?>
    <div class="cb-row" id="timeframe_id_<?=$tf[ 'timeframe_id' ]; ?>">
      <a href="<?php echo ( get_the_permalink( $item_id ) . '#timeframe' . $tf[ 'timeframe_id' ] );  ?>" class="cb-button align-right"> <?php echo __( 'Book here', 'commons-booking'); ?></a>
      <h3 class="cb-big"><a href="<?php echo ( get_the_permalink( $item_id ) ); ?>"><?php echo ( get_the_title( $item_id ) ); ?></a></h3>
      <span class="cb-date"><?=$tf['date_range'] ?></span> <span class="cb-timeframe-title"><?=$tf['timeframe_title'] ?></span>
    </div>
  <?php } // end foreach: timeframes ?>
</div>