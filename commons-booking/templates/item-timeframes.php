<?php 
/**
 * Template for item single view including location and calendar. 
 *
 * @since   0.5
 */
?>

<?php // timeframes ?>
<?php foreach ( $attributes['timeframes'] as $tf ) { ?>
   <div class="cb-timeframe" data-tfid="<?= $tf['timeframe_id'] ?>" data-itemid="<?=$attributes['item']['ID'] ?>" data-locid="<?= $tf['location_id'] ?>">   
    <div class="cb-location">
      <span class="cb-date"><?=$tf['date_range'] ?></span>
      <div class="cb-location-name">
        <?=$tf['name'] ?>
        <span class="cb-timeframe-description"><?=$tf['timeframe_title'] ?></span>
      </div>
      <div class="cb-address cb-row"><?=$tf['address'] ?></div>
      <div class="cb-contact cb-row"><?=$tf['contact'] ?></div>
    </div>
    <div id ="timeframe_<?=$tf['timeframe_id'] ?>" class="cb_timeframe_form">
        <ul class="cb-calendar">
          <?php // calendar cells ?>
          <?php foreach ( $tf['calendar'] as $cell ) { ?>
            <li id="<?=$cell['id'] ?>" class="cb-tooltip <?=$cell['weekday_code'] ?> <?=$cell['status'] ?>">
              <span class="cb-date"><?=$cell['date_short'] ?></span>
              <span class="cb-day"><?=$cell['day_short'] ?></span>
            </li>
          <?php } // end foreach: cell ?>
        </ul>
    </div>
<?php } // end foreach: timeframes ?>
