

<div class="cb-headline"><?php echo __('Welcome, stranger!', $this->plugin_slug ); ?> </div>
<p><?php echo __('If you don´t have an account, please register here: ', $this->plugin_slug ); ?><a href="<?php echo wp_registration_url(); ?>"><?php echo __( 'Register', $this->plugin_slug); ?></a></p>
<p><?php echo __('If you have lost your password, click here:', $this->plugin_slug ); ?> <a href="<?php echo wp_lostpassword_url(); ?>" title="Lost Password"><?php echo __( 'Lost Password', $this->plugin_slug); ?></a>
</p>
<div class="cb-login cb-box">

   <?php wp_login_form( ); ?>
 </div>