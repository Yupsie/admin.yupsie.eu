<?php //	namespace Controllers;

/**
 *	class C_Logout
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Logout extends C_Main {

	/**
	 *	function run
	 *	By default the View with a name matching that of the Controller is loaded
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		session_destroy();
		header('Location: /');
	}
}