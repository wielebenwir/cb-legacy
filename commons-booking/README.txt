=== Commons Booking ===

Contributors: Florian Egermann, Christian Wenzel, Sven Baier, Stefan Meretz
Donate link: https://www.wielebenwir.de/verein/unterstutzen
Tags: booking, commons
Requires at least: 3.9
Tested up to: 4.3.
Stable tag: 0.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Wordpress plugin for management and booking of commons. Brought you by wielebenwir.de

== Description ==

Unique features:
* Items, locations and timeframes: Set the locations and timeframes (when the item is bookable at that location) for each of your items. 
* Auto-accept bookings: A registered user can book items without the need for administration. 
* Simple booking process with a beautiful calendar: Click a day to book an item. 

* Official Website: http://www.wielebenwir.de/projekte/gemeingutsoftware
* Manual: http://dein-lastenrad.de/index.php?title=Introduction
* Bug-Tracker: https://bitbucket.org/wielebenwir/commons-booking/issues?status=new&status=open 
* Forum (Ask questions here): http://forum.dein-lastenrad.de/index.php?p=/categories/buchungs-software


== Installation ==

= Using The WordPress Dashboard (Note: Plugin is not yet available on the Wordpress Plugins Directory) =

1. Navigate to the 'Add New' in the plugins dashboard
2. Search for 'commons-booking'
3. Click 'Install Now'
4. Activate the plugin on the Plugin dashboard

= Uploading in WordPress Dashboard =

1. Navigate to the 'Add New' in the plugins dashboard
2. Navigate to the 'Upload' area
3. Select `commons-booking.zip` from your computer
4. Click 'Install Now'
5. Activate the plugin in the Plugin dashboard

= Using FTP =

1. Download `commons-booking.zip`
2. Extract the `commons-booking` directory to your computer
3. Upload the `commons-booking` directory to the `/wp-content/plugins/` directory
4. Activate the plugin in the Plugin dashboard


== Frequently Asked Questions ==

= Where can i find help/report bugs? =

Please go here: http://forum.dein-lastenrad.de/index.php?p=/categories/buchungs-software


== Screenshots ==

== Changelog ==

== 0.5.2 == 
* Removed login page backend setting & creation by plugin (all handled by user page)

== 0.5.1 ==
* New translation file (Thanks, Albert!)
* Enhancement: Added link to WP Passwort recovery page. 
* Enhancement: If registration submit was not successful, the following page will include the form now. 
* Fix: Removed dummy tooltip message
* Fix: for an issue when the code generation module picked only elements with upper case "S".
* Fix: a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed
* Fix: No emails will be send if there are any errors in the registration process
* Fix: Fixed a bug where days with multi-page bookings were not correctly rendered as "non-bookable" if the booking start date had passed.
* Fix: Pagination in codes table: offset is calculated correctly now.

== 0.5.0.2 == 
* removed "info/more" button (normal WP page content is rendered now)

== 0.5.0.1 == 
* Fixed div tag not closed

== 0.5 ==
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

== 0.4.2 =
* Added German translation

== 0.4.1.1 =
* Fix: Now compatible with PHP Version 5.3.26

= 0.4.1 =
* Fix: Timeframes starting on sundays now correctly indented
* Fix: If only a single timeframe was configured, it was not shown at the item edit screen

= 0.4. "Surfin´Safari" =
* Auto-Setup: All necessary Settings fields will be set on activation
* Added form input sanitization
* New animations for the booking bar
* If timeframe note is empty, hide the "-" delimiter
* Now checking for date validity
* Codes list: Titles of songs by the Beach Boys


= 0.3.2 = 
* Disable the settungs import/export function (not working right now)

= 0.3.1 =
* Fixed dependecies

= 0.3 "Aloha" =
* A fresh new look
* Major cleanup (functions and files)
* User page
* Custom registration page
* Custom Login page
* Feature: Users can cancel bookings

= 0.2 "Fab Four" =
* Codes list: Titles of songs by the Beatles
* Booking calendar 
* Mailing functionality
* Settings for messages and mail
* Template-tags for messages and mails

= 0.1 =
* Basic booking functionality
* Timeframes
* Bookings
* Custom post types

== Known Bugs ==

See: https://bitbucket.org/wielebenwir/commons-booking/issues?status=new&status=open


== Credits == 

* Built with [The WordPress Plugin Boilerplate Powered ](https://github.com/sudar/wp-plugin-in-github/wiki) 
* Uses [CMB2](https://github.com/WebDevStudios/Custom-Metaboxes-and-Fields-for-WordPress)
* Uses [CPT_Core](https://github.com/WebDevStudios/CPT_Core)
* Uses [Taxonomy_Core ]( https://github.com/WebDevStudios/Taxonomy_Core )