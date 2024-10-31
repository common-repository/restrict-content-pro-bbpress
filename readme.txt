=== Restrict Content Pro - bbPress ===
Author URI: https://ithemes.com
Author: iThemes
Contributors: jthillithemes, ithemes
Tags: Restrict content, member only, registered, logged in, restricted access, restrict access, limiit access, read-only, read only, bbpress, restrict content pro
Requires at least: 3.3
Tested up to: 5.8.0
Stable tag: 1.0.2

Adds support for restricting bbPress forums and topics to paid members with Restrict Content Pro

== Description ==

**On October 14th, 2021, all Restrict Content Pro add-ons will be removed from the WordPress plugin repository.**

**This plugin and all other Restrict Content Pro add-ons will remain available to download in your <a href="https://members.ithemes.com/panel/downloads.php">iThemes Member's Panel</a>.**

This is an add-on for the [Restrict Content Pro plugin](https://restrictcontentpro.com/). It does not function on its own.

This plugin will add support for limiting bbPress forums and topics to paid members and / or members with specific access rights. It makes it exceptionally easy to have members-only forums.

Learn more about Restrict Content Pro at [restrictcontentpro.com](https://restrictcontentpro.com/)

== Screenshots ==

1. Each forum and topic have the option to restrict it to paid users

== Changelog ==

= 1.0.2 =
* New: Added new Updater

= 1.0.1 =
* New: Added support for the upcoming Restrict Content Pro 3.1 update.
* New: Added PHPDocs.
* Tweak: Updated plugin author name and URL.
* Tweak: General code cleanup and formatting.
* Fix: Filter contents of restricted topics in RSS feeds.
* Fix: Remove use of deprecated `bbp_has_topics_query` filter.

= 1.0 =

Added support for restrictin forums / topics based on the subscription level, the paid status, and the access level of the member.
Improved the display of the metabox options.
Improved overall code quality.

= 0.6 =

Fixed: improper redirect when accessing a restricted topic while logged into an account that does not have access

= 0.5 =

New: send unauthorized users to the login page with a redirect back to the restricted topic / reply when accessing directly instead of using wp_die()

= 0.4 =

Fixed: allow forum moderators and Editors to view restricted forums / topics

= 0.3 =

Fixed: a bug with non-paid users still being able to access premium topics if using the direct URL
Tweaked: improved code formatting
Tweaked: made a few general code improvements

= 0.2 = 

Added default language files.
Added German translation files.

= 0.1 =

* First beta release.

== Upgrade Notice ==

= 0.2 = 

Added default language files.
Added German translation files.

= 0.1 =

* First beta release.

