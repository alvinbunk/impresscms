<?php
/**
 * Hidden Content TextSanitizer plugin
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @author	    Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
 * @since		1.2
 * @package		plugins
 * @version		$Id$
 */
function textsanitizer_hiddencontent(&$ts, $text)
{
        	$patterns[] = "/\[hide](.*)\[\/hide\]/sU";
        	$patterns[] = "/\[سینا](.*)\[\/سینا\]/sU";
			if($_SESSION['xoopsUserId'])
			{$replacements[] = _HIDDENC.'<div class="xoopsQuote">\\1</div>';}
			else{$replacements[] = _HIDDENC.'<div class="xoopsQuote">'._HIDDENTEXT.'</div>';}
			$replacements[] = '<div class="xoopsQuote">\\1</div>';
	return preg_replace($patterns, $replacements, $text);
}
?>