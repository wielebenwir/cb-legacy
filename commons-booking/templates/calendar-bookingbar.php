<?php 
/**
 *
 * @package   Commons_Booking_Public
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Display the booking bar
 *
 * @package Commons_Booking_Bookingbar
 */


?>
<div id="cb-bookingbar">
  <div id="cb-bookingbar-msg"></div>
  <div class="inner">
    <div class="cb-userinfo cb-small">

   <?php if ( is_user_logged_in() ) { ?>
      <?php 
        global $current_user;
        // get_currentuserinfo();
        echo __( 'Logged in as', 'commons-booking' ) . ': ' . $current_user->display_name;
      ?>
    </div>
  <div class="booking">
    <div id="intro">
    <?php echo __( 'Book this item:', 'commons-booking' ); ?>
    </div>
    <div id="date-start">
        from
      </div>
      <div id="date-end">
        date till
      </div>
      <div id="cb-submit">
        <a href="#" class="button cb-button">
          <?php echo __( 'Book now', 'commons-booking' ); ?>
        </a>
      </div>
  </div>
    <?php // Form fields to save the selection ?>
    <form id="booking-selection" action="<?php echo $this->target_url; ?>" method="post">
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
      <a href="<?php echo wp_login_url(); ?>"><?php echo __( 'Login' ); ?></a> | <a href="<?php echo wp_registration_url(); ?>"><?php echo __( 'Register' ); ?></a>
    <?php } ?>
  </div>
</div>