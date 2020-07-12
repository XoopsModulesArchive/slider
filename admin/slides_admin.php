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
	addNewSlide();
	break;
case "editSlide":
	editSlide();
	break;
case "submitnew":
	submitNewSlide();
	break;
case "submitedit":
	submitEditSlide();
	break;
case "delete": //ajax
	delSlide();
	break;
default:
	slidesIndex();
	break;
}

function slidesIndex() 
{

$myts =& MyTextSanitizer::getInstance();
include_once XOOPS_ROOT_PATH.'/class/pagenav.php';

xoops_cp_header();

global $xoTheme, $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
include_once ('../../../class/theme.php');

if (!isset($xoopsTpl) || !is_object($xoopsTpl)) {
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

$sid = $_REQUEST['sid'];
$sliders = new Sliders();
$sliderlist = $sliders->GetSliderbyID($sid);
$xoopsTpl->assign('totalcount', count($sliderlist[0]['slides']));
//print_r($sliderlist);
$xoopsTpl->assign('sliderlist',$sliderlist);
$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slides_admin_index.html');
xoops_cp_footer();
}

function addNewSlide()
{
	xoops_cp_header();
	global $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
	include_once ('../class/sliders.php');
	
	$sid = $_REQUEST['sid'];
	include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

	$submit_form = new XoopsThemeForm(_AM_SLIDER_NEW, "submitform", "slides_admin.php?op=submitnew", "post", true);
	$submit_form->setExtra('enctype="multipart/form-data"');
	
	$title = new XoopsFormText(_AM_SLIDER_TITLE, "title", 90, 255);
	$description = new XoopsFormDhtmlTextArea(_AM_SLIDER_DESCRIPTION, 'description', '', 37, 35);
	$img = new XoopsFormFile(_AM_SLIDES_IMAGE,'slide_image',4194304);
	
	$img_tray = new XoopsFormElementTray(_AM_SLIDES_IMAGE, '&nbsp;');
	$img_tray->addElement($img, false);
	
	$slider_id = new XoopsFormHidden("slider_id", $sid);
	$submit_button = new XoopsFormButton("", "submit", _SUBMIT, "submit");
	
	$submit_form->addElement($title, true);
	$submit_form->addElement($slider_id, false);
	$submit_form->addElement($description, false);
	$submit_form->addElement($img_tray, false);
	
	$submit_form->addElement($submit_button);
	
	$xoopsTpl->assign('form',$submit_form->render());
	
	$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
	$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_add_form.html');
	xoops_cp_footer();		
}

function submitNewSlide()
{
	global $xoopsDB, $xoopsModule, $_POST, $myts;
	$myts =& MyTextSanitizer::getInstance();
	$sid = intval($_REQUEST['slider_id']);
	$title = $myts->undohtmlSpecialChars($_POST['title']);
	$description = $myts->undohtmlSpecialChars($_POST['description']);
	
	include_once XOOPS_ROOT_PATH.'/class/uploader.php';
	include_once (XOOPS_ROOT_PATH.'/modules/slider/includes/gd_functions.php');

	$destination_path = XOOPS_ROOT_PATH."/modules/slider/uploads/";
	$allowed_mimetypes = array('image/gif', 'image/jpeg','image/png');
	
	
	if(function_exists("gd_info"))
	{
		$maxfilesize = 10094304;
		$maxfilewidth = 6000;
		$maxfileheight = 6000;
	}
	else
	{
		$maxfilesize = 4194304;
		$maxfilewidth = 1024;
		$maxfileheight = 800;
	}
  	$uploader = new XoopsMediaUploader($destination_path, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
	
	if(file_exists($destination_path.$_FILES['slide_image']['name']))
	{	
		$new_name = rand().$_FILES['slide_image']['name'];
		$uploader->setTargetFileName($new_name);
	}
	
	if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) 
	{
		if (!$uploader->upload())
		{
			$result =  $uploader->getErrors();
			$imagename="";
		}
		else
			$imagename=$uploader->getSavedFileName();
			makeThumbnail($destination_path.$imagename,460,345,false);
	}
	
	$sqlinsert="INSERT INTO ".$xoopsDB->prefix("slider_content")." (sid, title, description, image) VALUES ($sid, '$title', '$description', '$imagename')";
	
	if ( !$result = $xoopsDB->query($sqlinsert) )
	{
		echo _AM_SLIDER_ERROR_DB;
		return;
	}
	else 
	    redirect_header("index.php",1,_AM_SLIDER_DB_UPDATED);
}

function editSlide()
{
	xoops_cp_header();
	global $xoopsConfig, $xoopsModuleConfig, $xoopsModule, $xoopsDB,$xoopsTpl;
	include_once ('../class/sliders.php');
	
	$sliders = new Sliders();
	$mysliders = $sliders->getAllSliders();
	
	$sliders = new Sliders();
	
	$sid= isset($_REQUEST['id'])? intval($_REQUEST['id']):null;
	 
	$mysliders = $sliders->GetSlidebyID($sid);
	include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
	 
	$submit_form = new XoopsThemeForm(_AM_SLIDER_EDIT_INFO, "submitform", "slides_admin.php?op=submitedit", "post", true);
	$submit_form->setExtra('enctype="multipart/form-data"');
	
	$title = new XoopsFormText(_AM_SLIDER_TITLE, "title", 90, 255,$mysliders[0]['title']);
	$description = new XoopsFormDhtmlTextArea(_AM_SLIDER_DESCRIPTION, 'description',$mysliders[0]['description'], 37, 35);
	
	$img = new XoopsFormFile(_AM_SLIDES_IMAGE,'slide_image',4194304);
	
	$image = new XoopsFormLabel('', '<img src="/modules/slider/uploads/'.$mysliders[0]['image'].'" alt="" width="100" />');
	
	$img_tray = new XoopsFormElementTray(_AM_SLIDES_IMAGE, '&nbsp;');
	$img_tray->addElement($image, false);
	$img_tray->addElement($img, false);
	

	$slide_id = new XoopsFormHidden("slide_id", $sid);
	$slider_id = new XoopsFormHidden("slider_id", $mysliders[0]['sid']);
	$oldimage_name = new XoopsFormHidden("imagename", $mysliders[0]['image']);
	$submit_button = new XoopsFormButton("", "submit", _SUBMIT, "submit");
	
	$submit_form->addElement($title, true);
	$submit_form->addElement($slide_id, false);
	$submit_form->addElement($slider_id, false);
	$submit_form->addElement($description, false);
	$submit_form->addElement($img_tray, false);
	$submit_form->addElement($oldimage_name, false);
	
	$submit_form->addElement($submit_button);
	
	$xoopsTpl->assign('form',$submit_form->render());
	
	$xoopsTpl->assign('adminheader',XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_header.html');
	$xoopsTpl->display(XOOPS_ROOT_PATH.'/modules/slider/templates/admin/slider_admin_add_form.html');
	xoops_cp_footer();			
}

function submitEditSlide()
{
	global $xoopsDB, $xoopsModule, $_REQUEST, $myts;
	$myts =& MyTextSanitizer::getInstance();
	$id = $_POST['slide_id'];
	$sid = $_POST['slider_id'];
	$title = $myts->undohtmlSpecialChars($_POST['title']);
	$description = $myts->undohtmlSpecialChars($_POST['description']);
	
	include_once XOOPS_ROOT_PATH.'/class/uploader.php';
	include_once (XOOPS_ROOT_PATH.'/modules/slider/includes/gd_functions.php');

	
	$destination_path = XOOPS_ROOT_PATH."/modules/slider/uploads/";
	$allowed_mimetypes = array('image/gif', 'image/jpeg','image/png');
	if(function_exists("gd_info"))
	{
		$maxfilesize = 10094304;
		$maxfilewidth = 6000;
		$maxfileheight = 6000;
	}
	else
	{
		$maxfilesize = 4194304;
		$maxfilewidth = 1024;
		$maxfileheight = 800;
	}
  	$uploader = new XoopsMediaUploader($destination_path, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
	
	if(file_exists($destination_path.$_FILES['slide_image']['name']))
	{	
		$new_name = rand().$_FILES['slide_image']['name'];
		$uploader->setTargetFileName($new_name);
	}
	
	if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) 
	{
		
		if (!$uploader->upload())
		{
			$result =  $uploader->getErrors();
			$imagename="";
		}
		else
		{
			$oldimagename = $_POST['imagename'];
			$imagename=$uploader->getSavedFileName();
			makeThumbnail($destination_path.$imagename,460,345,false);
			
			unlink($destination_path.$oldimagename);
			$sqlinsert = "UPDATE ".$xoopsDB->prefix("slider_content")." SET  title='$title', description='$description', image='$imagename' WHERE id=".$id;
		}
	}
	
	else
		$sqlinsert = "UPDATE ".$xoopsDB->prefix("slider_content")." SET  title='$title', description='$description' WHERE id=".$id;
	
	if ( !$result = $xoopsDB->query($sqlinsert) )
	{
		echo _AM_SLIDER_ERROR_DB;
		return;
	}
	else
	  redirect_header("slides_admin.php?sid=$sid",1,_AM_SLIDER_DB_UPDATED);
}
 
function delSlide($id=null)
{
	global $xoopsDB;
	include_once ('../class/sliders.php');
	$sliders = new Sliders();
	
	$id= isset($_REQUEST['id'])? intval($_REQUEST['id']):$id;
	 
	$mysliders = $sliders->GetSlidebyID($id);
	
	$sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("slider_content"), $id);
   	$xoopsDB->queryf($sql) or die(mysql_error());
	
	$imagename = $mysliders[0]['image'];
	$destination_path = XOOPS_ROOT_PATH."/modules/slider/uploads/";
	unlink($destination_path.$imagename);
	exit();
}


?>