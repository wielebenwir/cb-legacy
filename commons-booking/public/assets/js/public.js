(function($) {
  "use strict";

  $(function() {

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

          /*
          * Item description toggle functionality 
          */

          var togglebutton = $( '.cb-toggle' );
          var toggleEl =  $( '.showhide' );
          toggleEl.hide();

          togglebutton.click( function( event ) {
            event.preventDefault();
            toggleEl.slideToggle( "slow", function() {
                // Animation complete.
            });
          });

          /*
          * Booking Calendar functionality 
          */

          // js vars from php
          var maxDays = cb_js_vars.setting_maxdays;
          var allowclosed = cb_js_vars.setting_allowclosed;
          var booking_review_page = cb_js_vars.setting_booking_review_page;
          var text_start_booking = cb_js_vars.text_start_booking;
          var text_return = cb_js_vars.text_return;
          var text_pickup = cb_js_vars.text_pickup;
          var text_pickupreturn = cb_js_vars.text_pickupreturn;
          var text_choose = cb_js_vars.text_choose;

          var text_errors = {
            'maxDays': cb_js_vars.text_error_days + maxDays,
            'timeframes': cb_js_vars.text_error_timeframes,
            'closedforbidden': cb_js_vars.text_error_closedforbidden,
            'sequential': cb_js_vars.text_error_sequential
          };

          var text_error_days = cb_js_vars.text_error_days;
          var text_error_timeframes = cb_js_vars.text_error_timeframes;
          var text_error_notbookable = cb_js_vars.text_error_notbookable;
          var text_error_closedforbidden = cb_js_vars.text_error_closedforbidden;
          var text_error_bookedday = cb_js_vars.text_error_bookedday;

          var selectedIndexes = [];
          var currentTimeFrame;

          // DOM containers
          var debug = $( '#debug' );
          var introContainer = $( '#intro' );
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );
          var bookingButton = $( '#cb-submit .button' );
  
          var dataContainer = $( '#cb-bookingbar #data' ); // @TODO: Retire me

          var wrapper = $( '.cb-timeframe' );
          var calEl = $( '.cb-timeframes-wrapper' );
          var msgEl = $( '#cb-bookingbar-msg' );

          var formEl = $( '#booking-selection');
          var form_date_start = $( 'input[name="date_start"]' ); 
          var form_date_end = $( 'input[name="date_end"]' ); 
          var form_item_id = $( 'input[name="item_id"]' ); 
          var form_location_id = $( 'input[name="location_id"]' ); 
          var form_timeframe_id = $( 'input[name="timeframe_id"]' ); 
          var formButton = $('#cb-submit a');

          var allCalendarDates = calEl.find('li');

          var errors = [];
          var selectedCount;


          // set starting text
          introContainer.html (text_choose);
          startContainer.html ( '' );
          endContainer.html ( '' );
          bookingButton.hide();


          // $('.tooltip').tooltipster({
          //   animation: 'grow',
          //   delay: 0,
          //   theme: 'tooltipster-default',
          //   touchDevices: false,
          // });


          formButton.click(function( event ) {
            event.preventDefault();
            formEl.submit();
          });
              var temp = [];

          calEl.selectonic({
            multi: true,
            mouseMode: "toggle",
            keyboard: false,
            selectedClass: "selected",
            filter: ".bookable",
            select: function(event, ui) {

              // set_neighbours( ui );
              // update_neighbours(ui);
              // temp = calEl.selectonic( 'getSelected');
              // is_continous ( temp );
              // temp.next().css( "background-color", "red" );

              // do something cool, for expample enable actions buttons
            },
            stop: function(event, ui) {

              var selectedIndexes = update_selected();
              var msgErrors = errors;

              // console.log (errors);
              if( errors.length > 0 ) {     
                  this.selectonic("cancel"); // cancel selection
                  for (var i = msgErrors.length - 1; i >= 0; i--) {
                    displayErrorNotice( text_errors[ msgErrors[i] ] );
                  }

              } else {
                  update_neighbours( selectedIndexes, selectedCount );
                  update_bookingbar( selectedIndexes );
              }

            },
            unselectAll: function(event, ui) {
              // …and disable actions buttons
            }
          });
          
          var selected; 
          var allBookableDates = calEl.find('li.bookable');
          var parentCal = '';
          var overbookable = true;


          function update_selected() {

            errors = [];
            var indexes = [];
            var selectedIDs = [];

            selected = calEl.find('li.selected'); // find selected elements
            selectedCount = selected.length;

            parentCal = $(selected).parents('.cb-timeframe');

            // VALIDATION - Timeframe
            // check if selection spans more than one timeframe 
            if ( $(selected).parents('.cb-timeframe').length > 1 ) {
              errors.push ("text_error_timeframes");
            }

            // var indexes = [];
            var betweenDays = [];
            var calIndexes = []; 

            // add selected indexes to array
            selected.each(function ( index, element ) {
               indexes.push ( calEl.find(element).index( 'li.bookable') );
               calIndexes.push ( parentCal.find(element).index() );
            } );            

            // check if there are days between the selection
            betweenDays = getDaysBetween( calIndexes );

            if ( betweenDays.length > 0 ) { // there are days between selected
              if (allowclosed == 1) { // booking over closed days is allowed, so check the days´ classes
                var d = checkForClass ( betweenDays, 'closed' );
                if ( d ) { // all days between are closed
                  selectedCount++; // booking over closed days, which count as one day           
                } else { // at least one day between is not closd
                   errors.push ("sequential");                
                }
              } else { //booking over closed days not allowed, so return error
                errors.push ("sequential");
              }
            }

            // VALIDATION - Day Count
            // Check if selection is more than max days
            if ( selectedCount > maxDays ) {
              errors.push ("maxDays");
            }

            return indexes;

          } // end update_selected()


          function checkForClass( els, myClass ) {

            var count = 0;
            $(els).each( function ( ) {
              $(this).css('border', '1px solid red');
              if ( $(this).hasClass( myClass )) {
                console.log ("has class");
                count++;
              } else {
                console.log ("has not class");
                return false;
              }
            });
              return count;
          }

          function getNeighbours ( indexes ) {
              var array = [];
              var low = indexes[0] - 1;
              var high = indexes[indexes.length];
              return array [low, high];          
          } 

          // check if there are non-selected days between selection
          function getDaysBetween( calIndexes ) {
              var counter = 0;
              var low = calIndexes[0];
              var high = calIndexes[calIndexes.length-1];
              var daysBetween = [];

              // loop through days
              for (var i = low; i < high; i++) { 
                if ( ( low + counter != calIndexes[counter] ) ) { // date is not in indexes
                  // daysBetween.push( calEl.eq(low + counter) );
                  daysBetween.push( parentCal.find('li').eq(low + counter) );
                }
                counter++;
              }
              return daysBetween;    
          }

            // highlight elements available for selection
            function update_neighbours( indexes, selectedCount ) {

              // remove Classnames 
              $(allCalendarDates).each(function () {
                 $(this).removeClass('selectable-happy');
              } );

            if ( selectedCount < maxDays ) {

              var currentHighEl = $(allBookableDates).eq(indexes[indexes.length-1]);
              var currentLowEl = $(allBookableDates).eq(indexes[0]);
              var nextEl = $(allBookableDates).eq( indexes[indexes.length-1] + 1 );
              var prevEl = $(allBookableDates).eq( indexes[0] -1 );
              var leftOverDays = maxDays - (selectedCount + 1);

              var between = [];

              var nextNeighbours = checkBetween(  currentHighEl, nextEl );
              var prevNeighbours = checkBetween(  prevEl, currentLowEl  );
              } 
            }

          function checkBetween ( startEl, endEl ) {
              var start;
              var end;
              start = startEl;
              end = endEl;

              if( startEl.parents( '.cb_timeframe_form').attr('id') != end.parents( '.cb_timeframe_form').attr('id')) {              
                return false;
              } 
              var between = start.nextUntil( end, 'li' );
              for (var i = 0; i < between.length; i ++) {
                if ( $(between[i]).hasClass('booked') ) {
                    return false;
                  } else {
                    $(between[i]).addClass('selectable-happy');
                  }
                }
                  start.addClass('selectable-happy');
                  end.addClass('selectable-happy');
                return i;
              };

          // show error notice
          function displayErrorNotice ( msg ) {
            msgEl.html( msg );
            msgEl.show();
            msgEl.attr( 'class', 'error' );
            msgEl.delay(3000).fadeOut();
          }


          function setDataContainer( start, end, meta ) {

              form_date_start.val( start );
              form_date_end.val( end );
              form_timeframe_id.val( meta['timeframe'] );
              form_item_id.val( meta['item'] );
              form_location_id.val( meta['location'] ); 

          }

          // return the timeframe-id, location-id & item-id of the timeframe containing the currently selected days  
          function getCurrentTimeframeMeta( indexes ) {
            var timeframe = allBookableDates.eq(indexes[0]).parents('.cb-timeframe');
            var tf_id = timeframe.attr('id');
            var item_id = timeframe.data('itemid');
            var loc_id = timeframe.data('locid');
            var meta = {  
              timeframe:  tf_id, 
              item:       item_id, 
              location:   loc_id
            };
            return meta; 
          }


          function bookingbar_set_text( target, content ) {

            if (content) {
              target.html(content);
              target.fadeIn('slow');
            } else {
              target.fadeOut('fast');      
            }
          }

          // set the selection & texts 
          function update_bookingbar( indexes ) {

            var tf_meta = getCurrentTimeframeMeta( indexes );
            var textFirst = '';
            var textSecond = '';

            var pickupIndex = indexes[0];
            var returnIndex = indexes[ indexes.length -1 ];

            var pickupDate = allBookableDates.get([ pickupIndex ]);
            var returnDate = allBookableDates.get([ returnIndex ]);

            if ( indexes.length > 0 ) { // there is a selection, show dates in bar

              var dateStartID = $(pickupDate).attr('id');
              var dateEndID = $(returnDate).attr('id');

              introContainer.hide();

              if ( pickupDate == returnDate ) { // one day selected

                textFirst = text_pickupreturn + '<div class="bb-date">' + pickupDate.innerHTML + '</div>'; // set Text
                textSecond = '';

              } else { // at least two days selected

                textFirst = text_pickup + '<div class="bb-date">' + pickupDate.innerHTML + '</div>'; // set Text
                textSecond = text_return + '<div class="bb-date">' + returnDate.innerHTML + '</div>';

              }

              setDataContainer( dateStartID, dateEndID, tf_meta ); // update the form
              bookingButton.fadeIn(800); // we show the booking button

            } else { // no selection, reset the text to standard

              introContainer.fadeIn ('fast');
              bookingButton.fadeOut(200); 

            }
              bookingbar_set_text( startContainer, textFirst );
              bookingbar_set_text( endContainer, textSecond );

            // var ready = 1;
            // startContainer.fadeOut(200);
            // startContainer.html ( textFirst );
            // startContainer.fadeIn(500);

            // endContainer.fadeOut(200);
            // endContainer.html ( textSecond );  
            // endContainer.fadeIn(500);


            // if ( ready == 1 ) {


            //   // dataContainer.data( "tf_id", targetli.parents('.cb-timeframe').data('tfid') );  // get data from DOM data- attribute
            //   // dataContainer.data( "item_id", targetli.parents('.cb-timeframe').data('itemid') );  // write to Container
            //   // dataContainer.data( "location_id", targetli.parents('.cb-timeframe').data('locid') );  // write to Container

            //   // set inputs
              
            // } else {
            //   bookingButton.fadeOut(300);           
            // }

          } // setselected


          // helper: create range array 
          function range( start, end ){ 
            start = start || 1; return end >= start ? range(start,end-1).concat(end) : []; 
          }

          function updateData ( ds ) {
            dataContainer.data( "ds", ds );
            dataContainer.data( "de", de );

          }

          function submitForm() {
            $( "#target" ).submit();
          }


          
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
    
    // Place your public-facing JavaScript here

  });

}(jQuery));