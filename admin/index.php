<?php
// includes
include ('../../../mainfile.php');
include ('../../../include/cp_header.php');

if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
	include ("../language/".$xoopsConfig['language']."/main.php");
} else {
	include ("../language/english/main.php");
}

$myts =& MyTextSanitizer::getInstance();
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

xoops_cp_header();

global $xoTheme, $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
include_once ('../../../class/theme.php');
	
	if (!isset($xoopsTpl) || !is_object($xoopsTpl)) 
	{
		include_once(XOOPS_ROOT_PATH."/class/template.php");
		$xoopsTpl = new XoopsTpl();
	}
	
	if(isset($xoTheme) || is_object($xoTheme))
	{
		$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
		$xoTheme->addScript('modules/slider/js/jqdialog.js');
		$xoTheme->addStylesheet('modules/slider/css/jqdialog.css', array('media' => 'screen'));
	}
	else
	{
		$xoopsTpl->assign('scripts','<script src="'.XOOPS_URL.'/modules/slider/js/jquery.js" type="text/javascript"></script>
	<script src="'.XOOPS_URL.'/modules/slider/js/jqdialog.js" type="text/javascript"></script>		
	<link rel="stylesheet" media="screen" href="'.XOOPS_URL.'/modules/slider/css/jqdialog.css" type="text/css" />');
	}	

include_once (XOOPS_ROOT_PATH.'/modules/slider/class/sliders.php');

$sliders = new Sliders();
$xoopsTpl->assign('totalcount', $sliders->totalsliders);

$sliderlist = $sliders->getAllSliders();

$xoopsTpl->assign('sliderlist',$sliderlist);
$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_index.html');
xoops_cp_footer();

?>