<?php
/**
 * PHP Highlighter TextSanitizer plugin
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @author	    Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
 * @since		1.2
 * @package		plugins
 * @version		$Id$
 */
function textsanitizer_syntaxhighlightphp(&$ts, $text)
{
	$patterns[] = "/\[code_php](.*)\[\/code_php\]/esU";
	$replacements[] = "textsanitizer_geshi_php_highlight( '\\1' )";
	return preg_replace($patterns, $replacements, $text);
}
function textsanitizer_geshi_php_highlight( $source )
{
	if ( !@include_once ICMS_LIBRARIES_PATH . '/geshi/geshi.php' ) return false;
	$source = MyTextSanitizer::undoHtmlSpecialChars($source);

    // Create the new GeSHi object, passing relevant stuff
    $geshi = new GeSHi($source, 'php');
    // Enclose the code in a <div>
    $geshi->set_header_type(GESHI_HEADER_NONE);

	// Sets the proper encoding charset other than "ISO-8859-1"
    $geshi->set_encoding(_CHARSET);

	$geshi->set_link_target ( "_blank" );

    // Parse the code
    $code = $geshi->parse_code();
	$code = "<div class=\"icmsCodePhp\"><code><pre>".$code."</pre></code></div>";
    return $code;
}
function javascript_syntaxhighlightphp($ele_name)
{
        $code = "<img onclick='javascript:icmsCodePHP(\"".$ele_name."\", \"".htmlspecialchars(_ENTERPHPCODE, ENT_QUOTES)."\");' onmouseover='style.cursor=\"hand\"' src='".ICMS_URL."/plugins/textsanitizer/".basename(dirname(__FILE__))."/php.png' alt='php' />&nbsp;";
		$javascript='';
        return array($code, $javascript);
}
function stlye_syntaxhighlightphp(){
echo'<style type="text/css">
</style>';
}
?>