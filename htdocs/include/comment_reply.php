<?php
/**
* The commentreply include file
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	core
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id$
*/

if (!defined('XOOPS_ROOT_PATH')) {
	die("ImpressCMS root path not defined");
}
include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
if ( ('system' != $xoopsModule->getVar('dirname') && XOOPS_COMMENT_APPROVENONE == $xoopsModuleConfig['com_rule']) || (!is_object($xoopsUser) && !$xoopsModuleConfig['com_anonpost']) || !is_object($xoopsModule) ) {
	redirect_header(XOOPS_URL . '/user.php', 1, _NOPERM);
}

icms_loadLanguageFile('core', 'comment');
$com_id = isset($_GET['com_id']) ? intval($_GET['com_id']) : 0;
$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
if ($com_mode == '') {
	if (is_object($xoopsUser)) {
		$com_mode = $xoopsUser->getVar('umode');
	} else {
		$com_mode = $xoopsConfig['com_mode'];
	}
}
if (!isset($_GET['com_order'])) {
	if (is_object($xoopsUser)) {
		$com_order = $xoopsUser->getVar('uorder');
	} else {
		$com_order = $xoopsConfig['com_order'];
	}
} else {
	$com_order = intval($_GET['com_order']);
}
$comment_handler =& xoops_gethandler('comment');
$comment =& $comment_handler->get($com_id);
$r_name = XoopsUser::getUnameFromId($comment->getVar('com_uid'));
$r_text = _CM_POSTER.': <b>'.$r_name.'</b>&nbsp;&nbsp;'._CM_POSTED.': <b>'.formatTimestamp($comment->getVar('com_created')).'</b><br /><br />'.$comment->getVar('com_text');$com_title = $comment->getVar('com_title', 'E');
if (!preg_match("/^(Re|"._CM_RE."):/i", $com_title)) {
	$com_title = _CM_RE.": ".xoops_substr($com_title, 0, 56);
}
$com_pid = $com_id;
$com_text = '';
$com_id = 0;
$dosmiley = 1;
$groups   = (is_object($xoopsUser)) ? $xoopsUser->getGroups() : ICMS_GROUP_ANONYMOUS;
$gperm_handler =& xoops_gethandler('groupperm');
if ($xoopsConfig ['editor_default'] != 'dhtmltextarea' && $gperm_handler->checkRight('use_wysiwygeditor', 1, $groups, 1, false)) {
  $dohtml = 1;
  $dobr = 0;
}else{
  $dohtml = 0;
  $dobr = 1;
}
$doxcode = 1;
$doimage = 1;
$com_icon = '';
$com_rootid = $comment->getVar('com_rootid');
$com_itemid = $comment->getVar('com_itemid');
include XOOPS_ROOT_PATH.'/header.php';
themecenterposts($comment->getVar('com_title'), $r_text);
include XOOPS_ROOT_PATH.'/include/comment_form.php';
include XOOPS_ROOT_PATH.'/footer.php';
?>