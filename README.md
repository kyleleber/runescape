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
The `runescape` module provides functionality used to integrate a runescape private server source base
into the Drupal application through a series of routes, controllers, and extendable services. The main `runescape` module
contains configuration entities for NPCs and Items, containing the original RSC ids and descriptions that are used to sync the game server
to these entities. The list is currently incomplete, but is a work-in-progress to get them updated and added. This system also allows
for custom items and npcs.

This module has no dependencies on any other modules.

Packaged within are a handful of sub-modules that can be optionally added for additional features.

`runescape_account_management`

The account management module provides an integration between the game server and a drupal form to create
new in-game accounts and link them through the Drupal account. This module also allows in-game account migrations to happen from
various other runescape private servers through parsing their highscores table.

`runescape_highscores`

The highscores module provides an integration between the game server and Drupal to create a tabular view of
in-game account stats and experiences and ranks them against other players.

`runescape_map_tracking`

The map tracking module privates an integration between the game server and Drupal to create a player-tracking
map. This utilizes the coordinates that the in-game character is currently at, and overlays it on a map
of the base-level map areas. Currently, only the ground level works for tracking.

`runescape_server_integration`
The server integration module is a base module that is used by the other submodules as the interface between
Drupal's database and the external database.

REQUIREMENTS
------------
This module has no other module dependencies.

INSTALLATION
------------
Follow the standard installation of any Drupal module.
Upon installation, configuration entities will be created for npcs and items.
A new administrative configuration menu item will be created at /admin/config/runescape.

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