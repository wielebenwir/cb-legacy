<?php 
/**
 * Template for item lists.
 *
 * @TODO: PREFIX
 * @since   0.0.1
 */
?>
<li class="cb-small">
<span class="cb-date"><?php echo date ('d.m.', strtotime($tf['date_start']) ); ?></span> - <span class="cb-date"> 
  <?php echo date ( 'd.m.', strtotime( $tf[ 'date_end' ]) ); ?> </span>: 
  <?php echo $location[ 'name' ]; ?>,  <?php echo $location['address']['street']; ?>, <?php echo $location['address']['city'];  ?>
  <span class="cb-action"><a href="<?php echo ( get_the_permalink ($item_id) . '#timeframe' . $tf[ 'id' ] );  ?>"> <?php echo __( 'Book here '); ?></a></span>
</li>