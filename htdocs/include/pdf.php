<?php
/**
* PDF generator
*
* System tool that allow create PDF's within ImpressCMS core
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package	core
* @since	1.1
* @author		Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
* @version	$Id$
*/
if(!defined('ICMS_ROOT_PATH')) {exit();}

function Generate_PDF($content, $doc_title, $doc_keywords)
{
	global $xoopsConfig;
	require_once ICMS_PDF_LIB_PATH.'/tcpdf.php';
	if(file_exists(ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/pdf.php'))
	{
		include_once ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/pdf.php';
	}
	else {include_once ICMS_ROOT_PATH.'/language/english/pdf.php';}

	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(PDF_AUTHOR);
	$pdf->SetTitle($doc_title);
	$pdf->SetSubject($doc_title);
	$pdf->SetKeywords($doc_keywords);
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->setLanguageArray($l); //set language items
	// set font
	if(defined("_PDF_LOCAL_FONT") && _PDF_LOCAL_FONT && file_exists(ICMS_PDF_LIB_PATH.'/fonts/'._PDF_LOCAL_FONT.'.php'))
	{
		$pdf->SetFont(_PDF_LOCAL_FONT);
	}
	else {$pdf->SetFont('dejavusans');}
	//initialize document
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->writeHTML($content, true, 0);
	return $pdf->Output();
}
?>