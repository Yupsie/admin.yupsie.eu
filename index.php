<?php

define('CMS_PATH', dirname(__FILE__) . '/');
define('SITES_PATH', '/var/sub');

/**
 *	Main index
 *
 *	@package Yupsie.eu v8.7mvc2.0.0
 *	@copyright Yupsie.eu (c) 18-04-2010
 */
session_start();

/**
 *	Load the configuration options
 *	@see /config.php for more information
 */
switch ($_SERVER['HTTP_HOST']) {
	case 'admin.weerbaarworden.nl';
		include('/var/dom/www.weerbaarworden.nl/config.php');
		break;

	case 'admin.impulsio.nl';
		include('/var/dom/www.impulsio.nl/config.php');
		break;

	case 'admin.bsr-venlo.nl';
		include('/var/dom/www.bsr-venlo.nl/config.php');
		break;

	default:
		include('../../www/config.php');
		break;
}

if (!isset($_SESSION['lang'])) {
	$_SESSION['lang'] = DB_LANG;
}
include(CMS_PATH . 'langs/' . $_SESSION['lang'] . '.php');

//	Set error reporting according to the debug mode
if (DEBUG_MODE == 1) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'On');
}
else {
	error_reporting(0);
	ini_set('display_errors', 'Off');
}

/**
 *	Load Controllers_Cleanurl_Main
 *	@see /controllers/cleanurl/index.php for more information
 */
require_once(CMS_PATH . 'controllers/cleanurl/index.php');
	$oClean = new C_Cleanurl_Main;

/**
 *	Load Controllers_Classloader_Main
 *	@see /controllers/classloader/index.php for more information
 */
require_once(CMS_PATH . 'controllers/classloader/index.php');

/**
 *	Load Controllers_Errorhandler_Main
 *	Set the exception handler to handle errors which are not caught, this will print an error page and eventually log the errors
 *	@see /controllers/errorhandler/index.php for more information
 */
$oExceptionHandler = new C_Errorhandler_Main;
set_exception_handler(array($oExceptionHandler, 'run'));

/**
 *	Load Controllers_Main
 *	Initiate a Controller object according to the URL and throw an Exception when the class doesn't exist (invalid URL)
 *	@see /controllers/index.php and others for more information
 */
$sPage = "C_" . $oClean->getParts(0);
if (class_exists($sPage)) {
	$oPage = new $sPage;
	$oPage->run();
}
else {
	throw new Exception(sprintf(CMS_ERROR_404, $oClean->getParts(0)));
}