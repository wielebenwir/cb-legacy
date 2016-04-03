<?php //var_dump($attributes); ?>

<div class="cb-list cb-box">
  <div class="cb-list-item">
      <div class="align-right"><?php if ( has_post_thumbnail() ) { the_post_thumbnail();} ?></div>
      <div class="right"><?php echo $item['description']; ?></div>
  </div>
  <?php if ( isset( $item['location'] ) ) { ?>
    <ul class="cb-list-item-timeframe">
      <?php foreach ( $item['location'] as $location ) { ?>
        <li class="cb-small cb-row">
          <span class="cb-date"><?php echo date ('d.m.', strtotime($location['date_start']) ); ?></span> - <span class="cb-date"> 
          <?php echo date ( 'd.m.', strtotime( $location[ 'date_end' ]) ); ?> </span>: 
          <?php echo $location[ 'name' ]; ?>,  <?php echo $location['address']['street']; ?>, <?php echo $location['address']['city'];  ?>
          <span class="cb-action"><a href="<?php echo ( $item['permalink'] . '#timeframe' . $location[ 'name' ] );  ?>" class="cb-button"> <?php echo __( 'Book here', Commons_Booking::$plugin_slug ); ?></a></span>
        </li>
      <?php } // end foreach location ?>
    </ul>
  <?php } // end if isset location ?>
  </div>