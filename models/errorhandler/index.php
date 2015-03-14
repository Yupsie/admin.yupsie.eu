<?php 	//	namespace Models\Errorhandler;

define('CMS_ERROR_LOG_OPEN', 'Kan logbestand niet openen');
define('CMS_ERROR_LOG_READ', 'Lezen van logbestand mislukt');
define('CMS_ERROR_LOG_WRITE', 'Schrijven naar logbestand mislukt');

/**
 *	class M_Errorhandler_Main
 *	Manage the errorlogs in the files
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 11-05-2010
 *	@version 8.0.0
 */
class M_Errorhandler_Main {
	private $sFile;
	private $sDir;
	
	public function __construct($sFilename = 'log') {
		$this->sDir = '/var/sub/admin.yupsie.eu/models/errorhandler/';
		$this->sFile = $this->sDir . $sFilename . '_' . date('Y_m') . '.txt';
	}

	/**
	 *	function setData
	 *	Modify the errorlogs
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setData($aData) {

		$sContent = '
#@####-' . $aData['date'] . '-###
- Message: ' . $aData['message'] . '

- File: ' . $aData['file'] .'
- Line: ' . $aData['line'] .'
- Code: ' . $aData['code'] . '

##-Stack-trace-##
' . $aData['stacktrace'] . '

#-Additional-info-#
- IP: ' . $aData['ip'] . '
- User-agent: ' . $aData['agent'] . '
- Cookie: ' . $aData['cookie'] . '
- Querystring: ' . $aData['querystring'] . '
- Request method: ' . $aData['request'] . '
- HTTP Status: '  . '
';

		if (!$rFile = fopen($this->sFile, 'a+')) {
			 throw new Exception(CMS_ERROR_LOG_OPEN);
		}

		// 	Write $somecontent to our opened file.
		if (fwrite($rFile, $sContent) === false) {
			throw new Exception(CMS_ERROR_LOG_WRITE);
		}
		fclose($rFile);
	}

	/**
	 *	function getData
	 *	View the errorlogs
	 *
	 *	@access public
	 *	@return string
	 */
	public function getData() {
		if (!$rFile = fopen($this->sFile, 'r')) {
			throw new Exception(CMS_ERROR_LOG_OPEN);
		}
		if (!$sContent = fread($rFile, filesize($this->sFile))) {
			throw new Exception(CMS_ERROR_LOG_READ);
		}
		return $sContent;
	}
}