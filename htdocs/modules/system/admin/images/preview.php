<?php
include('../../../../mainfile.php');
include("../../../../libraries/wideimage/lib/WideImage.inc.php");
include("../../../../kernel/image.php");
include("../../../../kernel/imagecategory.php");

$file = $_GET['file'];
$resize = isset($_GET['resize'])?$_GET['resize']:1;

$image_handler = xoops_gethandler('image');
$imgcat_handler = xoops_gethandler('imagecategory');

$image =& $image_handler->getObjects(new Criteria('image_name', $file),false,true);
$imagecategory =& $imgcat_handler->get($image[0]->getVar('imgcat_id'));
$folder = XOOPS_UPLOAD_PATH.'/'.$imagecategory->getVar('imgcat_foldername');
if ($imagecategory->getVar('imgcat_storetype') == 'db') {
	$img = wiImage::loadFromString($image[0]->getVar('image_body'));
}else{
	$img = wiImage::load($folder.'/'.$file);
}
$width = $img->getWidth();
$height = $img->getHeight();

header('Content-type: image/png');
if ($resize && ($width > 400 || $height > 300)){
    echo $img->resize(400, 300)->asString('png');	
}else{
	echo $img->asString('png');
}

?>