<?php //print_r($attributes); ?>
<div class="cb-items-list">
<?php foreach ( $attributes as $item ) { ?>
  <h2 class="cb-big"><a href="<?=$item['permalink'] ?>"><?=$item['title'] ?></a></h2>
  <div class="cb-list">
    <div class="cb-list-item">
        <div class="align-right"><?php if ( has_post_thumbnail($item['item_id']) ) { the_post_thumbnail($item['item_id']);} ?></div>
    </div>
    <?php if ( isset( $item['location'] ) ) { ?>
      <div class="cb-table">
        <?php foreach ( $item['location'] as $location ) { ?>
          <div class="cb-row">
            <span class="cb-date"><?php echo date ('d.m.', strtotime($location['date_start']) ); ?></span> - <span class="cb-date"> 
            <?php echo date ( 'd.m.', strtotime( $location[ 'date_end' ]) ); ?> </span>: 
            <?php echo $location[ 'name' ]; ?>,  <?php echo $location['address']['street']; ?>, <?php echo $location['address']['city'];  ?>
            <a href="<?php echo ( $item['permalink'] . '#timeframe' . $location[ 'name' ] );  ?>" class="cb-button align-right"> <?php echo __( 'Book here', Commons_Booking::$plugin_slug ); ?></a>
          </div>
        <?php } // end foreach location ?>
      </div>
    <?php } // end if isset location ?>
    </div>
  <?php } ?>>
  </div>