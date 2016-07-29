# 0.9 

* FEATURE: Category Wordpress Widget – List all item categories
* FEATURE: User Wordpress Widget – User funtions (Login/Logout, Registration, "My Bookings")
* FEATURE: New Setting: Themes (now included: "standard" & kasimir")
* FEATURE: New Setting: Show Day Name Row (Mon, Tue, Wed…) above the calendar
* FEATURE: New Setting: Closed days count. Now you can set how the system counts any number of closed days (0 or 1). See Issue #116
* FIX Issue #120: Bulk delete in Bookings table
* FIX: The first overbookable day was not correctly marked in calendar selection.
* FIX Issue #134: Extra user fields are now visible in backend


# 0.8.0.10

* FIX: /Languages/ folder removed from svn, translations should be applied now. 

# 0.8.0.9

* FIX: Booking comments now displayed regardless of DB Prefix (again, this should do it).
* FIX: CSS Styling of rows with only one item.
* FIX: Removed PHP shorthand tag causing errors on some servers.


# 0.8.0.8 

* FIX: Booking comments now displayed regardless of DB Prefix.

# 0.8.0.7

* FIX Issue #120: Bookings can be deleted, correct Ids are printed in the bookings table.
* FIX Translation strings for the booking table. 
* FIX: Removed button borders in css
* FIX: Logout-Button has button styling now.
* ENHANCEMENT Bookings table filter dropdown: Now sorted alphabetically.
* FEATURE: Added optional row of weekdays.

# 0.7.0.6

* FIX: Address field now printed out correctly
* FIX: Now correctly calculating the date difference to enable/disable booking cancel button. 
* ENHANCEMENT: Better structure of variables in SASS Files 
* ENHANCEMENT: Design updates.  
* ENHANCEMENT:  Better display of the table filter if no items/timeframes/codes have been created. 
* FIX: Flushing rewrite rules should no longer be necessary on first installation. 


# 0.7.0.5

* FIX: Booking is now possible when using custom db prefixes.

# 0.7.0.4

* Sanitizing extra user profile fields

# 0.7.0.3

* Re-packeged plugin. Should fix "can not confirm or cancel" error
* FIX: Address was not printed on calendar. Issue #106
* ENHANCEMENT: Address now added to timeframes-compact template. 

# 0.7.0.2

* FIX: Show no error message if one of the location address fields are empty

# 0.7.0.1

* FIX: When using the plugin Yoast SEO, login/user-bar was shown multiple times
* FIX Issue #98 : enabled pagination on item pages

# 0.7 

**Features**

* FEATURE: Redesigned Calendar with better representation of pickup/return/booking over weekends and helpful tooltips
* FEATURE: Redesigned item lists 
* FEATURE: Redesigned timeframes. 
* FEATURE: Bookings list & booking Detail now have a handy navigation bar. 
* FEATURE: Added CRON job:  Pending bookings will be deleted after one day. 
* FEATURE: Booking Comments. Users can add a comment on the booking review screen, which will be shown in the calendar tooltip. Uses the Wordpress comments feature, so 

**Changes**
* Behaviour of the calendar date picker. Now, you have to select the days in sequence. 
* REMOVED: User Profile/Registration/Forgot Password functionality. I recommend installing "Theme my Login". 

**Bug fixes**

* FIX: Now correctly jumping to selected timeframe anchor when "Book here" was clicked in item list  
* FIX: Issue with javascript de/selection sometimes behaving strange
* FIX: Javascript validation bug when booking over multiple days
* FIX: Server-side booking validation (closed days are now correctly counted) 
* FIX: Fix: Registration page extra fields were not properly defined ( if $_POST didn´t exist)
* FIX: User meta data was not saved when editing the profile. 
* FIX: Unchecked Checkboxes in settings don´t throw errors anymore. 
* FIX: Bookings that are "pending" are no longer shown on the User Bookings page. 
* FIX: Codes table pagination
* FIX: Bookings table pagination
* FIX: Pagination table
* FIX: Fixed ambiguous (wrong) message after hitting "Save and generate codes" on the Timeframes edit screen when no changes were made.
* FIX: Plugin localization string in cb-timeframes-edit.php 
* FIX: Items List: If no item short description provided, show a message.
* Fix: URL to booking in booking confirmation email now compatible with Wordpress non-pretty  Permalinks (?page=X)
* FIX: Double bookings are no longer possible. (compare function (booked days != selected days) did not work).  
* FIX: Users can´t cancel bookings from the past (booking end date < current date)
* FIX: HTML structure, items without a timeframe now have a proper end < div > ending-tag
* FIX: Item Timeframes: If no timeframe present, display message
* FIX: Booking over closed days php validation now correctly throws an error when javascript is tricked.

**Enhancements**

* ENHANCEMENT: Daily Cron job deletes all pending bookings older than one day 
* ENHANCEMENT: Output of location address can now be formatted (Localisation in translation file) 
* ENHANCEMENT: Adding new timeframes from the item edit screen: Added a link to navigate back to the item. 
* ENHANCEMENT: Timeframe Edit Screen: First location/Item is pre-selected in dropdown menu. Provides better behaviour if using the plugin with just one location. 
* ENHANCEMENT: Timeframe Edit Screen: Added More meaningful message if no locations/items available. 
* ENHANCEMENT: Booking Review & Booking Confirmation are now separate pages (so you can add your own texts). 
* ENHANCEMENT: Bookings Table is now sortable
* ENHANCEMENT: Opening hours are now shown on the calendar pages. 

**Known issues:** 

* YOAST SEO: Booking Confirmation Message is not displayed. Possible Workaround: [Disabling WordPress SEO plugins on a page by page basis - MemberFindMe](https://memberfind.me/disabling-wordpress-seo-plugins-on-a-page-by-page-basis/) 

# 0.6 
* FIX: Corrected Database Table Version Option names
* FIX: Installation / Code Table creation. Corrected Primary column (id). Removed legacy if empty. 

# 0.5.3 

* added missing translation strings
* Fix user names / first_name / last_name in Email template
* Fixed status string: canceled bookings are shown as canceled on the user page
* Issue #74, Issue #71 Fix: A day that was booked couldn´t be booked again.
* Fix: Replaced user_name with first_name & last_name


# 0.5.2.1
* fixed missing translation strings ("Confirm Booking" & "Cancel")
* Now printing the address fields in item single view as comma-seperated

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