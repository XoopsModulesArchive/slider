<?php
// includes
include ('../../../include/cp_header.php');
if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
	include ("../language/".$xoopsConfig['language']."/main.php");
} else {
	include ("../language/english/main.php");
}
if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
		include_once(XOOPS_ROOT_PATH."/class/template.php");
		$xoopsTpl = new XoopsTpl();
	}
if(!isset($_POST['op'])) {
	$op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
	$op = $_POST['op'];
}

switch ($op) {
case "new":
	addNewSlider();
	break;
case "editSlider":
	editSlider();
	break;
case "submitnew":
	submitNewSlider();
	break;
case "submitedit":
	submitEditSlider();
	break;
case "delete": //ajax
	delSlider();
	break;
case "setactive": //ajax
	setActiveSlider();
	break;
default:
	addNewSlider();
	break;
}


function addNewSlider()
{
	xoops_cp_header();
	global $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
	include_once ('../class/sliders.php');
	
	$sliders = new Sliders();
	$mysliders = $sliders->getAllSliders();
	
	$id= isset($_GET['id'])? intval($_GET['id']):null;
	
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

	$submit_form = new XoopsThemeForm(_AM_SLIDER_NEW, "submitform", "sliders_admin.php?op=submitnew", "post", true);

	$title = new XoopsFormText(_AM_SLIDER_TITLE, "title", 90, 255);
	$description = new XoopsFormDhtmlTextArea(_AM_SLIDER_DESCRIPTION, 'description', '', 37, 35);
	$date = new XoopsFormTextDateSelect(_AM_SLIDER_DATE,'mydate');
	
	$slider_id = new XoopsFormHidden("slider_id", $id);
	$submit_button = new XoopsFormButton("", "submit", _SUBMIT, "submit");
	
	$submit_form->addElement($title, true);
	$submit_form->addElement($slider_id, false);
	$submit_form->addElement($description, false);
	$submit_form->addElement($date, true);
	
	$submit_form->addElement($submit_button);
	
	$xoopsTpl->assign('form',$submit_form->render());
	
	$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
	$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_add_form.html');
	xoops_cp_footer();		
}

function submitNewSlider()
{
	global $xoopsDB, $xoopsModule, $_POST, $myts;
	$myts =& MyTextSanitizer::getInstance();

	$title = $myts->htmlSpecialChars($_POST['title']);
	$description = $myts->htmlSpecialChars($_POST['description']);
	$date = strtotime($_REQUEST['mydate']);
	
	$sqlinsert="INSERT INTO ".$xoopsDB->prefix("slider")." (title, description, mydate) VALUES ('$title', '$description', '$date')";
	
	if ( !$result = $xoopsDB->query($sqlinsert) )
	{
		echo _AM_SLIDER_ERROR_DB;
		return;
	}
	else
	    redirect_header("index.php",1,_AM_SLIDER_DB_UPDATED);
}

function editSlider()
{
	xoops_cp_header();
	global $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
	include_once ('../class/sliders.php');
	
	$sliders = new Sliders();
	$mysliders = $sliders->getAllSliders();
	
	$sliders = new Sliders();
	
	$id= isset($_REQUEST['id'])? intval($_REQUEST['id']):null;
	
	$mysliders = $sliders->GetSliderbyID($id);
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

	$submit_form = new XoopsThemeForm(_AM_SLIDER_EDIT_INFO, "submitform", "sliders_admin.php?op=submitedit", "post", true);
	
	$title = new XoopsFormText(_AM_SLIDER_TITLE, "title", 90, 255,$mysliders[0]['title']);
	$description = new XoopsFormDhtmlTextArea(_AM_SLIDER_DESCRIPTION, 'description',$mysliders[0]['description'], 37, 35);
	$date = new XoopsFormTextDateSelect(_AM_SLIDER_DATE,'mydate',15,$mysliders[0]['timestamp']);
	
	$slider_id = new XoopsFormHidden("slider_id", $id);
	$submit_button = new XoopsFormButton("", "submit", _SUBMIT, "submit");
	
	$submit_form->addElement($title, true);
	$submit_form->addElement($slider_id, false);
	$submit_form->addElement($description, false);
	$submit_form->addElement($date, true);
	
	$submit_form->addElement($submit_button);
	
	$xoopsTpl->assign('form',$submit_form->render());
	
	$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
	$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_add_form.html');
	xoops_cp_footer();			
}

function submitEditSlider()
{
	global $xoopsDB, $xoopsModule, $_REQUEST, $myts;
	$myts =& MyTextSanitizer::getInstance();
	$id = $_POST['slider_id'];
	$title = $myts->htmlSpecialChars($_REQUEST['title']);
	$description = $myts->htmlSpecialChars($_REQUEST['description']);
	$date = strtotime($_REQUEST['mydate']);
	
	$xoopsDB->query("UPDATE ".$xoopsDB->prefix("slider")." SET  title='$title', description='$description', mydate='$date' WHERE id=".$id) or die(mysql_error());
	redirect_header("index.php?id=$id",1,_AM_SLIDER_DB_UPDATED);
}
 
function delSlider()
{
	global $xoopsDB;
	include_once ('../class/sliders.php');
	$sliders = new Sliders();
	$id= isset($_REQUEST['id'])? intval($_REQUEST['id']):null;
	$active_slider_id = $sliders->getActiveSliderID();
	$sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("slider"), $id);
   	$xoopsDB->queryf($sql) or die(mysql_error());
	
	
	$mysliders = $sliders->GetSliderbyID($id);
	$destination_path = XOOPS_ROOT_PATH."/modules/slider/uploads/";
	
	foreach($mysliders[0]['slides'] as $slide)
	{
		echo $slide['id'].' '.$slide['image'];
		$sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("slider_content"), $slide['id']);
   		$xoopsDB->queryf($sql) or die(mysql_error());
		unlink($destination_path.$slide['image']);
	}
	if($active_slider_id==$id)
	{
		$myslider = $sliders->getAllSliders(0,1);
		
		$xoopsDB->query("UPDATE ".$xoopsDB->prefix("slider")." SET active=1 WHERE id=".$myslider[0]['id']) or die(mysql_error());
	}
	exit();
}

function setActiveSlider()
{
	global $xoopsDB;
	include_once ('../class/sliders.php');
	$id= isset($_REQUEST['id'])? intval($_REQUEST['id']):null;
	$sliders = new Sliders();
	$activeslider = $sliders->getActiveSlider();
 	//set old active slider inactive
	if(!empty($activeslider))
		$xoopsDB->query("UPDATE ".$xoopsDB->prefix("slider")." SET  active=0 WHERE id=".$activeslider['id']) or die(mysql_error());
	//set new id as active
	$xoopsDB->query("UPDATE ".$xoopsDB->prefix("slider")." SET active=1 WHERE id=".$id) or die(mysql_error());
  
	exit();
}

?>