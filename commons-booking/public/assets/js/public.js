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
          var allowclosed = cb_js_vars.setting_allowclosed;
          var booking_review_page = cb_js_vars.setting_booking_review_page;
          var text_start_booking = cb_js_vars.text_start_booking;
          var text_return = cb_js_vars.text_return;
          var text_pickup = cb_js_vars.text_pickup;
          var text_pickupreturn = cb_js_vars.text_pickupreturn;
          var text_choose = cb_js_vars.text_choose;
          var text_error_days = cb_js_vars.text_error_days;
          var text_error_timeframes = cb_js_vars.text_error_timeframes;
          var text_error_notbookable = cb_js_vars.text_error_notbookable;
          var text_error_closedforbidden = cb_js_vars.text_error_closedforbidden;
          var text_error_bookedday = cb_js_vars.text_error_bookedday;

          var selectedIndexes = [];
          var currentTimeFrame;

          // DOM containers
          var debug = $( '#debug' );
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );
          var bookingButton = $( '#cb-submit .button' );
  
          var dataContainer = $( '#cb-bookingbar #data' );

          var wrapper = $( '.cb-timeframe' );
          var calEl = $( '.cb-calendar li' );
          var msgEl = $( '#cb-bookingbar-msg' );

          var formEl = $( '#booking-selection');
          var form_date_start = $( 'input[name="date_start"]' ); 
          var form_date_end = $( 'input[name="date_end"]' ); 
          var form_item_id = $( 'input[name="item_id"]' ); 
          var form_location_id = $( 'input[name="location_id"]' ); 
          var form_timeframe_id = $( 'input[name="timeframe_id"]' ); 
          var formButton = $('#cb-submit a');

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

          formButton.click(function() {
            event.preventDefault();
            formEl.submit();
          });

        wrapper.each( function( ) {
          $( 'li', this).on( "click", function( index ) {
              update ( $( this ).index(), $( this ).parents( '.cb_timeframe_form' ).attr( 'id' ) );
          });
        });

        function update( index, tf_id ) {

          console.log (booking_review_page);

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


          // Calculate Distance & get the classes of the days in between
          var distance = 0;
          var daystatus = 0;

          if ( clickedIndexes.length > 1 ) { // if more than one clicked 
            var firstEl = $( '#'+tf_id+' li').eq( clickedIndexes[0] ).attr('id');
            var secondEl = $( '#'+tf_id+' li').eq( clickedIndexes[1] ).attr('id');
            
            var daysBetween = getDaysBetween (firstEl, secondEl);
            daystatus = getDayStatus ( daysBetween );            
            distance = daysBetween.length -1;

          }

          // VALIDATION
          if ( selectedIndexes.length > 0 && currentTimeFrame != tf_id ) { // within current timeframe
              displayNotice (text_error_timeframes,  "error");
              return false;
          } else if ( !$( '#'+tf_id+' li').eq( index ).hasClass('bookable') ) { // day selected is bookable
              displayNotice (text_error_notbookable,  "error");
              return false;       
          } else if ( distance >= maxDays ) { // max days is not exceeded
              displayNotice (text_error_days + maxDays, "error");
              return false;              
          } else if ( daystatus < 0 ) { // between pickup date and return date is a booked date
              displayNotice ( text_error_bookedday, "error");
              return false;            
          } else if ( ( daystatus > 0 ) && ( allowclosed != "on" ) ) { // booking over closed days, but not allowed
              displayNotice ( text_error_closedforbidden , "error");
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
            var textFirst;
            var textSecond;

            var ready = 0;

            var indexes = selected.sort(function(a,b){return a - b});
            var targetli = $( '#'+tf_id+' li' );

            targetli.each(function( myindex ) {
              if ( $.inArray( myindex, indexes )  > -1 )  {
                $( this ).addClass(' selected ');
              } else {
                $( this ).removeClass(' selected ');
            }
            });   


            if ( indexes.length == 1  ) { // 1 selected -> pickup & return same day 
              
              textFirst = text_pickupreturn + targetli.get([ indexes[0] ]).innerHTML; // set Text
              textSecond = '';

              dataContainer.data( "ds", targetli.eq([ indexes[0] ]).attr('id') ); // write to Container
              dataContainer.data( "de", targetli.eq([ indexes[0] ]).attr('id') ); 
            
              ready = 1;

            } else if ( indexes.length == 2 ) { // 2 selected -> pickup & return different days 

              textFirst = text_pickup + targetli.get([ indexes[0] ]).innerHTML; // set Text
              textSecond = text_return + targetli.get([ indexes[1] ]).innerHTML;

              dataContainer.data( "ds", targetli.eq([ indexes[0] ]).attr('id') );  // write to Container
              dataContainer.data( "de", targetli.eq([ indexes[1] ]).attr('id') ); 

              ready = 1;

            } else { // None selected or error

              form_date_start.val(''); // clear start & end input values
              form_date_end.val('');  
              
              textFirst = text_choose;    // set texts
              textSecond = "";

              ready = 0;
            } 

            startContainer.html ( textFirst );
            endContainer.html ( textSecond );  

            if ( ready == 1 ) {


              dataContainer.data( "tf_id", targetli.parents('.cb-timeframe').data('tfid') );  // get data from DOM data- attribute
              dataContainer.data( "item_id", targetli.parents('.cb-timeframe').data('itemid') );  // write to Container
              dataContainer.data( "location_id", targetli.parents('.cb-timeframe').data('locid') );  // write to Container


              // set inputs
              form_date_start.val( dataContainer.data ("ds") );
              form_date_end.val( dataContainer.data ("de") );

              form_timeframe_id.val( dataContainer.data ("tf_id") );
              form_item_id.val( dataContainer.data ("item_id") );
              form_location_id.val( dataContainer.data ("location_id") );      

              bookingButton.show(); 
            } else {
              bookingButton.hide();           
            }

          } // setselected

          function updateData ( ds ) {
            dataContainer.data( "ds", ds )
            dataContainer.data( "de", de )
            debug.text( dataContainer.data( "go" ) );

          }

          function setData () {

          }

          function submitForm() {
            $( "#target" ).submit();
          }

          /* 
           * Gets the days between two timestamps
           * @param   startdate, endDate: timestamps
           * @return  array (timestamps)
           */
          function getDaysBetween(startdate, endDate) { 

            var dates = [startdate, endDate];
            dates.sort();

            // convert timestamps to date js object
            var start = new Date( dates[0] * 1000 ),
                end = new Date ( dates[1]  * 1000 ),
                currentDate = start,
                between = []
            ;

            while (currentDate <= end) {
                var temp = new Date(currentDate);
                between.push( temp.getTime()/1000 ); // convert back to timestamp and push into array
                currentDate.setDate(currentDate.getDate() + 1);
            }
            return between;
          } // getDaysBetween
          
          /* 
           * Gets the relevant classes 
           * @param   array ids 
           * @return  int closed days count 
           */
          function getDayStatus( ids ) {
            var closed = 0;
            for (var i = ids.length - 1; i >= 0; i--) {
              if ( $( 'li#'+ids[i]).hasClass( 'booked' ) ) {
                return -1;
              } else if ( $( 'li#'+ids[i]).hasClass( 'closed' ) ) {
                closed++;
              }
            };
            return closed;
          } // getDaysBetween

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