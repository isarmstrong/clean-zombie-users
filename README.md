Clean Zombie Users
Version 0.4d (beta)
==================

If you have a WordPress site with lots spammer registrations, this plugin will delete any user who has never had a post or comment approved.

== Description ==
Anyone who has run a WordPress site for more than a few weeks knows that 95% of all site registrations typically come from spammers. When they can\'t get a comment approved, they just sit in the database, clogging up the works.

We call these folks Zombie users, and they\'re coming for your brains.

This is a plugin designed to do one thing: clean up zombie users.

If you have a WordPress site with lots spammer registrations, this plugin will delete any user who has never had a post or comment approved. Zombie Users... BRAAAAINS.

Included Features:
-------------------------------------------------
* Target specific user roles for deletion based on whether they have ever posted or commented
* Includes a test mode to see what the results will be
* Includes the ability to delete users in chunks, for large sites.

To Do:
-------------------------------------------------
* Allow newer users to be excluded
* Build in logic for all major e-commerce plugins so that paying customers can\'t be pruned
* Boolean values don\'t really need to be sanitized, but we\'re going to do it anyway
* Need to make the entire introductory message change after a post
* Need to put the deleted users list into a scrolling DIV

Ongoing Development
-------------------------------------------------
If you would like to contribute something, find the plugin at:
https://github.com/isarmstrong/clean-zombie-users

== Installation ==
1. Upload \"test-plugin.php\" to the \"/wp-content/plugins/\" directory.
2. Activate the plugin through the \"Plugins\" menu in WordPress.
3. Visit the options page in your administrative settings and get cleaning

== Screenshots ==
1. The plugin options screen options-page.png
2. Successful Operation Screen success-page.png

== Changelog ==
= 0.4d =
* Properly sanitized the text field for limiting results

= 0.4c =
* Broke up the plugin into smaller files for greater readability
* Properly enqueued styles & scripts

= 0.4b =
* Fixed the undefined index in dynamic variables on lines 71 and 72.
* Fixed a jQuery mistake in the confirmation conditional
* Plugin now 100% error free in testing.

= 0.4a =
* Fixed the has_cap warning by changing \"8\" to \"manage_options\" in option page permissions