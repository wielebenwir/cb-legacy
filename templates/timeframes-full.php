<?php 
/**
 * Template for item single view including location and calendar. 
 *
 * CB_Data::render_item_single() 
 *   => using timeframes-full template
 *
 * @since   0.5
 */
?>
<div class="cb-timeframes-wrapper">
  <!-- TEMPLATE: timeframes-full -->
<?php foreach ( $attributes['timeframes'] as $tf ) { ?>

  <a name="timeframe<?= $tf['timeframe_id'] ?>"></a>
   <div class="cb-timeframe cb-box" id="<?= $tf['timeframe_id'] ?>" data-tfid="<?= $tf['timeframe_id'] ?>" data-itemid="<?=$attributes['item']['ID'] ?>" data-locid="<?= $tf['location_id'] ?>">   
      <span class="cb-date"><?=$tf['date_range'] ?></span> <span class="cb-timeframe-title"><?=$tf['timeframe_title'] ?></span>
      <div class="cb-location-name cb-big">
        <?=$tf['name'] ?>   
      </div>
      <div class="cb-table">
      <div class="cb-address cb-row">
        <a href="http://maps.google.com/?q=<?=$tf['address'] ?>" target="_blank" class="cb-button align-right cb-small"><?=_e( 'Show in Maps', 'commons-booking' ); ?></a>
        <span class="cb-row-title"><?=_e('Address', 'commons-booking'); ?></span>
        <?=$tf['address'] ?></div>
      <div class="cb-opening-hours cb-row"><span class="cb-row-title"><?=_e('Opening hours', 'commons-booking'); ?></span><?=$tf['opening_hours'] ?></div>
      <div class="cb-contact cb-row"><span class="cb-row-title"><?=_e('Contact', 'commons-booking'); ?></span><?=$tf['contact'] ?></div>
    </div>
    <div id="timeframe_<?=$tf['timeframe_id'] ?>" class="cb_timeframe_form">
        <ul class="cb-calendar">
        <?php // optional: Row of weekday names 
        if ($tf['render_daynames'] == "on") { ?>
        <div class="cb-weekday-row">
          <span><?php echo __('Mon', 'commons-booking'); ?></span>
          <span><?php echo __('Tue', 'commons-booking'); ?></span>
          <span><?php echo __('Wed', 'commons-booking'); ?></span>
          <span><?php echo __('Thu', 'commons-booking'); ?></span>
          <span><?php echo __('Fri', 'commons-booking'); ?></span>
          <span><?php echo __('Sat', 'commons-booking'); ?></span>
          <span><?php echo __('Sun', 'commons-booking'); ?></span>
        </div>
        <?php } ?> 
          <?php // calendar cells ?>            
          <?php foreach ( $tf['calendar'] as $cell ) { ?>
            <li id="<?=$cell['id'] ?>" class="cb-tooltip <?=$cell['weekday_code'] ?> <?=$cell['status'] ?>" title="<?php echo $cell['tooltip']; ?>"><div class="cb-cal-inner"
              ><span class="cb-j"><?=$cell['date_short'] ?></span><span class="cb-M"><?=$cell['day_short'] ?> </span>
            </div>
            </li>
          <?php } // end foreach: cell ?>
        </ul>
    </div>
  </div>
<?php } // end foreach: timeframes ?>
</div>