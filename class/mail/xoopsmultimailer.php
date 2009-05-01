<?php
/**
* Functions to extend PHPMailer to email the users
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	MultiMailer
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id$
*/

if (!defined("XOOPS_ROOT_PATH")) {
    die("ImpressCMS root path not defined");
}
/**
 * @package		class
 * @subpackage	mail
 * 
 * @filesource 
 *
 * @author		Jochen Büînagel	<jb@buennagel.com>
 * @copyright	copyright (c) 2000-2003 The XOOPS Project (http://www.xoops.org)
 *
 * @version		$Revision: 1083 $ - $Date: 2007-10-16 12:42:51 -0400 (mar., 16 oct. 2007) $
 */

/**
 * load the base class
 */
require_once(ICMS_LIBRARIES_PATH.'/phpmailer/class.phpmailer.php');

/**
 * Mailer Class.
 * 
 * At the moment, this does nothing but send email through PHP's "mail()" function,
 * but it has the abiltiy to do much more.
 * 
 * If you have problems sending mail with "mail()", you can edit the member variables
 * to suit your setting. Later this will be possible through the admin panel.
 *
 * @todo		Make a page in the admin panel for setting mailer preferences.
 * 
 * @package		class
 * @subpackage	mail
 *
 * @author		Jochen Buennagel	<job@buennagel.com>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 * @version		$Revision: 1083 $ - changed by $Author$ on $Date: 2007-10-16 12:42:51 -0400 (mar., 16 oct. 2007) $
 */
class XoopsMultiMailer extends PHPMailer {

	/**
	 * "from" address
	 * @var 	string
	 * @access	private
	 */
	var $From 		= "";
	
	/**
	 * "from" name
	 * @var 	string
	 * @access	private
	 */
	var $FromName 	= "";

	// can be "smtp", "sendmail", or "mail"
	/**
	 * Method to be used when sending the mail.
	 * 
	 * This can be:
	 * <li>mail (standard PHP function "mail()") (default)
	 * <li>smtp	(send through any SMTP server, SMTPAuth is supported.
	 * You must set {@link $Host}, for SMTPAuth also {@link $SMTPAuth}, 
	 * {@link $Username}, and {@link $Password}.)
	 * <li>sendmail (manually set the path to your sendmail program 
	 * to something different than "mail()" uses in {@link $Sendmail}) 
	 * 
	 * @var 	string
	 * @access	private
	 */
	var $Mailer		= "mail";

	/**
	 * set if $Mailer is "sendmail"
	 * 
	 * Only used if {@link $Mailer} is set to "sendmail".
	 * Contains the full path to your sendmail program or replacement.
	 * @var 	string
	 * @access	private
	 */
	var $Sendmail = "/usr/sbin/sendmail";

	/**
	 * SMTP Host.
	 * 
	 * Only used if {@link $Mailer} is set to "smtp"
	 * @var 	string
	 * @access	private
	 */
	var $Host		= "";

	/**
   * Sets connection prefix.
   * Options are "", "ssl" or "tls"
   * @var string
   */
  var $SMTPSecure = "";

	/**
	 * Does your SMTP host require SMTPAuth authentication?
	 * @var 	boolean
	 * @access	private
	 */
	var $SMTPAuth	= FALSE;

	/**
	 * Username for authentication with your SMTP host.
	 * 
	 * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
	 * @var 	string
	 * @access	private
	 */
	var $Username	= "";

	/**
	 * Password for SMTPAuth.
	 * 
	 * Only used if {@link $Mailer} is "smtp" and {@link $SMTPAuth} is TRUE
	 * @var 	string
	 * @access	private
	 */
	var $Password	= "";
	
	/**
	 * Constuctor
	 * 
	 * @access public
	 * @return void 
	 * 
	 * @global	$xoopsConfig
	 */
	function XoopsMultiMailer(){
		global $xoopsConfig;
	
		$config_handler =& xoops_gethandler('config');
		$xoopsMailerConfig =& $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);
		$this->From = $xoopsMailerConfig['from'];
		if ($this->From == '') {
		    $this->From = $xoopsConfig['adminmail'];
		}
		$this->Sender = $this->From;

		if ($xoopsMailerConfig["mailmethod"] == "smtpauth") {
		    	$this->Mailer = "smtp";
			$this->SMTPAuth = true;
			// @todo create a config option for authentication type: none, ssl, tls
            		//$this->SMTPSecure = "ssl";
			// TODO: change value type of xoopsConfig "smtphost" from array to text
			$this->Host = implode(';',$xoopsMailerConfig['smtphost']);
			$this->Username = $xoopsMailerConfig['smtpuser'];
			$this->Password = $xoopsMailerConfig['smtppass'];
			//$this->Port = 465;
		} else {
			$this->Mailer = $xoopsMailerConfig['mailmethod'];
			$this->SMTPAuth = false;
			$this->Sendmail = $xoopsMailerConfig['sendmailpath'];
			$this->Host = implode(';',$xoopsMailerConfig['smtphost']);
		}
		$this->CharSet = strtolower( _CHARSET );
		if ( file_exists( XOOPS_ROOT_PATH . "/language/{$xoopsConfig['language']}/phpmailer.php" ) ) {
			include( XOOPS_ROOT_PATH . "/language/{$xoopsConfig['language']}/phpmailer.php" );
			$this->language = $PHPMAILER_LANG;
		} else {
			$this->SetLanguage( 'en', ICMS_LIBRARIES_PATH . "/phpmailer/language/" );
		}
		$this->PluginDir = ICMS_LIBRARIES_PATH."/phpmailer/";
	}



	/**
   * Formats an address correctly. This overrides the default addr_format method which does not seem to encode $FromName correctly
   * @access private
   * @param string    $addr the email address to be formatted
   * @return string   the formatted string (address)
   */
  function AddrFormat($addr) {
    if(empty($addr[1]))
        $formatted = $addr[0];
    else
        $formatted = sprintf('%s <%s>', '=?'.$this->CharSet.'?B?'.base64_encode($addr[1]).'?=', $addr[0]);

    return $formatted;
  }




  /**
   * Sends mail via SMTP using PhpSMTP (Author:
   * Chris Ryan).  Returns bool.  Returns false if there is a
   * bad MAIL FROM, or DATA input.
   * Rebuild Header if there is a bad RCPT
   * @access protected
   * @param string    $header the header to be sent
   * @param string    $body   the body of the email to be sent
   * @return bool
   */
  function SmtpSend($header, $body) {
    include_once($this->PluginDir . "class.smtp.php");
    $error = "";
    $bad_rcpt = array();

    if (!$this->SmtpConnect()) {
        return false;
    }            

    $smtp_from = ($this->Sender == "") ? $this->From : $this->Sender;
    if (!$this->smtp->Mail($smtp_from)) {
        $error = $this->Lang("from_failed") . $smtp_from;
        $this->SetError($error);
        $this->smtp->Reset();
        return false;
    }
    // Attempt to send attach all recipients
    for ($i = 0; $i < count($this->to); $i++) {
        if (!$this->smtp->Recipient($this->to[$i][0])) {
            $bad_rcpt[] = $this->to[$i][0];
            unset($this->to[$i]);
        }
    }
    for ($i = 0; $i < count($this->cc); $i++) {
        if (!$this->smtp->Recipient($this->cc[$i][0])) {
            $bad_rcpt[] = $this->cc[$i][0];
            unset($this->cc[$i]);
        }
    }
    for ($i = 0; $i < count($this->bcc); $i++) {
        if (!$this->smtp->Recipient($this->bcc[$i][0])) {
            $bad_rcpt[] = $this->bcc[$i][0];
            unset($this->bcc[$i]);
        }
    }

    // Create error message
    $count = count($bad_rcpt);
    if ($count > 0) {
        for ($i = 0; $i < $count; $i++) {
            if ($i != 0) { 
                $error .= ", ";
            }
            $error .= $bad_rcpt[$i];
        }
        
        //To rebuild a correct header, it should to rebuild a correct adress array
        $this->to = array_values($this->to);
        $this->cc = array_values($this->cc);
        $this->bcc = array_values($this->bcc);
        $header = $this->CreateHeader();
        
        $error = $this->Lang("recipients_failed") . $error;
        $this->SetError($error);
    }
    if (!$this->smtp->Data($header . $body)) {
        $this->SetError($this->Lang("data_not_accepted"));
        $this->smtp->Reset();
        return false;
    }
    if ($this->SMTPKeepAlive == true) {
        $this->smtp->Reset();
    } else {
        $this->SmtpClose();
    }

    return true;
  }
}


?>