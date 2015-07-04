<?php 
/**
 * Template for item lists.
 *
 * @since   0.0.1
 */
?>


<?php foreach ( $items as $item ) { ?>
<div class="cb-list-item">
  <div class="cb-item cb-list">
    <div class="cb-headline"><a href="<?php echo $item['permalink']; ?>"><?php echo $item['title']; ?></a></div>
    <?php if ( isset( $item['thumb'] )) { ?><div class="align-right"><?php echo $item['thumb']; ?></div><? } ?>
    <div class="right"><?php echo $item['description']; ?></div>
  </div>
  <?php if ( isset( $item['location'] ) ) { ?>
    <ul class="cb-list-item-timeframe">
      <?php foreach ( $item['location'] as $location ) { ?>
        <li class="cb-small">
          <span class="cb-date"><?php echo date ('d.m.', strtotime($location['date_start']) ); ?></span> - <span class="cb-date"> 
          <?php echo date ( 'd.m.', strtotime( $location[ 'date_end' ]) ); ?> </span>: 
          <?php echo $location[ 'name' ]; ?>,  <?php echo $location['address']['street']; ?>, <?php echo $location['address']['city'];  ?>
          <span class="cb-action"><a href="<?php echo ( $item['permalink'] . '#timeframe' . $location[ 'name' ] );  ?>"> <?php echo __( 'Book here '); ?></a></span>
        </li>
      <? } // end foreach location ?>
    </ul>
  <?php } // end if isset location ?>
  </div>
<? } // end foreach ?>
</div>

