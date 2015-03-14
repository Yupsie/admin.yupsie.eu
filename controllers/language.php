<?php //	namespace Controllers;

/**
 *	class C_Language
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Language extends C_Main {

	/**
	 *	function run
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {

		if ($this->oClean->getParts(1)) {
			$_SESSION['lang'] = strtolower($this->oClean->getParts(1));
		}

		header('Location: /info/');
	}
}