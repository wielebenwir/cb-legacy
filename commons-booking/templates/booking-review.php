<?php 
  if (is_user_logged_in() ) {

   if ( !empty($_REQUEST['create']) && $_REQUEST['create'] == 1) { // we create a new booking
      echo ("create");

       if ( !empty($_REQUEST['date_start']) && !empty($_REQUEST['date_end']) && !empty($_REQUEST['timeframe_id']) && !empty($_REQUEST['item_id']) && !empty($_REQUEST['location_id']) && !empty($_REQUEST['_wpnonce']) ) { // all needed vars available

          if (! wp_verify_nonce($_REQUEST['_wpnonce'], 'booking-review-nonce') ) die("Security check");

          echo ("date_start: ". $_REQUEST['date_start'] );
          echo ("date_end: ". $_REQUEST['date_start'] );  
          echo ("date_end: ". $_REQUEST['date_start'] );  
          echo ("location_id: ". $_REQUEST['location_id'] );  
          echo ("item_id: ". $_REQUEST['item_id'] );  
          echo ("timeframe_id: ". $_REQUEST['timeframe_id'] );  

      } else { // not all needed vars available 
        echo "Error";
        die();
      }
    } // end if create 
  

  }else { // not logged in     
    echo "You ahve to be logged in.";
} // end if logged in 

?>