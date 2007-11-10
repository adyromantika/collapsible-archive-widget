=== Collapsible Archive Widget ===
Contributors: adywarna
Donate link: http://www.romantika.name/v2/
Tags: collapse, collapsible, archive, collapsible archive, widget
Requires at least: 2.1
Tested up to: 2.3
Stable tag: trunk

This simple plugin is a widget that displays a collapsible archives list in your widgetized sidebar by utilizing JavaScript.

== Description ==

This simple plugin is a widget that displays a collapsible archives list in your widgetized sidebar by using JavaScripts. In version 2.0.0 script.aculo.us effects has been added as an option, utilizing the script.aculo.us files supplied with WordPress.

== Installation ==

1. Upload `collapsible-archive.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Use your 'Presentation'/'Sidebar Widgets' settings to drag and configure

== Configuration ==

* Widget title: the title of the widget
* List type: ul for bulleted list, p for paragraph, br for paragraph with line breaks
* Show Post count: Whether or not to show the post number for each year and month
* Abbreviate month names: Check this box to show abbreviation of month names
* Use script.aculo.us effects: Whether or not to show effects
* Expand effect: Effect to use when expanding the list
* Collapse effect: Effect to use when collapsing the list
* Expand the list by default: Check this box to have the list expanded when loaded
* Show individual posts: Show posts in the list. This should be used in extra caution; if you have a lot of posts consider disabling it as this will take time to load

== Change Log ==

* 03-Aug-2007: Initial version
* 04-Sep-2007: Added ability to select whether to use abbreviations for the month names, and script.aculo.us effects!
* 27-Sep-2007: Fixed javascript include - effects.js added and scriptaculous.js removed (For some reason it worked in 2.2).
* 10-Nov-2007: Added ability to display posts (with caution), to expand by default, and also added plus and minus signs as expand/collapse buttons
