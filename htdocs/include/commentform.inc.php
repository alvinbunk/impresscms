<?php
/**
 * The commentform include file
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

if (!defined("ICMS_ROOT_PATH")) {
	die("ImpressCMS root path not defined");
}
include_once ICMS_ROOT_PATH . '/class/xoopslists.php';
include ICMS_ROOT_PATH . '/class/xoopsformloader.php';
$cform = new XoopsThemeForm(_CM_POSTCOMMENT, "commentform", "postcomment.php", "post", true);
if (!preg_match("/^re:/i", $subject)) {
	$subject = "Re: " . icms_substr($subject,0,56);
}
$cform->addElement(new XoopsFormText(_CM_TITLE, 'subject', 50, 255, $subject), true);
$icons_radio = new XoopsFormRadio(_MESSAGEICON, 'icon', $icon);
$subject_icons = IcmsLists::getSubjectsList();
foreach ($subject_icons as $iconfile) {
	$icons_radio->addOption($iconfile, '<img src="' . ICMS_URL . '/images/subject/' . $iconfile . '" alt="" />');
}
$cform->addElement($icons_radio);
$cform->addElement(new icms_form_elements_Dhtmltextarea(_CM_MESSAGE, 'message', $message, 10, 50), true);
$option_tray = new icms_form_elements_Tray(_OPTIONS,'<br />');
if ($icmsUser) {
	if ($icmsConfig['anonpost'] == true) {
		$noname_checkbox = new icms_form_elements_Checkbox('', 'noname', $noname);
		$noname_checkbox->addOption(1, _POSTANON);
		$option_tray->addElement($noname_checkbox);
	}
	if ($icmsUser->isAdmin($icmsModule->getVar('mid'))) {
		$nohtml_checkbox = new icms_form_elements_Checkbox('', 'nohtml', $nohtml);
		$nohtml_checkbox->addOption(1, _DISABLEHTML);
		$option_tray->addElement($nohtml_checkbox);
	}
}
$smiley_checkbox = new icms_form_elements_Checkbox('', 'nosmiley', $nosmiley);
$smiley_checkbox->addOption(1, _DISABLESMILEY);
$option_tray->addElement($smiley_checkbox);

$cform->addElement($option_tray);
$cform->addElement(new XoopsFormHidden('pid', (int) ($pid)));
$cform->addElement(new XoopsFormHidden('comment_id', (int) ($comment_id)));
$cform->addElement(new XoopsFormHidden('item_id', (int) ($item_id)));
$cform->addElement(new XoopsFormHidden('order', (int) ($order)));
$button_tray = new icms_form_elements_Tray('' ,'&nbsp;');
$button_tray->addElement(new icms_form_elements_Button('', 'preview', _PREVIEW, 'submit'));
$button_tray->addElement(new icms_form_elements_Button('', 'post', _CM_POSTCOMMENT, 'submit'));
$cform->addElement($button_tray);
$cform->display();
