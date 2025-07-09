<?php
/**
 * [BEGIN_COT_EXT]
 * Hooks=global
 * [END_COT_EXT]
 */

defined('COT_CODE') or die('Wrong URL.');
require_once cot_incfile('page', 'module');
require_once cot_langfile('treecatspage', 'plug');
require_once cot_incfile('treecatspage', 'plug');
list($auth_read, $auth_write, $auth_admin) = cot_auth('module', 'page');
$cot_build_structure_page = cot_build_structure_page_tree('', array());

