<?php 
/**
 * Template for the booking bar. 
 *
 * @since   0.5
 */
?>
<div id="cb-bookingbar">
  <div id="cb-bookingbar-msg"></div>
  <div class="inner">
   <?php if ( is_user_logged_in() ) { ?>
        <?php 
        global $current_user;
        echo '<div class="cb-userinfo cb-small">' . __( 'Logged in as', 'commons-booking' ) . ' <a href="'. get_edit_user_link( $current_user->ID ) .'">'. esc_attr( $current_user->user_nicename ) .'</a></div>';
      ?>
  <div class="booking">
    <div id="intro">
    <?php echo __( 'Book this item:', 'commons-booking' ); ?>
    </div>
    <div id="date-start">
        
      </div>
      <div id="date-end">
        
      </div>
      <div id="cb-submit">
        <a href="#" class="cb-button">
          <?php echo __( 'Book now', 'commons-booking' ); ?>
        </a>
      </div>
  </div>
    <?php // Form fields to save the selection ?>
    <form id="booking-selection" action="<?php echo esc_url ( $attributes['target_url'] ); ?>" method="post">
      <input type="hidden" name="date_start">
      <input type="hidden" name="date_end">
      <input type="hidden" name="timeframe_id">
      <input type="hidden" name="item_id">
      <input type="hidden" name="location_id">
      <input type="hidden" name="create" value="1">
      <?php wp_nonce_field('booking-review-nonce'); ?>
    </form>
    <div id="data"></div>
    <div id="debug"></div>
    <?php } else { ?>
      <p class="cb-big"><?php echo __( 'You have to be registered to book.', 'commons-booking' ); ?></p>
      <a href="<?php echo wp_login_url(); ?>" class="cb-button"><?php echo __( 'Login', 'commons-booking' ); ?></a> <a href="<?php echo wp_registration_url(); ?>" class="cb-button"><?php echo __( 'Register', 'commons-booking' ); ?></a><br><br>
    <?php } ?>
  </div>
</div>