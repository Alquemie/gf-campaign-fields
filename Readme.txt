=== Gravity Forms Campaign Fields Add-On ===
Contributors: alquemie
Tags: gravityforms, google analytics, marketing
Requires at least: 6.0
Tested up to: 6.1.1
Stable tag: 3.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add a hidden field to capture marketing campaign data in Gravity Forms.

== Description ==

This plugin adds an advanced field to Gravity Forms that collects marketing campaign and device data and stores the data with form entries.  The form field is automatically added to any form that is edited or created.

The plugin can be configured to track first touch or last touch attribution and the campaign query string parameters are customizable.

The plugin currently supports:

* Google Analytics UTM Parameters
* Google AdWords (GCLID and MatchType)
* Device Information (browser, OS, device type)
* Marin (KWID and Creative ID)
* Facebook ClickId 
* Microsoft(Bing) ClickId 
* Criteo ID
* Twitter ClickId 
* Snapchat ClickId 

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/gf-campaign-fields` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Forms->Settings->Marketing Campaign screen to configure the plugin

== Frequently Asked Questions ==

= Does this plugin include Gravity Forms? =

No, you must purchase your own license of Gravity Forms


== Screenshots ==

1. The settings screen where you define first/last touch attribution and the query string parameters used to define the values
2. Building a form that contains campaign fields

== Changelog ==
= 3.0.0 =
* Complete Code Refactor 
* NOT compatible with previous versions
