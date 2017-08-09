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
    
      // Script for hCard map, fired on div.commons-booking-hcard-map in the HTML (with .commons-init)
      commons_booking_hcard_map: {
        
        init: function(mapDIV) {
          var locationsMap, vcards, markers, group, padding;

          // Location info toggle functionality
          var notice_map_click = cb_js_vars.text_notice_map_click; // String from PHP
          console.log(notice_map_click);
          $('<div id="cb-location-popup-container" class="cb-location-wrapper cb-box"></div>').insertAfter(mapDIV); // insert info box div
          var locationInfoTaget = $('#cb-location-popup-container'); // set div as target
          locationInfoTaget.html(notice_map_click); // Set initial string
          var locationInfoOld = '';

          // update the target div with location info
          function updateLocationInfo( locationInfo ) {
            if (locationInfo != locationInfoOld) { // prevent fade if same marker is clicked twice
              locationInfoTaget.fadeOut("slow", function() { // is faded out
                  locationInfoTaget.html( locationInfo ); // exchange info
                  locationInfoTaget.fadeIn("fast"); // fade in
                  locationInfoOld = locationInfo;
              });
            } 
          }

          if (window.L) {
            locationsMap = L.map(mapDIV).setView([51.505, -0.09], 13);
            markers      = [];
            
            // OSM streets
            L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
                maxZoom: 18
            }).addTo(locationsMap);
            
            // Add hCard's
            vcards = $('.vcard');
            if (!vcards.length && window.console) console.warn('no .vcard found for the map');
            vcards.each(function() {
              var marker, Icon, icon;
              var oOptions = {};
              var oIconOptions = {};
              var adr      = $(this).find('.adr:first');
              var lat      = adr.find('.geo .latitude').text();
              var lng      = adr.find('.geo .longitude').text();
              var iconUrl  = adr.find('.geo .icon').text();
              var iconShadowUrl = adr.find('.geo .icon-shadow').text();
              var title    = $(this).find('.fn:first').text();
              var href     = $(this).find('.fn:first').attr("href");
              var desc     = '';
              
              $(this).find('.cb-popup').each(function() {
                desc += $("<div/>").append($(this).clone()).html();
              });
             $(this).hide();
              
              // Warnings
              if (window.console) {
                if (!adr.length) console.warn('.vcard found but has no .adr');
                if (!title)      console.warn('.vcard found but has no .fn title');
                if (!href)       console.warn('.vcard found with .fn but has no @href');
                if (lat === '')  console.warn('.vcard found but has no .adr .geo .latitude');
                if (lng === '')  console.warn('.vcard found but has no .adr .geo .longitude');
              }
                        
              if (lat && lng) {
                // Give some defaults for best chances of working
                if (!title) title = '(no title)';
                if (!href)  href  = '#' + title;  // Should not happen
                         
                if (iconUrl) {
                  oIconOptions = {
                      iconUrl:      iconUrl,
                      title:        title,
                      alt:          title,
                      iconSize:     [48, 48], // size of the icon
                      shadowSize:   [48, 48], // size of the shadow
                      iconAnchor:   [24, 24], // point of the icon which will correspond to marker's location
                      shadowAnchor: [20, 20], // the same for the shadow
                      popupAnchor:  [0, -8]   // point from which the popup should open relative to the iconAnchor
                  };
                  if (iconShadowUrl) oIconOptions.shadowUrl = iconShadowUrl;
                  Icon = L.Icon.extend({options:oIconOptions});
                  oOptions.icon = new Icon();
                }
                
                if (window.console) console.info('adding [' + title + '] at [' + lat + ',' + lng + ']');
                marker = L.marker([lat, lng], oOptions).addTo(locationsMap);
                var locationInfo = '<h2><a href="' + href + '">' + title + '</a></h2>' + desc;
                marker.on('click', function(e) { updateLocationInfo( locationInfo ); } );
                markers.push(marker);
              }
            });

            // Fit to all markers
            if (markers.length) {
              padding = (markers.length == 1 ? 2 : 0.5);
              group   = new L.featureGroup(markers);
              locationsMap.fitBounds(group.getBounds().pad(padding));
              if (window.console) console.log(group.getBounds());
            }
            
            if (window.console) console.log(locationsMap);
          } else if (window.console) console.error('Leaflet library not loaded');
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
          var closed_days_count_as = parseInt ( cb_js_vars.setting_closed_days_count );
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
          var bookingBar = $( '#cb-bookingbar' );
          var debug = $( '#debug' );
          var introContainer = $( '#intro' );
          var startContainer = $( '#date-start' );
          var endContainer = $( '#date-end' );
          var bookingButton = $( '#cb-submit .cb-button' );
  
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
          var overbookDays = [];
          var betweenDays = [];
          var allBookableDates = calEl.find('li.bookable');

          // check if timeframe  element exist on the site
          if ( $( '.cb-timeframes-wrapper' ).length ) {

            // START: set starting text & element status
            introContainer.html (text_choose);
            startContainer.html ( '' );
            endContainer.html ( '' );
            bookingButton.hide();

            // START: tooltipster script
            $('.cb-tooltip').tooltipster({
              animation: 'fade',
              delay: 0,
              theme: 'tooltipster-cb',
            });

            // START: resizes the bookingbar 
            $(window).on('resize', function(){
              resize_bookingbar();
            });
            resize_bookingbar();
          
            // Selection script 
            calEl.selectonic({
              multi: true,
              mouseMode: "toggle",
              keyboard: false,
              selectedClass: "selected",
              filter: ".bookable",
              select: function(event, ui) {

              },
              stop: function(event, ui) {

                var selectedIndexes = update_selected();
                var msgErrors = errors;


                if( errors.length > 0 ) {     
                    this.selectonic("cancel"); // cancel selection
                    for (var i = msgErrors.length - 1; i >= 0; i--) {
                      displayErrorNotice( text_errors[ msgErrors[i] ] );
                    }

                } else {
                  removeClasses();
                  addClasses();
                  update_bookingbar( selectedIndexes );
                  // showToolTips();
                  resize_bookingbar();
                }

              },
              unselectAll: function(event, ui) {
                // …and disable actions buttons
              }
            });

          } else { // timeframe element does not exist
            
            bookingBar.hide();
          
          }
          
          var selected; 
          var parentCal = '';
          var overbookable = true;

          // Submit
          formButton.click(function( event ) {
            event.preventDefault();
            formEl.submit();
          });

          // resize the bookingbar to width of calendar.
          function resize_bookingbar() {

              var parentpos = $('.cb-timeframes-wrapper').offset();

              // .outerWidth() takes into account border and padding.
              var width = $('.cb-timeframes-wrapper').outerWidth();

              $("#cb-bookingbar").css({
                  width: width,
                  'margin-left': (parentpos.left) + "px"
              })
          }


          function update_selected() {

            errors = [];
            overbookDays = [];
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
                var allowedClasses = ['closed', 'selected'];
                overbookDays = checkForClass ( betweenDays, allowedClasses);
                if ( typeof overbookDays != 'undefined' ) { // all days between are closed
                  selectedCount = selectedCount + closed_days_count_as ; // booking over closed days, which count as one day           
                } else { // at least one day between is not closed
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

          // remove all Classes
          function removeClasses() {
            allCalendarDates.each(function() {
              $(this).removeClass('selected-last');
              $(this).removeClass('selected-first');
              $(this).removeClass('overbooking');
            })
          }
          // set classes
          function addClasses() {

            var start = selected.first();
            var end = selected.last();

            start.addClass('selected-first'); // mark first el
            end.addClass('selected-last'); // mark last el

            if ( overbookDays.length > 0 ) {  // mark all days that are overbookable          
              start.nextUntil( end ).addClass('overbooking');
            }
          }

          //check for classes
          function checkForClass( els, classes ) {

            var error = 0;
            var betweenEls = [];

            $(els).each( function ( element, index ) {
              // $(this).css('background','red'); DEBUG only @TODO remove me
              if ( $(this).hasClasses( classes )) {
                betweenEls.push( $(this).index() );          
              } else {
                error++;
              }
            });

            if (error == 0) {
              return betweenEls;
            } 
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
                  daysBetween.push( calEl.find('li').eq(low + counter) );
                }
                counter++;
              }
              return daysBetween;    
          }

          // show error notice
          function displayErrorNotice ( msg ) {
            msgEl.html( msg );
            msgEl.show();
            msgEl.attr( 'class', 'error' );
            msgEl.delay(3000).fadeOut();
          }

          // add dates to the form element
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

          // set html text on the booking bar
          function bookingbar_set_text( target, content ) {

            if (content) {
              target.html(content);
              target.fadeIn('slow');
            } else {
              target.hide();      
            }
          }

          // update the bookingbar 
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

          } // update_bookingbar

          // submit the form
          function submitForm() {
            $( "#target" ).submit();
          }

          // helper: create range array 
          function range( start, end ){ 
            start = start || 1; return end >= start ? range(start,end-1).concat(end) : []; 
          }

          // helper: has css classes 
          $.fn.extend({
              hasClasses: function (selectors) {
                  var self = this;
                  for (var i in selectors) {
                      if ($(self).hasClass(selectors[i])) 
                          return true;
                  }
                  return false;
              }
          });

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
        
        $('.commons-init').each(function() {
          var self = this;
          $.each($(this).attr('class').replace(/-/g, '_').split(/\s+/), function(i, classnm) {
            if (classnm.substr(0,15) == 'commons_booking') UTIL.fire(classnm, undefined, self);
          });
        });
      }
    };

    $(document).ready(UTIL.loadEvents);
    
    // Place your public-facing JavaScript here

  });

}(jQuery));