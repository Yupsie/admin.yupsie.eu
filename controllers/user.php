<?php //	namespace Controllers;

/**
 *	class C_User
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_User extends C_Main {

	/**
	 *	function run
	 *	Load the model and view
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		$this->oModel = new M_Main;
		
		if ($this->oClean->getParts(1)) {
			$this->oView->setContent($this->oModel->getPrivileges(strtolower($this->oClean->getParts(1))));
		}
		else {
			$this->oView->setContent($this->oModel->getUsers());
		}
		return $this->oView->getHtml();
	}
}