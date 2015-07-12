/*
* Date picker for Timeframes edit screen
*
*/


(function($) {
  "use strict";

  $(function() {

    $( '.cb-datepicker' ).each(function( ) { 
      $(this).datepicker( { dateFormat: "yy-mm-dd" } );
      console.log (this);
    
    });
  });

}(jQuery));