<?php //	namespace Models;

/**
 * 	class M_Cron
 *	Load the main Model, other Models can extend this class
 *
 * 	@author yupsie
 */
class M_Cron extends M_Main {

	/**
	 *	function getLogCron
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@return array
	 */
	public function getLogCron() {

		$aData = array();

		if (defined('LOG_PATH') && is_dir(LOG_PATH)) {
		    if ($rDir = opendir(LOG_PATH)) {
		        while (($sFile = readdir($rDir)) !== false) {
		        	$aPath = pathinfo($sFile);
		        	if ($aPath['extension'] == 'log') {

						$rFile = fopen(LOG_PATH . $sFile, "r");
						$sFileContents = fread($rFile, filesize(LOG_PATH . $sFile));

						$aFileContents = explode('<br />', nl2br($sFileContents));
						foreach ($aFileContents as $sLine) {
							$aLine = explode('|', $sLine);
							if (count($aLine) > 1) {
								list($aColumns['time'], $aColumns['name'], $aColumns['frequency'], $aColumns['color'], $aColumns['description'], $aColumns['status']) = $aLine;
								$aData[$aColumns['time']] = $aColumns;
							}
						}

						fclose($rFile);
						$sFile = str_replace('.php', '', $sFile);
					}
		        }
		        closedir($rDir);
		    }
		}
		krsort($aData);
		return $aData;
	}

	/**
	 *	function getLog
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@return array
	 */
	public function getLog() {

		$aData = array();

		if (defined('LOG_PATH') && is_dir(LOG_PATH)) {
		    if ($rDir = opendir(LOG_PATH)) {
		        while (($sFile = readdir($rDir)) !== false) {
		        	$aPath = pathinfo($sFile);
		        	if ($aPath['extension'] == 'txt') {

						$rFile = fopen(LOG_PATH . $sFile, "r");
						$sFileContents = fread($rFile, filesize(LOG_PATH . $sFile));
						$aData[$aPath['filename']] = nl2br($sFileContents);

						fclose($rFile);
					}
		        }
		        closedir($rDir);
		    }
		}
		krsort($aData);
		return $aData;
	}
}