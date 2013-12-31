Clean Zombie Users
Version 0.4b (beta)
==================

Damn zombie users! Always eating your MySQL database. This WordPress plugin cleans up all sorts of users, especially spammy ones. Braaaaaains. 

Included Features:

* Target specific user roles for deletion based on whether they have ever posted or commented
* Includes a test mode to see what the results will be
* Includes the ability to delete users in chunks, for large sites.
* We don't need to validate boolean values on checkboxes/radios but will do it just to be OCD

To Do:

* Allow newer users to be excluded
* Build in logic for all major e-commerce plugins

*** CHANGELOG ***

Version 0.4b (beta)
* Fixed the undefined index in dynamic variables on lines 71 and 72.
* Fixed a jQuery mistake in the confirmation conditional
* Plugin now 100% error free in testing.

Version 0.4a (beta)
* Fixed the has_cap warning by changing "8" to "manage_options" in option page permissions
