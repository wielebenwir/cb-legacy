# 0.5.2 
* Removed login page backend setting & creation by plugin (all handled by user page)

# 0.5.1
* New translation file (Thanks, Albert!)
* Enhancement: Added link to WP Passwort recovery page. 
* Enhancement: If registration submit was not successful, the following page will include the form now. 
* Fix: Removed dummy tooltip message
* Fix: for an issue when the code generation module picked only elements with upper case "S".
* Fix: a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed
* Fix: No emails will be send if there are any errors in the registration process
* Fix: Fixed a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed.
* Fix: Pagination in codes table: offset is calculated correctly now.

# 0.5.0.2 
* removed "info/more" button (normal WP page content is rendered now)

# 0.5.0.1
* Fixed div tag not closed

# 0.5
* Feature: Issue #34  Shortcode for items implemented
* Feature: Days to show: Set the days to show in calendar 
* Feature: Single Items: "More…" button is only shown if description is entered (item edit main text field)
* Fix: User info (first & last name) now correctly displayed in booking admin table.
* Fix: Issue #31: content will now display on functional pages
* Fix: Compatibility with Page Builder  
* Fix: Calendar was not rendered
* Fix: Backend tables now show 30 entries instead of 5
* Fix: Datepicker now showing correctly
* Fix: HTML Tags on pages were filtered by plugin
* Fix: Issue #30 renamed css class to solve the issue that calendar was not rendered in wordpress themes with bootstrap-tooltip.
* Fix: #36 fixed the issue that the timeframes with a start date later than today weren´t shown correctly in the item edit screen.
* + many more…

# 0.4.2
* German translation (Thanks Sven, thanks )

# 0.4.1.1
* Fix: Now compatible with PHP Version 5.3.26

# 0.4.1 
* Fix: Timeframes starting on sundays now correctly indented
* Fix: If only a single timeframe was configured, it was not shown at the item edit screen

# 0.4 "Surfin´Safari" 
* Auto-Setup: All necessary Settings fields will be set on activation
* Added form input sanitization
* New animations for the booking bar
* If timeframe note is empty, hide the "-" delimiter
* Now checking for date validity
* Codes list: Titles of songs by the Beach Boys

# 0.3.2  
* Disable the settungs import/export function (not working right now)

# 0.3.1 
* Fixed dependecies
* Fixed typos

# 0.3 "Aloha"
* A fresh new look
* Major cleanup (functions and files)
* User page
* Custom registration page
* Custom Login page
* Feature: Users can cancel bookings

# 0.2 "Fab Four" 
* Codes list: Titles of songs by the Beatles
* Booking calendar 
* Mailing functionality
* Settings for messages and mail
* Template-tags for messages and mails

# 0.1 
* Basic booking functionality
* Timeframes
* Bookings
* Custom post types