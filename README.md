Clean Zombie Users
Version 0.4a (beta)
==================

Damn zombie users! Always eating your MySQL database. This WordPress plugin cleans up all sorts of users, especially spammy ones. Braaaaaains. 

Included Features:

* Target specific user roles for deletion based on whether they have ever posted or commented
* Includes a test mode to see what the results will be
* Includes the ability to delete users in chunks, for large sites.

To Do:

* Fix undefined indexes in the dynamic variable on line 71
* Allow newer users to be excluded
* Build in logic for all major e-commerce plugins

*** CHANGELOG ***

Version 0.4a (beta)
* Fixed the has_cap warning by changing "8" to "manage_options" in option page permissions
