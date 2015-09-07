/*
* Date picker for Timeframes edit screen
*
*/

(function($) {
  "use strict";

  $(function() {

    // set German Localization @TODO 
    $.datepicker.setDefaults(
      $.extend($.datepicker.regional['de'])
    );

    $( '.cb-datepicker' ).each(function( ) { 
      $(this).datepicker( { dateFormat: "yy-mm-dd" } );
    
    });
  });

}(jQuery));