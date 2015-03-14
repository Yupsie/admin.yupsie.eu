<?php //	namespace Controllers\Cleanurl;

/**
 *	class Controllers_Cleanurl_Main
 *	Generate and read clean URL's
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0b
 */
class C_Cleanurl_Main {
	private $sBasename;
	private $aParts = array();

	/**
	 *	function __construct
	 *	Initialize variables
	 *
	 *	@access public
	 *	@return void
	 */
	public function __construct() {
		$this->sBasename = CMS_PATH;

		$this->parseURL();
	}

	/**
	 *	function parseURL
	 *	Get the URL from the SERVER superglobal
	 *
	 *	@access private
	 *	@return void
	 */
	private function parseURL() {
		$sUri = $_SERVER['REQUEST_URI'];
		$sScript = $_SERVER['SCRIPT_NAME'];
		$aExtension = explode(".", $sScript);

		if (strstr($sUri, ".")) {
			$aUri = explode('.', $sUri);
			$sLast = end($aUri);

			if ($sLast == end($aExtension)) {
				array_pop($aUri);
				$sUri = implode('.', $aUri);
			}
		}

		$this->sBasename = basename($sScript, '.' . end($aExtension));
		$sTemporary = explode('/', $sUri);
		$iKey = array_search($this->sBasename, $sTemporary);
		$this->aParts = array_slice($sTemporary, $iKey + 1);
	}

	/**
	 *	function setRelative
	 *	Get the relative path to display images, the GET variables will be handled like directories
	 *
	 *	@access public
	 *	@return string
	 */
	public function getRelative() {
		//	Count the number of slashes to define relative path
		$iSlashes = count($this->aParts);
		$sSlashes = '';
		for ($iCountSlashes = 0; $iCountSlashes < $iSlashes; $iCountSlashes++) {
			$sSlashes .= "../";
		}
		//	Make relative path variable available for webpage
		return $sSlashes;
	}

	/**
	 *	function getParts
	 *	Get the names of the variables which are used to specify the parts of the query string
	 *
	 *	@access public
	 *	@param integer iTag
	 *	@param boolean bClass
	 *	@return string
	 */
	public function getParts($iTag, $bClass = true) {
		if ($bClass) {
			if (!empty($this->aParts[$iTag])) {
				return ucfirst(strtolower($this->aParts[$iTag]));
			}
			elseif ($iTag == 0) {
				return 'Main';
			}
			else {
				return false;
			}
		}
		else {
			if (!empty($this->aParts[$iTag])) {
				return strtolower($this->aParts[$iTag]);
			}
			elseif ($iTag == 0) {
				return 'main';
			}
			else {
				return false;
			}
		}
	}
}