<?php

/* ====================
[BEGIN_COT_EXT]
Code=treecatspage
Name=Tree Cats Page
Category=navigation-structure
Description=The category tree for the page module is displayed globally anywhere in the template. 
Version=2.2.27
Date=March 4Th, 2026
Author=Webitproff
Copyright=Copyright (c) Webitproff
Notes=
Auth_guests=R
Lock_guests=12345A
Auth_members=R
Lock_members=12345A
Requires_modules=page
Requires_plugins=
Order=50
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
blacktreecatspage=01:string:::Category codes (black list codes page structure as system, unvalidated e.t.c)
[END_COT_EXT_CONFIG]
==================== */


/**
 * Tree Cats Page (tree of page categories anywhere)
 * Plugin treecatspage for Cotonti 0.9.26, PHP 8.4+
 * Filename: treecatspage.setup.php 
 * Purpose: Setup & Config File. Register data in $db_core, $db_auth and $db_config for the Plugin
 * Date: March 4Th, 2026
 * Note: Correct counting + clickability of parent categories along with a drop-down list of child categories
 * Source: https://github.com/webitproff/cot-treecatspage
 * WepPage: https://abuyfile.com/ru/market/cotonti/plugs/plagin-tree-cats-page-dlya-cotonti-siena-0926
 *
 * @package treecatspage
 * @version 2.2.27
 * @author webitproff
 * @copyright Copyright (c) webitproff 2026 | https://github.com/webitproff
 * @license BSD
 */  
 
