(function($) {
  "use strict";

  $(function() {

    /* ========================================================================
     * DOM-based Routing
     * Based on http://goo.gl/EUTi53 by Paul Irish
     *
     * Only fires on body classes that match. If a body class contains a dash,
     * replace the dash with an underscore when adding it to the object below.
     *
     * .noConflict()
     * The routing is enclosed within an anonymous function so that you can
     * always reference jQuery with $, even when in .noConflict() mode.
     * ======================================================================== */

    // Use this variable to set up the common and page specific functions. If you
    // rename this variable, you will also need to rename the namespace below.
    var Commons_Booking = {

      // All pages
      common: {
        init: function() {
          // JavaScript to be fired on all pages
        }
      },
      // Script for booking, fired on single cb_items
      single_cb_items: {

        init: function() {

          // js vars from php
          var maxDays = cb_js_vars.setting_maxdays;
          var text_start_booking = cb_js_vars.text_start_booking;
          var text_return = cb_js_vars.text_return;
          var text_pickup = cb_js_vars.text_pickup;
          var text_pickupreturn = cb_js_vars.text_pickupreturn;
          var text_choose = cb_js_vars.text_choose;
          var text_error_days = cb_js_vars.text_error_days;
          var text_error_timeframes = cb_js_vars.text_error_timeframes;

          var selectedIndexes = [];
          var selectedDates = [];
          var currentTimeFrame;
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );

          var wrapper = $( '.cb-timeframe' );
          var calEl = $( '.cb-calendar li' );
          var msgEl = $( '#cb-bookingbar-msg' );

          var high;
          var low;
          var sortArray = [];
          var arrayToSort;
          var minMax = 0;

          // set starting text
          startContainer.html ( text_choose );
          endContainer.html ( '' );


          $('.tooltip').tooltipster({
            animation: 'grow',
            delay: 0,
            theme: 'tooltipster-default',
            touchDevices: false,
          });

        wrapper.each( function( ) {
          $( 'li', this).on( "click", function( index ) {
              update ( $( this ).index(), $( this ).parents( '.cb_timeframe_form' ).attr( 'id' ) );
          });
        });

        function update( index, id ) {


          var clickedIndexes = [];

          var needle = $.inArray( index, selectedIndexes ); // look for index in array. 
          var clickedIndexes = selectedIndexes.concat();

          // De-Selection
          if ( needle > -1 )  { // already selected, so de-select
            clickedIndexes.splice( needle, 1 );              
          } else {        
            if (selectedIndexes.length > 1 ) { // 2 selected, so exchange first item with it
              clickedIndexes[0] = index;   
            } else {
               clickedIndexes.push ( index );          
            }
          }

          if ( selectedIndexes.length > 0 && currentTimeFrame != id ) {
            displayNotice (text_error_timeframes,  "error");
            return false;
          } else {
             currentTimeFrame = id;
          }

          // Valdiation 
          var distance = 0;
          if ( clickedIndexes.length > 1 ) {
             var distance = clickedIndexes.reduce(function(a, b) {
                return Math.abs( a - b );
              });
          }
          // check if distance smaller than 
          if ( ( distance < maxDays ) ) {  
            selectedIndexes = clickedIndexes;  
          } else {
            displayNotice (text_error_days + maxDays, "error");
          }

          setSelected( selectedIndexes, id );

          }

        function displayNotice ( msg, theclass) {
          msgEl.html( msg );
          msgEl.show();
          msgEl.attr( 'class', theclass );
          msgEl.delay(3000).fadeOut();

        }

      
        function setSelected( selected, id ) {
          // console.log ("indexes:" + i);

          var tf_id = id;
          var indexes = selected.concat();
          var start;
          var end;

          var indexes = selected.sort(function(a,b){return a - b});


          $( '#'+tf_id+' li' ).each(function( myindex ) {

            if ( $.inArray( myindex, indexes )  > -1 )  {
              $( this ).addClass(' selected ');
            } else {
              $( this ).removeClass(' selected ');
          }
          });   

          // start = text_pickup + $( ".cb-calendar li" ).get([ indexes[0] ]).innerHTML;
          if ( indexes.length == 0 ) {
            start = text_choose;
            end = "";
          } else if ( indexes.length == 1 ) { 
            start = text_pickupreturn + $( '#'+tf_id+' li' ).get([ indexes[0] ]).innerHTML;
            end = "";
          } else {
            start = text_pickup + $( '#'+tf_id+' li' ).get([ indexes[0] ]).innerHTML;
            end = text_return + $( '#'+tf_id+' li' ).get([ indexes[1] ]).innerHTML;
          }

          startContainer.html ( start );
          endContainer.html ( end );  
        }

        }
      }
    };





    // The routing fires all common scripts, followed by the page specific scripts.
    // Add additional events for more control over timing e.g. a finalize event
    var UTIL = {
      fire: function(func, funcname, args) {
        var namespace = Commons_Booking;
        funcname = (funcname === undefined) ? 'init' : funcname;
        if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
          namespace[func][funcname](args);
        }
      },
      loadEvents: function() {
        UTIL.fire('common');

        $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
          UTIL.fire(classnm);
        });
      }
    };

    $(document).ready(UTIL.loadEvents);

    // Write in console log the PHP value passed in enqueue_js_vars in public/class-commons-booking.php
    console.log( cb_js_vars.text_pickup );
    
    // Place your public-facing JavaScript here

  });

}(jQuery));