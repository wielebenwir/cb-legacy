# Commons Booking 

---

> Note: We are currently working on **CommonsBooking 2.0**, a complete rewrite with many additional features. 
> [CommonsBooking 2 Repo](https://github.com/wielebenwir/commons-booking-2/)

---





**Contributors:** flegfleg, sgrubsmyon  
**Donate link:** https://www.wielebenwir.de/verein/unterstutzen  
**Tags:** booking, commons, sharing  
**Requires at least:** 3.9  
**Tested up to:** 5.4
**Stable Tag:** 0.9.3
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

A Wordpress plugin for management and booking of commons goods. 


## Description 

This plugin gives associations, groups and individuals the ability to share items (e.g. cargobikes, tools) with users. It is based on the idea of Commons and sharing resources for the benefit of the community. 

It was developed for the ["commons cargo bike" movement](http://www.wielebenwir.de/mobilitaet/free-nomadic-cargo-bikes-are-changing-the-urban-landscape) across Germany and Austria, but it can use it for any kind items.

**Unique features:**

* Items, locations and timeframes: No need for a "centralised storage", items can be assigned to different locations for the duration of a timeframe, each with their own contact information.  
* Simple booking process: A booking is at least one full day, just pick the date on the calendar.
* Auto-accept bookings: A registered user can book items without the need for administration. 
* Codes: The plugin automatically generates booking codes, which are used at the station to validate the booking. 
* Users can leave booking comments (e.g. what they use the item for).


**Use cases:**

* You/your associations owns special tools that are not in use every day, and you want to make them available to a local group.
*  You own a cargo bike that you want to share with the community, and it will be placed at different locations throughout the year.

**Plugin websites**

* [Official Website (German)](http://www.wielebenwir.de/projekte/commons-booking)
* [Official WIKI (German)](http://dein-lastenrad.de/index.php?title=Commons_Booking_Software)
* [Bug-Tracker](https://github.com/wielebenwir/commons-booking/issues) 
* [Bulletin Board (German)](http://forum.dein-lastenrad.de/index.php?p=/categories/buchungs-software)


## Upgrade Notice 

New in this version: 

* FEATURE: Set sender name and email address for booking confirmation emails. *Update this in settings!*
* FEATURE: Category WordPress Widget – List all item categories
* FEATURE: User WordPress Widget – User funtions (Login/Logout, Registration, „My Bookings“)
* FEATURE: New Setting: Themes (now included: „standard“ & kasimir“)
* FEATURE: New Setting: Show Day Name Row (Mon, Tue, Wed…) above the calendar
* FEATURE: New Setting: Closed days count. Now you can set how the system counts any number of closed days (0 or 1). See Issue #116
* CHANGE: Consolidated the two setting options under „Advanced“ into „Customize Login and Registration pages“, since one does not make sense without the other.


## Installation 


### Using The WordPress Dashboard 

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'commons-booking'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard


### Uploading in WordPress Dashboard 

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `commons-booking.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard


### Using FTP 

1. Download `commons-booking.zip`
2. Extract the `commons-booking` directory to your computer
3. Upload the `commons-booking` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard



## Frequently Asked Questions 


### Where can i find help/report bugs? 

* [Bug-Tracker](https://github.com/wielebenwir/commons-booking/issues) 
* [Bulletin Board (German)](http://community.dein-lastenrad.de/foren/forum/commons-booking/)


## Changelog 


### 0.9.4.7

* Fixed: Past bookings could be canceled.

### 0.9.4.5

* Maintenance: Better messages on max booking days error
* Fixed: Password reset mail function & redirect (thanks, poilu!)

### 0.9.4.5

* Maintenance: Better messages on max booking days error

### 0.9.4.4 

* Bugfix: Fixed profile update hook to fire later.
* Bugfix: Missing function triggered error #180  (thanks mega-stoffel & poilu)


### 0.9.4.3 

* Bugfix: Remove redirect after profile update that prevented password reset emails to be sent on WP 5.3
* Maintenance: Adress string can now be empty without throwing an error. 

### 0.9.4.2 

* Maintenance: Remove $wpdb->prepare() where not needed
* Maintenance: Version bump

### 0.9.4.1

* Critical bugfix: Logic error in date check of bookings

### 0.9.4

Markus Voge took over adding features to Version 1.0.

* FEATURE: Locations can receive copies of confirmation emails that concern
  their item. This feature must be enabled for each location by checking the new
  checkbox "Send copies of booking confirmation emails to the location" on the
  location edit page. By default, the checkbox is unchecked. If it is checked,
  any email address entered into the contact details text field will receive
  copies of all conformation emails, but only about the item managed by the
  location.
* FEATURE: Emails are sent to users confirming the cancelation of a booking.
  The concerned location is informed via a copy of the email if the "Send
  copies ..." checkbox is checked (see above).
* FEATURE: When an admin deletes an open booking from the bookings table, the
  user (and location if configured, see above) is also informed via email.
* FEATURE: When the location of an item changes, users who already booked are
  informed via email of the new location. If configured for receiving email
  copies (see above), also the old and new location are informed. Admins can
  either edit an existing timeframe to change the location or delete the old and
  create a new timeframe at the correct location. This enables to use a "fake"
  location as a placeholder and then change to the actual location as soon as it
  is known.
* FIXED: Bookings table shows the latest bookings at the top by default (default
  order: descending by booking start date).

### 0.9.4.2 

* Maintenance: Remove $wpdb->prepare() where not needed
* Maintenance: Version bump


### 0.9.3

Maintenance release. New features will be added to Version 2.0.

* FIXED: Calendar javascript selection count failed if weekday rows enabled (Thanks to Markus Voge). 
* Minimal change: Div class name changed from "intro" to "cb-intro" (Again, thanks to Markus Voge). 


### 0.9.2.12

* FIXED: Closed days are now correctly rendered on the calendar, and non-bookable. 


### 0.9.2.11 

* FIXED: Re-added screenshots & updated WP plugin directory description.  


### 0.9.2.9 

* FIXED: Yoast SEO campatibility
* FIXED: Jetpack Compatibility #146


### 0.9.2.6 

* FIXED: a typo in email registration settings


### 0.9.2.5 

* FIXED: Location information is now displayed correctly. 


### 0.9.2.4 

* CHANGE: Plugin directory structure flattened


### 0.9.3 

* CHANGE: Editors now have access to the CB Menu. (capability: publish_pages)
* FIX: Closed days where not correctly rendered



### 0.9.3 

* FIX: Javascript validation no longer failing if week day row is activated


### 0.9.2 

* FEATURE: Set sender name and email address for booking confirmation emails.


### 0.9.1.2 

* Updated profile cleanup styles for standard WordPress & "Theme My Login"
* Updated settings intro text to be translateable
* Consolidated the two setting options under "Advanced" into "Customize Login and Registration pages", since one doesn´t make sense without the other.


### 0.9.1 

* FIX: Issue #131: Booking comments are now restricted to max 140 characters.
* ENHANCEMENT: Compatibility with Theme my Login & Kasimir Wordpress-Theme
* FIX: No more Javscript errors if no timeframe configured. Bookingbar is hidden.
* ENHANCEMENT: User Widget formatting


### 0.9 

* FEATURE: Category Wordpress Widget – List all item categories
* FEATURE: User Wordpress Widget – User funtions (Login/Logout, Registration, "My Bookings")
* FEATURE: New Setting: Themes (now included: "standard" & kasimir")
* FEATURE: New Setting: Show Day Name Row (Mon, Tue, Wed…) above the calendar
* FEATURE: New Setting: Closed days count. Now you can set how the system counts any number of closed days (0 or 1). See Issue #116
* FIX Issue #120: Bulk delete in Bookings table
* FIX: The first overbookable day was not correctly marked in calendar selection.
* FIX Issue #134: Extra user fields are now visible in backend



### 0.8.0.10 

* FIX: /Languages/ folder removed from svn, translations should be applied now. 


### 0.8.0.9 
* FIX: Booking comments now displayed regardless of DB Prefix (again, this should do it).
* FIX: CSS Styling of rows with only one item.
* FIX: Removed PHP shorthand tag causing errors on some servers.



### 0.8.0.8 
* FIX: Booking comments now displayed regardless of DB Prefix.


### 0.8.0.7 

* FIX Issue #120: Bookings can be deleted, correct Ids are printed in the bookings table.
* FIX Translation strings for the booking table. 
* FIX: Removed button borders in css
* FIX: Logout-Button has button styling now.
* ENHANCEMENT Bookings table filter dropdown: Now sorted alphabetically.
* FEATURE: Added optional row of weekdays.



### 0.8.0.6 

* Removed language files & functions. 


### 0.8.0.2 

* Repackaging so the german translation gets picked up. 


### 0.8 

* FEATURE: Added print button to edit timeframes screen and custom print styles. 
* ENHANCEMENT: Instead of code ids, the codes are now shown in the bookings table.
* FIX: All locations/items are visible in the filter dropdown again.
* FIX: Users sometimes could not access their bookings in 0.7.0.7
* Cleanup of language files (Removed Whitespaces in strings, etc.)


### 0.7.0.6 

	* FIX:  Double Bookings (two pending bookings on the same day could be both finalised) are no longer possible.



### 0.7.0.6 

* FIX: Address field now printed out correctly
* FIX: Now correctly calculating the date difference to enable/disable booking cancel button. 
* ENHANCEMENT: Better structure of variables in SASS Files 
* ENHANCEMENT: Design updates. 
* ENHANCEMENT:  Better display of the table filter if no items/timeframes/codes have been created. 
* FIX: Flushing rewrite rules should no longer be necessary on first installation.  



### 0.7.0.5 

* FIX: Booking is now possible when using custom db prefixes.


### 0.7.0.4 

* Sanitizing extra user profile fields


### 0.7.0.3 

* Re-packeged plugin. Should fix "can not confirm or cancel" error
* FIX: Address was not printed on calendar. Issue #106
* ENHANCEMENT: Address now added to timeframes-compact template. 


### 0.7.0.2 

* FIX: Show no error message if one of the location address fields are empty



### 0.7.0.1 

* FIX: When using the plugin Yoast SEO, login/user-bar was shown multiple times
* FIX Issue #98 : enabled pagination on item pages


### 0.7 

* FEATURE: Redesigned Calendar with better representation of pickup/return/booking over weekends and helpful tooltips
* FEATURE: Redesigned item lists 
* FEATURE: Redesigned timeframes. 
* FEATURE: Bookings list & booking Detail now have a handy navigation bar. 
* FEATURE: Added CRON job:  Pending bookings will be deleted after one day. 
* FEATURE: Booking Comments. Users can add a comment on the booking review screen, which will be shown in the calendar tooltip. Uses the Wordpress comments feature, so 
* Behaviour of the calendar date picker. Now, you have to select the days in sequence. 
* REMOVED: User Profile/Registration/Forgot Password functionality. I recommend installing "Theme my Login". 
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
* ENHANCEMENT: Daily Cron job deletes all pending bookings older than one day 
* ENHANCEMENT: Output of location address can now be formatted (Localisation in translation file) 
* ENHANCEMENT: Adding new timeframes from the item edit screen: Added a link to navigate back to the item. 
* ENHANCEMENT: Timeframe Edit Screen: First location/Item is pre-selected in dropdown menu. Provides better behaviour if using the plugin with just one location. 
* ENHANCEMENT: Timeframe Edit Screen: Added More meaningful message if no locations/items available. 
* ENHANCEMENT: Booking Review & Booking Confirmation are now separate pages (so you can add your own texts). 
* ENHANCEMENT: Bookings Table is now sortable
* ENHANCEMENT: Opening hours are now shown on the calendar pages. 



### 0.5.3.1 
* Fix Issue #96: Login redirect not working


### 0.5.3 
* added missing translation strings
* Fix user names / first_name / last_name in Email template
* Fixed status string: canceled bookings are shown as canceled on the user page
* Issue #74, Issue #71 Fix: A day that was booked couldn´t be booked again.
* Fix: Replaced user_name with first_name & last_name


### 0.5.2.1 
* fixed missing translation strings ("Confirm Booking" & "Cancel")
* Now printing the address fields in item single view as comma-seperated


### 0.5.2 
* Removed login page backend setting & creation by plugin (all handled by user page)


### 0.5.1 
* New translation file (Thanks, Albert!)
* Enhancement: Added link to WP Passwort recovery page. 
* Enhancement: If registration submit was not successful, the following page will include the form now. 
* Fix: Removed dummy tooltip message
* Fix: for an issue when the code generation module picked only elements with upper case "S".
* Fix: a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed
* Fix: No emails will be send if there are any errors in the registration process
* Fix: Fixed a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed.
* Fix: Pagination in codes table: offset is calculated correctly now.


### 0.5.0.2 
* removed "info/more" button (normal WP page content is rendered now)


### 0.5.0.1 
* Fixed div tag not closed


### 0.5 
* Feature: Issue #34  Shortcode for items implemented
* Feature: Days to show: Set the days to 
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


### 0.4.2 
* Added German translation


### 0.4.1.1 
* Fix: Now compatible with PHP Version 5.3.26


### 0.4.1 
* Fix: Timeframes starting on sundays now correctly indented
* Fix: If only a single timeframe was configured, it was not shown at the item edit screen


### 0.4. "Surfin´Safari" 
* Auto-Setup: All necessary Settings fields will be set on activation
* Added form input sanitization
* New animations for the booking bar
* If timeframe note is empty, hide the "-" delimiter
* Now checking for date validity
* Codes list: Titles of songs by the Beach Boys



### 0.3.2 
* Disable the settungs import/export function (not working right now)


### 0.3.1 
* Fixed dependecies


### 0.3 "Aloha" 
* A fresh new look
* Major cleanup (functions and files)
* User page
* Custom registration page
* Custom Login page
* Feature: Users can cancel bookings


### 0.2 "Fab Four" 
* Codes list: Titles of songs by the Beatles
* Booking calendar 
* Mailing functionality
* Settings for messages and mail
* Template-tags for messages and mails


### 0.1 
* Basic booking functionality
* Timeframes
* Bookings
* Custom post types
