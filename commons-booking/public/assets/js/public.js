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

          var allDates = calEl.find('li');

          var errors = [];


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

              var indexes = update_selected();
              var msgErrors = errors;

              // console.log (errors);
              if( errors.length > 0 ) {     
                  this.selectonic("cancel"); // cancel selection
                  for (var i = msgErrors.length - 1; i >= 0; i--) {
                    displayErrorNotice( text_errors[ msgErrors[i] ] );
                  }

              } else {
                  update_neighbours( indexes );
                  update_bookingbar( indexes );
              }

            },
            unselectAll: function(event, ui) {
              // …and disable actions buttons
            }
          });
          
          var selected; 
          var allDates = calEl.find('li.bookable');
          var parentCalID = '';
          var overbookable = true;


          function update_selected() {

            errors = [];
            var indexes = [];

            selected = calEl.find('li.selected'); // find selected elements
            var selectedCount = selected.length;

            parentCalID = $(selected).parents('.cb-timeframe').attr('id');

            // VALIDATION - Timeframe
            // check if selection spans more than one timeframe 
            if ( $(selected).parents('.cb-timeframe').length > 1 ) {
              errors.push ("text_error_timeframes");
            }

            // VALIDATION - Day Count
            // Check if selection is more than 3 days
            if ( selectedCount > 3) {
              errors.push ("maxDays");
            }

            // var indexes = [];
            var sequential = [];

            selected.each(function ( index, element ) {
               // console.log( $(element).attr('id'));
               indexes.push ( calEl.find(element).index( 'li.bookable') );
               console.log ( calEl.find(element).index( 'li.bookable') );
            } );

            var selectedIndexesTest = [];
            var selectedIndexesTest = calEl.find('li.selected').index( 'li.bookable');
            // console.log(selectedIndexesTest);

            // VALIDATION - Overbookable days
            // see function
            sequential = checkSequential( indexes );

            // console.log(indexes);

            
            return indexes;

          } // end update_selected()


            // check if selection is sequential
            // return neighbours, 
            function checkSequential( indexes ) {
              var counter = 0;
              var low = indexes[0];
              var high = indexes[indexes.length-1];
              var betweenIndexes = [];

              // loop through days
              for (var i = low; i < high; i++) {
                if ( ( low + counter != indexes[counter] ) ) { // date is not in indexes, check if over-bookable
                  if ( overbookable === true ) { // booking over closed days is enabled
                    var el = $( allDates ).get( low + counter );
                    var isClosed = $(el).hasClass('closed');
                    var isSelected = $(el).hasClass('selected');
                    if ( ( isClosed)  ||  ( isSelected ) ) { // el in between is closed or selected
                        betweenIndexes.push( low + counter );          
                      }  else { // el in between is NOT a closed or selected day, abort selection with error
                        errors.push ("sequential");
                        return false;  
                      }
                  } else { // booking over closed days is NOT allowed
                    errors.push ("closedforbidden");
                    return false;
                  }
                }           
                counter++;   
              }
              return true;  
            }

            // highlight elements available for selection
            function update_neighbours( indexes ) {

              // remove Classnames 
              $(allDates).each(function () {
                 $(this).removeClass('selectable-happy');
              } );

            if ( indexes.length < maxDays) {

              var low = indexes[0] -1;
              if ( low < 0 ) { low = 0 }
              var high = indexes[indexes.length-1] + 1;

              var nextEl = $(allDates).eq(high);

              if ( nextEl.hasClass('bookable') &&  ! ( nextEl.hasClass('selected' ))) {
                nextEl.addClass('selectable-happy');
                } else if ( nextEl.hasClass('closed') ) {
                  nextEl.nextUntil('.bookable').last().next().addClass('selectable-happy');
                }
              var prevEl = $(allDates).eq(low);
              if ( prevEl.hasClass('bookable') &&  ! ( prevEl.hasClass('selected' ))) {
                prevEl.addClass('selectable-happy');
                } else if ( prevEl.hasClass('booked') ) {

                } else if ( prevEl.hasClass('closed') ) {
                  prevEl.prevUntil('.bookable').last().prev().addClass('selectable-happy');
                }
              } 
            }

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
            var timeframe = allDates.eq(indexes[0]).parents('.cb-timeframe');
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

            var pickupDate = allDates.get([ pickupIndex ]);
            var returnDate = allDates.get([ returnIndex ]);

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

          function updateData ( ds ) {
            dataContainer.data( "ds", ds );
            dataContainer.data( "de", de );

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
    
    // Place your public-facing JavaScript here

  });

}(jQuery));