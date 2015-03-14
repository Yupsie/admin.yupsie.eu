<?php //	namespace Controllers;

/**
 *	class C_View
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_View extends C_Main {

	/**
	 *	function run
	 *	By default the View with a name matching that of the Controller is loaded
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {

		$this->oModel = new M_Main;
		$aData['tables'] = $this->oModel->getData(get_class($this));

		foreach($aData['tables'] as $aValueTables) {
			$aData['columns'][$aValueTables['tablename']] = $this->oModel->getColumns($aValueTables['tablename']);
			$aData['rows'][$aValueTables['tablename']] = $this->oModel->getRows($aValueTables['tablename']);
			$aData['numcolumns'][$aValueTables['tablename']] = $this->oModel->getColumnsNum($aValueTables['tablename']);
			$aData['numrows'][$aValueTables['tablename']] = $this->oModel->getRowsNum($aValueTables['tablename']);
		}
		$this->oView->setContent($aData);//, $this->oClean->getParts(1), $this->iAuth

		return $this->oView->getHtml();
	}
}