<?php
/**
* Admin control panel entry page
*
* This page is responsible for
* - displaying the home of the Control Panel
* - checking for cache/adminmenu.php
* - displaying RSS feed of the ImpressCMS Project
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		core
* @since		XOOPS
* @author		http://www.xoops.org The XOOPS Project
* @author		modified by marcan <marcan@impresscms.org>
* @version		$Id$
*/

$xoopsOption['pagetype'] = 'admin';
/** Including mainfile.php is required */
include 'mainfile.php';
/** Include the control panel functions */
include ICMS_ROOT_PATH.'/include/cp_functions.php';

// Admin Authentication
if($xoopsUser)
{
	if(!$xoopsUser->isAdmin(-1)) {redirect_header('index.php',2,_AD_NORIGHT);}
}
else {redirect_header('index.php',2,_AD_NORIGHT);}
// end Admin Authentication

$op = isset($_GET['rssnews']) ? intval($_GET['rssnews']) : 0;
if(!empty($_GET['op'])) {$op = intval($_GET['op']);}
if(!empty($_POST['op'])) {$op = intval($_POST['op']);}

	$sess_handler =& xoops_gethandler('session');
	$sess_handler->securityLevel = 3;
	$sess_handler->check_ip_blocks = 2;
	$sess_handler->salt_key = XOOPS_DB_SALT;
	if($_SESSION['reg_i']) {$sess_handler->enableRegenerateId = true;}
	$sess_handler->icms_sessionOpen();
	$_SESSION['reg_i'] = true;


if(!file_exists(ICMS_CACHE_PATH.'/adminmenu_'.$xoopsConfig['language'].'.php') && $op != 2)
{
	xoops_header();
	xoops_confirm(array('op' => 2), 'admin.php', _RECREATE_ADMINMENU_FILE);
	xoops_footer();
	exit();
}

switch($op)
{
	case 1:
		xoops_cp_header();
		showRSS(1);
	break;
	case 2:
		xoops_module_write_admin_menu(impresscms_get_adminmenu());
		redirect_header('javascript:history.go(-1)', 1, _AD_LOGINADMIN);
	break;
/*	case 10:
		$rssurl = 'http://www.impresscms.org/modules/smartsection/backend.php?categoryid=1';
		$rssfile = ICMS_CACHE_PATH.'/www_smartsection_category1.xml';
		$caching_time = 1;
		$items_to_display = 1;
		
		$rssdata = '';
		if(!file_exists($rssfile) || filemtime($rssfile) < time() - $caching_time)
		{
			require_once ICMS_ROOT_PATH.'/class/snoopy.php';
			$snoopy = new Snoopy;
			if($snoopy->fetch($rssurl))
			{
				$rssdata = $snoopy->results;
				if(false !== $fp = fopen($rssfile, 'w')) {fwrite($fp, $rssdata);}
				fclose($fp);
	        	}
		}
		else
		{
			if(false !== $fp = fopen($rssfile, 'r'))
			{
				while(!feof ($fp)) {$rssdata .= fgets($fp, 4096);}
				fclose($fp);
			}
		}
		if($rssdata != '')
		{
			include_once ICMS_ROOT_PATH.'/class/xml/rss/xmlrss2parser.php';
			$rss2parser = new XoopsXmlRss2Parser($rssdata);
			if(false != $rss2parser->parse())
			{
				$items =& $rss2parser->getItems();
				$count = count($items);
				$myts =& MyTextSanitizer::getInstance();
				for($i = 0; $i < $items_to_display; $i++)
				{
					?>
					<div>
						<img style="vertical-align: middle;" src="<?php ICMS_URL?>/modules/smartsection/images/icon/doc.png" alt="<?php $items[$i]['title']?>">&nbsp;<a href="<?php $items[$i]['guid']?>"><?php $items[$i]['title']?></a>
					</div>
					<div>
						<img class="smartsection_item_image" src="<?php ICMS_URL?>/uploads/smartsection/images/item/impresscms_news.gif" alt="<?php $items[$i]['title']?>" title="<?php $items[$i]['title']?>" align="'._GLOBAL_RIGHT.'">
						<?php $items[$i]['description']?>
					</div>
					<div style="clear: both;">&nbsp;</div>
					<div style="font-size: 10px; text-align: right;"><a href="http://www.impresscms.org/modules/smartsection/category.php?categoryid=1">All ImpressCMS Project news...</a></div>
					<?php
				}
			}
			else
			{
				//echo $rss2parser->getErrors();
			}
		}
	break;*/
	default:
		$mods = xoops_cp_header(1);

		// ###### Output warn messages for security  ######
		if(is_dir(ICMS_ROOT_PATH.'/install/'))
		{
			xoops_error(sprintf(_WARNINSTALL2,ICMS_ROOT_PATH.'/install/'));
			echo '<br />';
		}
		$db = $GLOBALS['xoopsDB'];

		if(getDbValue($db, 'modules', 'version', 'version="120"') == 0 AND getDbValue($db, 'modules', 'mid', 'mid="1"') == 1)
		{
			xoops_error('<a href="'.ICMS_URL.'/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=system">'._WARNINGUPDATESYSTEM.'</a>');
			echo '<br />';
		}
		if(is_writable(ICMS_ROOT_PATH.'/mainfile.php'))
		{
			xoops_error(sprintf(_WARNINWRITEABLE,ICMS_ROOT_PATH.'/mainfile.php'));
			echo '<br />';
		}
		if(is_dir(ICMS_ROOT_PATH.'/upgrade/'))
		{
			xoops_error(sprintf(_WARNINSTALL2,ICMS_ROOT_PATH.'/upgrade/'));
			echo '<br />';
		}
/*		if(!is_dir(XOOPS_TRUST_PATH))
		{
			xoops_error(_TRUST_PATH_HELP);
			echo '<br />';
		}*/
		$sql1 = "SELECT conf_modid FROM `".$xoopsDB->prefix('config')."` WHERE conf_name = 'dos_skipmodules'";
		if($result1 = $xoopsDB->query($sql1))
		{
			list($modid) = $xoopsDB->FetchRow($result1);
			$protector_is_active = '0';
			if (!is_null($modid)){
			$sql2 = "SELECT isactive FROM `".$xoopsDB->prefix('modules')."` WHERE mid =".$modid;
			$result2 = $xoopsDB->query($sql2);
			list($protector_is_active) = $xoopsDB->FetchRow($result2);
			}
		}
		if($protector_is_active == 0)
		{
			xoops_error(_PROTECTOR_NOT_FOUND);
			echo '<br />';
		}
		// ###### Output warn messages for correct functionality  ######
		if(!is_writable(ICMS_CACHE_PATH))
		{
			xoops_warning(sprintf(_WARNINNOTWRITEABLE,ICMS_CACHE_PATH));
			echo '<br />';
		}
		if(!is_writable(ICMS_UPLOAD_PATH))
		{
			xoops_warning(sprintf(_WARNINNOTWRITEABLE,ICMS_UPLOAD_PATH));
			echo '<br />';
		}
		if(!is_writable(ICMS_COMPILE_PATH))
		{
			xoops_warning(sprintf(_WARNINNOTWRITEABLE,ICMS_COMPILE_PATH));
			echo '<br />';
		}
	
		$icmsAdminTpl->assign('lang_cp', _CPHOME);
		$icmsAdminTpl->assign('lang_insmodules', _AD_INSTALLEDMODULES);
		// Loading allowed Modules
		$icmsAdminTpl->assign('modules', $mods);

		if(count($mods) > 0) {$icmsAdminTpl->assign('modulesadm', 1);}
		else {$icmsAdminTpl->assign('modulesadm', 0);}

		// Loading System Configuration Links
		$groups = $xoopsUser->getGroups();
		$all_ok = false;
		if(!in_array(XOOPS_GROUP_ADMIN, $groups))
		{
			$sysperm_handler =& xoops_gethandler('groupperm');
			$ok_syscats =& $sysperm_handler->getItemIds('system_admin', $groups);
		}
		else {$all_ok = true;}
	
		require_once ICMS_ROOT_PATH.'/class/xoopslists.php';
		require_once ICMS_ROOT_PATH.'/modules/system/constants.php';
	
		$admin_dir = ICMS_ROOT_PATH.'/modules/system/admin';
		$dirlist = XoopsLists::getDirListAsArray($admin_dir);
	
		if(file_exists(ICMS_ROOT_PATH.'/modules/system/language/'.$xoopsConfig['language'].'/admin.php'))
		{
			include ICMS_ROOT_PATH.'/modules/system/language/'.$xoopsConfig['language'].'/admin.php';
		}
		elseif(file_exists(ICMS_ROOT_PATH.'/modules/system/language/english/admin.php'))
		{
			include ICMS_ROOT_PATH.'/modules/system/language/english/admin.php';
		}
	
		$cont = 0;
		asort($dirlist);
		foreach($dirlist as $file)
		{
			include $admin_dir.'/'.$file.'/xoops_version.php';
			if($modversion['hasAdmin'])
			{
				$category = isset($modversion['category']) ? intval($modversion['category']) : 0;
				if(false != $all_ok || in_array($modversion['category'], $ok_syscats))
				{
					$sysmod = array('title' => $modversion['name'], 'link' => ICMS_URL.'/modules/system/admin.php?fct='.$file, 'image' => ICMS_URL.'/modules/system/admin/'.$file.'/images/'.$file.'_big.png');
					$icmsAdminTpl->append('sysmod', $sysmod);
					$cont++;
				}
			}
			unset($modversion);
		}
		if($cont > 0) {$icmsAdminTpl->assign('systemadm', 1);}
		else {$icmsAdminTpl->assign('systemadm', 0);}
	
		echo $icmsAdminTpl->fetch(ICMS_ROOT_PATH.'/modules/system/templates/admin/system_indexcp.html');
	break;
}

function showRSS($op=1)
{
	switch($op)
	{
		case 1:
			$config_handler =& xoops_gethandler('config');
			$xoopsConfigPersona =& $config_handler->getConfigsByCat(XOOPS_CONF_PERSONA);
			$rssurl = $xoopsConfigPersona['rss_local'];
			$rssfile = ICMS_CACHE_PATH.'/adminnews_'._LANGCODE.'.xml';
		break;
	}
	$rssdata = '';
	if(!file_exists($rssfile) || filemtime($rssfile) < time() - 86400)
	{
		require_once ICMS_ROOT_PATH.'/class/snoopy.php';
        	$snoopy = new Snoopy;
        	if($snoopy->fetch($rssurl))
		{
            		$rssdata = $snoopy->results;
            		if(false !== $fp = fopen($rssfile, 'w')) {fwrite($fp, $rssdata);}
            		fclose($fp);
        	}
	}
	else
	{
		if(false !== $fp = fopen($rssfile, 'r'))
		{
			while(!feof ($fp)) {$rssdata .= fgets($fp, 4096);}
			fclose($fp);
		}
	}
/*	if($rssdata != '')
	{
		include_once ICMS_ROOT_PATH.'/class/xml/rss/xmlrss2parser.php';
		$rss2parser = new XoopsXmlRss2Parser($rssdata);
		if(false != $rss2parser->parse())
		{
			echo '<table class="outer" width="100%">';
			$items =& $rss2parser->getItems();
			$count = count($items);
			$myts =& MyTextSanitizer::getInstance();
			for($i = 0; $i < $count; $i++)
			{
				echo '<tr class="head"><td><a href="'.htmlspecialchars($items[$i]['link']).'" rel="external">';
				echo htmlspecialchars($items[$i]['title']).'</a> ('.htmlspecialchars($items[$i]['pubdate']).')</td></tr>';
				if($items[$i]['description'] != '')
				{
					echo '<tr><td class="odd">'.utf8_decode($items[$i]['description']);
					if($items[$i]['guid'] != '')
					{
						echo '&nbsp;&nbsp;<a href="'.htmlspecialchars($items[$i]['guid']).'" rel="external">'._MORE.'</a>';
					}
					echo '</td></tr>';
				}
				elseif($items[$i]['guid'] != '')
				{
					echo '<tr><td class="even" valign="top"></td><td colspan="2" class="odd"><a href="'.htmlspecialchars($items[$i]['guid']).'" rel="external">'._MORE.'</a></td></tr>';
				}
			}
			echo '</table>';
		}
		else {echo $rss2parser->getErrors();}
	}*/
	
	
	include_once(ICMS_ROOT_PATH . '/class/icmssimplerss.php');
	
	// Create a new instance of the SimplePie object
	$feed = new IcmsSimpleRss($rssurl, 3600);

	?>
		<div id="sp_results"> 

			<!-- As long as the feed has data to work with... -->
			<?php if ($feed): ?>
				<div class="chunk focus" align="center">

					<!-- If the feed has a link back to the site that publishes it (which 99% of them do), link the feed's title to it. -->
					<h3 class="header"><?php if ($feed->get_link()) echo '<a href="' . $feed->get_link() . '">'; echo $feed->get_title(); if ($feed->get_link()) echo '</a>'; ?></h3>

					<!-- If the feed has a description, display it. -->
					<?php echo $feed->get_description(); ?>

				</div>

				<!-- Let's begin looping through each individual news item in the feed. -->
				<?php foreach($feed->get_items() as $item): ?>
					<div class="chunk">

						<?php
						// Let's add a favicon for each item. If one doesn't exist, we'll use an alternate one.
						if (!$favicon = $feed->get_favicon())
						{
							$favicon = './for_the_demo/favicons/alternate.png';
						}
						?>

						<!-- If the item has a permalink back to the original post (which 99% of them do), link the item's title to it. -->
						<h4><img src="<?php echo $favicon; ?>" alt="Favicon" class="favicon" /><?php if ($item->get_permalink()) echo '<a href="' . $item->get_permalink() . '">'; echo $item->get_title(); if ($item->get_permalink()) echo '</a>'; ?></h4>

						<!-- Display the item's primary content. -->
						<?php echo $item->get_content(); ?>

						<?php
						// Check for enclosures.  If an item has any, set the first one to the $enclosure variable.
						if ($enclosure = $item->get_enclosure(0))
						{
							// Use the embed() method to embed the enclosure into the page inline.
							echo '<div align="center">';
							echo '<p>' . $enclosure->embed(array(
								'audio' => './for_the_demo/place_audio.png',
								'video' => './for_the_demo/place_video.png',
								'mediaplayer' => './for_the_demo/mediaplayer.swf',
								'alt' => '<img src="./for_the_demo/mini_podcast.png" class="download" border="0" title="Download the Podcast (' . $enclosure->get_extension() . '; ' . $enclosure->get_size() . ' MB)" />',
								'altclass' => 'download'
							)) . '</p>';
							echo '<p class="footnote" align="center">(' . $enclosure->get_type();
							if ($enclosure->get_size())
							{
								echo '; ' . $enclosure->get_size() . ' MB';								
							}
							echo ')</p>';
							echo '</div>';
						}
						?>

					</div>

				<!-- Stop looping through each item once we've gone through all of them. -->
				<?php endforeach; ?>

			<!-- From here on, we're no longer using data from the feed. -->
			<?php endif; ?>

		</div>

	</div>	
	<?php

}
xoops_cp_footer();
?>