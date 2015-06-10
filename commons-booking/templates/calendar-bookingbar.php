<?php 
global $wpdb;
?>
<div id="cb-bookingbar">

  <?php if ( is_user_logged_in() ) { ?>
  <div class="cb-userinfo cb-small">
    <?php 
    global $current_user;
      get_currentuserinfo();
      echo ( __( 'Logged in as:' ));
      echo $current_user->display_name;
    ?>
  </div>
  <div id="date-from">
    from
  </div>
  <div id="date-till">
    date till
  </div>

  <div id="cb-submit">
    <a href="#" class="button cb-button">
      Submit
    </a>
  </div>
  <?php } else { ?>
    <p class="cb-big"><?php echo __( 'You have to be registered to book.' ); ?></p>
    <a href="<?php echo wp_login_url(); ?>"><?php echo __( 'Login' ); ?></a> | <a href="<?php echo wp_registration_url(); ?>"><?php echo __( 'Register' ); ?></a>
  <?php } ?>
</div>