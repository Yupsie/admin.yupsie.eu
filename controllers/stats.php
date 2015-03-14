<?php //	namespace Controllers;

/**
 *	class C_Stats
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Stats extends C_Main {

	/**
	 *	function run
	 *	Load the model and view
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		$this->oModel = new M_Main;
		
		$aData['series'] = $this->oModel->getVisitData();
		$aData['pie'] = $this->oModel->getVisitDataPie();
		$aData['count'] = $this->oModel->getVisitDataCount();
		
		$this->oView->setContent($aData);
		return $this->oView->getHtml();
	}
}