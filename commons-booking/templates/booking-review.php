<?php 
                 echo '<p class="cb-big"><strong>'. get_the_title($this->item_id ) . ' </strong> '.  __( ' - Your booking information' ) . '</p>';
                 echo '<p class="cb-small">' .  __( ' Pickup at:' ) .' <strong>'. get_the_title($this->location_id ) . ' </strong></p>';
                 echo '<p class="cb-small">' .  __( ' Pickup date:' ) .' <span class="date">'. $this->nice_date_start . ' </span></p>';
                 echo '<p class="cb-small">' .  __( ' Return date:' ) .' <span class="date">'. $this->nice_date_end . ' </span></p>';

?>