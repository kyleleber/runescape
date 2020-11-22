CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Troubleshooting
 * FAQ
 * Maintainers

INTRODUCTION
------------
The account management module provides an integration between the game server and a drupal form to create
new in-game accounts and link them through the Drupal account. This module also allows in-game account migrations to happen from
various other runescape private servers through parsing their highscores table.

REQUIREMENTS
------------
This module has no other module dependencies.

INSTALLATION
------------
Follow the standard installation of any Drupal module.
Upon installation of module, you will need to import the mysql file `add_forum_account.sql`
found in the game_database_addons directory in this module. This will add the column to the database
that is needed to link a forum account to an in-game account.

CONFIGURATION
-------------
No manual configuration is necessary for enabling this module.

TROUBLESHOOTING
---------------
N/A

FAQ
---
N/A

MAINTAINERS
-----------
Kyle Leber <kyleleber2014@gmail.com>
