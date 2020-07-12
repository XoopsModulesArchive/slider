<?php
	include "../../mainfile.php";
	global $xoopsModuleConfig,$xoopsModule, $xoopsUser, $xoopsConfig;
	$xoopsOption['template_main'] = 'slider_index.html';
	include_once XOOPS_ROOT_PATH.'/header.php';
	
	if(isset($xoTheme) || is_object($xoTheme))
	{
		$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
		
		$xoTheme->addScript('modules/slider/js/jquery.easing.1.3.js');
		$xoTheme->addScript('modules/slider/js/jquery.ennui.contentslider.js');
		$xoTheme->addStylesheet('modules/slider/css/slider.css', array('media' => 'all'));
		$xoTheme->addScript('modules/slider/js/slider.js');
	
	}
	else
	{
		$xoopsTpl->assign('scripts','<script src="'.XOOPS_URL.'/modules/slider/js/jquery.js" type="text/javascript"></script>
<script src="'.XOOPS_URL.'/modules/slider/js/slider.js" type="text/javascript"></script>		
<script src="'.XOOPS_URL.'/modules/slider/js/jquery.easing.1.3.js" type="text/javascript"></script>
<script src="'.XOOPS_URL.'/modules/slider/js/jquery.ennui.contentslider.js" type="text/javascript"></script>
<link rel="stylesheet" media="screen" href="'.XOOPS_URL.'/modules/slider/css/slider.css" type="text/css" />');
	}
	include_once (XOOPS_ROOT_PATH.'/modules/slider/class/sliders.php');
	
	$sliders = new Sliders();
	$myslider = $sliders->getActiveSlider();
	
	$xoopsTpl->assign('sliderlist',$myslider);
	
	include_once XOOPS_ROOT_PATH.'/footer.php';
?>