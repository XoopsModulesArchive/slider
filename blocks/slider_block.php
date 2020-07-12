<?php
function slider_block($params)
{
	global $xoTheme, $xoopsOption, $xoopsTpl,$xoopsConfig,$xoopsDB;
	include_once(XOOPS_ROOT_PATH.'/modules/slider/class/sliders.php');
	
	
		
	
	if ( file_exists(XOOPS_ROOT_PATH."/modules/slider/language/".$xoopsConfig['language']."/main.php") ) {
		include_once (XOOPS_ROOT_PATH."/modules/slider/language/".$xoopsConfig['language']."/main.php");
	} else {
		include_once (XOOPS_ROOT_PATH."/modules/slider/language/english/main.php");
	}
	
	
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
	
	
	$sliders = new Sliders();
	$myslider = $sliders->getActiveSlider();
	include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

       /* $blockObj = new XoopsBlock($arr['bid']);
		$blockObj->buildTitle($arr['title']. $arr['title'].'ssss');
	echo $arr['title'];*/
	
	$xoopsTpl->assign('sliderlist',$myslider);
	$xoopsTpl->assign('more',_AM_SLIDER_READMORE);
	
	return true;
}
?>