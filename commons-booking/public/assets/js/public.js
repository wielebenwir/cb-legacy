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
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );
          var bookingButton = $( '#cb-submit .button' );
  
          var dataContainer = $( '#cb-bookingbar #data' );

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
          startContainer.html ( text_choose );
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

              var msgErrors = update_selected();
              console.log (errors.length);
              if( errors.length > 0 ) {     
                  this.selectonic("cancel"); // cancel selection
                  for (var i = msgErrors.length - 1; i >= 0; i--) {
                    displayErrorNotice( text_errors[ msgErrors[i] ] );
                    // console.log( text_errors[ errors[i] ]);
                  }

              }

            },
            unselectAll: function(event, ui) {
              // …and disable actions buttons
            }
          });
          
          var selected; 
          var allDates = calEl.find('li');
          var parentCal = [];
            // console.log (sequential);
          var overbookable = true;


          function update_selected() {

            errors = [];

            selected = calEl.find('li.selected'); // find selected elements
            
            // VALIDATION
            // check if selection spans more than one timeframe 
            if ( $(selected).parents('.cb-timeframe').length > 1 ) {
              errors.push ("text_error_timeframes");
            }

            // Check if selection is more than 3 days
            if ( selected.length > 3) {
              errors.push ("maxDays");
            }

            var indexes = [];
            var sequential = [];

            $(selected).each(function () {
               indexes.push ( $(this).index());
            } );

            sequential = checkSequential( indexes );

            // console.log (els);
            allDates.each(function () {
              // console.log ($(allDates).index(this));              
            } );
            console.log(errors);

            return errors;

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


          function sort_by_index( els ) {
            els.sort(function(a,b){
                return parseInt(a.index) > parseInt(b.index);
            });
          }

          function update_neighbours( el ) {
            var next = $(el.target).next();
            var prev = $(el.target).prev();

            validate_el( next );
            validate_el( prev );

          }

          function validate_el( el ) {
            if ( ! el.hasClass('selected')) {
              el.addClass( 'selectable-glow' );
            }
          }

          function set_neighbours( el ) {
            // temp = calEl.selectonic("getSelected");

            // console.log (temp.length);
            $(el).nextAll().slice(0,4).css( "background-color", "red" );
            $(el).prevAll().slice(0,4).css( "background-color", "green" );    
          }

          function validatebefore( el ) {
            var tempList = [];
            tempList = calEl.selectonic( 'getSelected');
            // console.log(tempList.length);
            console.log ( el.target );
            if ( tempList.length > 6 ) {
              return false;
            } else {
              return true;
            }

          }

        function get_selectable_elements( selection ) {

        }

        function is_continous( els ) {
          var sorted = sort_by_id( els );
          console.log (els);
          console.log (sorted );

          // console.log ( els ); 
  
        }

        function sort_by_id ( els ) {
            els.sort(function(a,b){
              return parseInt(a.id) > parseInt(b.id);
          });
        }

        // wrapper.each( function( ) {
        //   $( 'li', this).on( "click", function( index ) {
        //       update ( $( this ).index(), $( this ).parents( '.cb_timeframe_form' ).attr( 'id' ) );
        //   });
        // });

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

          // if setting allowclosed is set, add the closed days to the max days to allow booking           
          var theDays;
          if ( allowclosed == 1 ) {
            theDays = maxDays + daystatus;
          } else {
            theDays = maxDays;
          }

          // VALIDATION
          if ( selectedIndexes.length > 0 && currentTimeFrame != tf_id ) { // within current timeframe
              displayNotice (text_error_timeframes,  "error");
              return false;
          } else if ( !$( '#'+tf_id+' li').eq( index ).hasClass('bookable') ) { // day selected is bookable
              displayNotice (text_error_notbookable,  "error");
              return false;       
          } else if ( distance >= theDays ) { // max days is not exceeded
              displayNotice (text_error_days + maxDays, "error");
              return false;              
          } else if ( daystatus < 0 ) { // between pickup date and return date is a booked date
              displayNotice ( text_error_bookedday, "error");
              return false;            
          } else if ( ( daystatus > 0 ) && ( allowclosed = 0 ) ) { // booking over closed days, but not allowed
              displayNotice ( text_error_closedforbidden , "error");
              return false;       
          } else { // no errors
            selectedIndexes = clickedIndexes;  
            currentTimeFrame = tf_id;     
          }

          // set selected, show Booking Button
          setSelected( selectedIndexes, tf_id );

          }

          // show error notice
          function displayErrorNotice ( msg ) {
            msgEl.html( msg );
            msgEl.show();
            msgEl.attr( 'class', 'error' );
            msgEl.delay(3000).fadeOut();
          }


          // show notices
          function displayNotice ( msg, theclass) {
            msgEl.html( msg );
            msgEl.slideDown();
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

            startContainer.fadeOut(200);
            startContainer.html ( textFirst );
            startContainer.fadeIn(500);

            endContainer.fadeOut(200);
            endContainer.html ( textSecond );  
            endContainer.fadeIn(500);


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

              bookingButton.fadeIn(800); 
            } else {
              bookingButton.fadeOut(300);           
            }

          } // setselected

          function updateData ( ds ) {
            dataContainer.data( "ds", ds );
            dataContainer.data( "de", de );

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
    
    // Place your public-facing JavaScript here

  });

}(jQuery));