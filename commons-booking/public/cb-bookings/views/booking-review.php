<?php // ITEM  ?>
<div class="cb-headline"><?php echo  __( ' Your Booking: ' ) . $this->b_vars['item_name']; ?></div>
  <div class="cb-booking-item cb-box">
  <div class="img" style="float:right;"><?php echo $this->b_vars['item_thumb']; ?></div>
  <div class=""><?php echo $this->b_vars['item_content']; ?></div>
</div>

<?php // REVIEW ?>
<div class="cb-headline"><?php echo __( ' Pickup & Return ' ); ?></div>
<div class="cb-booking-review cb-box">
  <div>
    <?php echo __( ' Pickup at: ' ); ?><strong><?php echo $this->b_vars['location_name']; ?></strong>
  </div>
  <div>
    <?php echo __( ' Pickup date:' ) ?> <span class="cb-date"><?php echo $this->b_vars['date_start'] ?></span>
  </div>
  <div>
    <?php echo __( ' Return date: ' ) ?><span class="cb-date"><?php echo $this->b_vars['date_end']; ?></span>
  </div>
</div>

<?php //USER ?>
<div class="cb-headline"><?php echo __( ' Your information ' ); ?></div>
<div class="cb-booking-user cb-box">
  <div><?php echo __( ' Full name: ' ); ?><strong><?php echo $this->b_vars['user_name']; ?></strong></div>
  <div><?php echo __( ' Email: ' ) ?><strong><?php echo $this->b_vars['user_email']; ?></strong></div>
  <div><?php echo __( ' Address: ' ) ?><strong><?php echo $this->b_vars['user_address']; ?></strong></div>
  <div><?php echo __( ' Phone number: ' ) ?><strong><?php echo $this->b_vars['user_phone']; ?></strong></div>
</div>

<?php //LOCATION ?>
<div class="cb-headline"><?php echo  __( ' Location information: ' ); ?><?php echo $this->b_vars['location_adress']; ?></div>
<div class="cb-booking-location cb-box">
  <div class="img" style="float:right;">
    <?php echo $this->b_vars['location_thumb'];  ?>
  </div>
  <div class="cb-adress">
    <?php echo $this->b_vars['location_adress']; ?>
  </div>
  <div class="cb-contactinfo">
    <?php echo $this->b_vars['location_contact']; ?>
  </div>
</div>