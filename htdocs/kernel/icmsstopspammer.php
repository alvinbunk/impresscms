<?php
/**
* IcmsStopSpammer object
*
* This class is responsible for cross referencing register information with StopForumSpam.com API
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		IcmsPersistableObject
* @author	    Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
* @author		marcan <marcan@impresscms.org>
* @since		1.2
* @version		$Id
*/
class IcmsStopSpammer {
	private $api_url;

	/**
	 * Constructor
	 */
	public function __construct() {
		// checkin stopforumspam API
		$this->api_url = "http://www.stopforumspam.com/api?";
	}

	/**
	 * Check the StopForumSpam API for a specific field (username, email or IP)
	 *
	 * @param string $field field to check
	 * @param string $value value to validate
	 * @return true if spammer was found with passed info
	 */
	function checkForField($field, $value) {
		$spam = false;
		$file = @fopen($this->api_url . $field . '=' . $value, "r");
		if (!$file) {
			echo "<script> alert('" . _US_SERVER_PROBLEM_OCCURRED . "'); window.history.go(-1); </script>\n";
		}
		while (!feof($file)) {
			$line = fgets($file, 1024);
			if (eregi("<appears>(.*)</appears>", $line, $out)) {
				$spam = $out[1];
				break;
			}
		}
		fclose($file);
		return $spam == 'yes';
	}

	/**
	 * Check the StopForumSpam API for specified username
	 *
	 * @param string $username username to check
	 * @return true if spammer was found with this username
	 */
	function badUsername($username) {
		$ret = $this->checkForField('username', $username);
	}

	/**
	 * Check the StopForumSpam API for specified email
	 *
	 * @param string $email email to check
	 * @return true if spammer was found with this email
	 */
	function badEmail($email) {
		$ret = $this->checkForField('email', $email);
	}

	/**
	 * Check the StopForumSpam API for specified IP
	 *
	 * @param string $ip ip to check
	 * @return true if spammer was found with this IP
	 */
	function badIP($ip) {
		$ret = $this->checkForField('ip', $ip);
	}
}

?>