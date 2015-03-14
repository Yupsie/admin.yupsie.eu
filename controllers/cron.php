<?php //	namespace Controllers;

/**
 *	class C_Cron
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Cron extends C_Main {
	/**
	 *	function run
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		$this->oModel = new M_Cron;

		$aData = array(
			'cron' => $this->oModel->getLogCron(),
			'errors' => $this->oModel->getLog()
		);
		$this->oView->setContent($aData);
		return $this->oView->getHtml();
	}
}