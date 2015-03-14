<?php //	namespace Controllers\Classloader;


/**
 *	function __autoload
 *	Load all classes automatically on call
 *
 *	@param string sClassname
 *	@return void
 */
function __autoload($sClassname) {
	C_Classloader_Main::run($sClassname);
}

/**
 *	class Controllers_Classloader_Main
 *	Give an error when a class cannot be loaded
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Classloader_Main {

	/**
	 *	function run
	 *
	 *	@access public static
	 *	@param string sClassname
	 *	@return void
	 */
	public static function run($sClassname) {
	//	$sFilename = strtolower(__NAMESPACE__ . '/' . str_replace(array('Main'), array('index'), $sClassname));
		$sFilename = strtolower(str_replace(array('Main', 'M_', 'V_', 'C_', '_'), array('index', 'models/', 'views/', 'controllers/', '/'), $sClassname));

        if (file_exists(CMS_PATH . $sFilename . '.php')) {
            if (is_readable(CMS_PATH . $sFilename . '.php')) {
                require(CMS_PATH . $sFilename . '.php');
            }
			else {
                throw new Exception('Oeps... - 403 - Bestand met klasse ' . $sClassname . ' niet leesbaar');
            }
        }
		else {
            throw new Exception('Oeps... - 404 - Klasse "' . $sClassname . '" niet gevonden');
        }
	}
}