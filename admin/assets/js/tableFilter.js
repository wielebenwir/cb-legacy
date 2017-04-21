/*
* Table Filter for Wordpress Backend Tables
*
*/


(function($) {
  "use strict";

  $(function() {

      var page = getUrlParameter('page');

    // Filters for Admin Table

    $( 'div.tablefilters select' ).each(function( ) {

      $( this ).change(function(){
        var filterID = $(this).val();
        if( filterID != '' ){
          document.location.href = 'admin.php?page='+page+filterID;    
        } else {
          document.location.href = 'admin.php?page='+page;    
        }
      });    
    });
  });

}(jQuery));