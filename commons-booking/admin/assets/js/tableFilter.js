(function($) {
  "use strict";

  $(function() {

    // Filters for Admin Table

    $( 'div.tablefilters select' ).each(function( ) {

      $( this ).change(function(){
        var filterID = $(this).val();
        if( filterID != '' ){
          document.location.href = 'admin.php?page=timeframes'+filterID;    
        } else {
          document.location.href = 'admin.php?page=timeframes';    
        }
      });    
    });
  });

}(jQuery));