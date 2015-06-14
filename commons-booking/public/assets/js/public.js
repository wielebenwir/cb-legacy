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
          var text_error_notbookable = cb_js_vars.text_error_notbookable;

          var selectedIndexes = [];
          var selectedDates = [];
          var currentTimeFrame;

          // DOM containers
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );
          var bookingButton = $( '#cb-submit .button' );

          var wrapper = $( '.cb-timeframe' );
          var calEl = $( '.cb-calendar li' );
          var msgEl = $( '#cb-bookingbar-msg' );

          var form_date_start = $( 'input[name="date_start"]' ); 
          var form_date_end = $( 'input[name="date_end"]' ); 

          // set starting text
          startContainer.html ( text_choose );
          endContainer.html ( '' );
          bookingButton.hide();


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

        function update( index, tf_id ) {

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

          // Calculate Distance 
          var distance = 0;
          if ( clickedIndexes.length > 1 ) {
             var distance = clickedIndexes.reduce(function(a, b) {
                return Math.abs( a - b );
              });
          }


          // VALIDATION
          if ( selectedIndexes.length > 0 && currentTimeFrame != tf_id ) { // within current timeframe
              displayNotice (text_error_timeframes,  "error");
              return false;
          } else if ( !$( '#'+tf_id+' li').eq( index ).hasClass('bookable') ) { // day selected is bookable
              displayNotice (text_error_notbookable,  "error");
              return false;       
          } else if ( (distance >= maxDays )) { // max days is not exceeded
            displayNotice (text_error_days + maxDays, "error");
            return false;       
          } else { // no errors
            selectedIndexes = clickedIndexes;  
            currentTimeFrame = tf_id;     
          }

          // set selected, show Booking Button
          setSelected( selectedIndexes, tf_id );

          }

          // show notices
          function displayNotice ( msg, theclass) {
            msgEl.html( msg );
            msgEl.show();
            msgEl.attr( 'class', theclass );
            msgEl.delay(3000).fadeOut();
          }

          // set the selection & texts 
          function setSelected( selected, id ) {

            var tf_id = id;
            var indexes = selected.concat();
            var start;
            var end;

            var indexes = selected.sort(function(a,b){return a - b});

            var targetli = $( '#'+tf_id+' li' );

            targetli.each(function( myindex ) {

              if ( $.inArray( myindex, indexes )  > -1 )  {
                $( this ).addClass(' selected ');
              } else {
                $( this ).removeClass(' selected ');
            }
            });   

            bookingButton.hide();

            if ( indexes.length == 0 ) {
              form_date_start.val(''); // clear start & end input values
              form_date_end.val('');  
              start = text_choose;    // set texts
              end = "";
            } else if ( indexes.length == 1 ) { // 1 selected -> pickup & return same day 
              bookingButton.show();
              start = text_pickupreturn + targetli.get([ indexes[0] ]).innerHTML;
              form_date_start.val( targetli.eq([ indexes[0] ]).attr('id') );
              form_date_end.val( targetli.eq([ indexes[0] ]).attr('id') );
              end = "";
            } else { // 2 selected -> pickup & return different days 
              bookingButton.show();
              start = text_pickup + targetli.get([ indexes[0] ]).innerHTML;
              end = text_return + targetli.get([ indexes[1] ]).innerHTML;
              form_date_start.val( targetli.eq([ indexes[0] ]).attr('id') );
              form_date_end.val( targetli.eq([ indexes[1] ]).attr('id') );
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