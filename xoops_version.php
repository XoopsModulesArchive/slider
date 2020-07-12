<?php

//  ------------------------------------------------------------------------ //
// Author: Ben Brown                                                         //
// Site: http://xoops.thehandcoders.com                                      //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!defined('XOOPS_ROOT_PATH')) {
	die('XOOPS root path not defined');
}

$modversion['name']		    = _MIC_SLIDER_NAME;
$modversion['version']		= 0.1;
$modversion['author']       = 'Kostas Ksilas';
$modversion['description']	= _MIC_SLIDER_DESC;
$modversion['license']		= "GPL see LICENSE"; 
$modversion['official']		= 1;
$modversion['image']		= "images/logo.png";
$modversion['dirname']		= "slider";

// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0]	= "slider";
$modversion['tables'][1]	= "slider_content";
// Admin things
$modversion['hasAdmin']		= 1;
$modversion['adminindex']	= "admin/index.php";
$modversion['adminmenu']	= "admin/menu.php";

// Search
$modversion['hasSearch'] = 0;

// Menu
$modversion['hasMain'] = 1;
global $xoopsDB, $xoopsUser, $xoopsConfig, $xoopsModule, $xoopsModuleConfig;

// Smarty
$modversion['use_smarty'] = 1;

// Templates
$modversion['templates'][1]['file'] = 'slider_index.html';
$modversion['templates'][1]['description'] = _MIC_INDEX_DESC;

// Blocks
$modversion['blocks'][1]['file'] = "slider_block.php";
$modversion['blocks'][1]['name'] = _MIC_BNAME1;
$modversion['blocks'][1]['description'] = _MIC_BNAME1_DESC;
$modversion['blocks'][1]['show_func'] = "slider_block";
$modversion['blocks'][1]['template'] = 'slider_block_index.html';

// Comments
$modversion['hasComments'] = 0;
?>
