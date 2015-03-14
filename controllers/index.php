<?php //	namespace Controllers;

/**
 *	class C_Main
 *	Load the main controller, other controllers can extend this class
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Main {
	protected $oView;
	protected $oModel;
	protected $oMenu;
	protected $oReplies;
	protected $sReplySection;
	protected $iAuth;
	protected $oClean;
	protected $oFormCheck;

	/**
	 *	function __construct
	 *	Authorise the current user. Load the querystring. Load the menu for the header. Load the standard view
	 *
	 *	@access public
	 *	@return void
	 */
	public function __construct() {
		
		$this->oClean = new C_Cleanurl_Main;
		
		$sViewName = str_replace('C_', 'V_', get_class($this));
		$this->oView = new $sViewName;

		if ((!isset($_SESSION['loggedin'])) || ($_SESSION['loggedin'] != 1)) {
			if ($this->oClean->getParts(0) != null && $this->oClean->getParts(0) != 'Main') {
				header('Location: /');
			}
			else {
				if ($_SERVER['REQUEST_METHOD'] == 'POST') {
					if (($_POST['name'] == DB_USER) && ($_POST['password'] == DB_PASS)) {
						$_SESSION['loggedin'] = 1;
						header('Location: /info/');
					}
				}
			}
		}
		elseif ($this->oClean->getParts(0) == null) {
			header('Location: /view/');
		}
	}

	/**
	 *	function run
	 *	By default the View with a name matching that of the Controller is loaded
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {

		//	@todo: See if the database persmissions ($iAuth) can be restricted to certain tables configured in the database users
		$this->oModel = new M_Main;
	//	print_r($this->oModel->getPrivileges(DB_USER));
		$this->oView->setContent($this->oModel->getData(get_class($this)), $this->oClean->getParts(1));//$this->iAuth

		if (($_SERVER['REQUEST_METHOD'] == 'POST') && (!empty($_POST['table']))) {
			if ($_POST['action'] == 'edit') {
				if ($oModel->setRows($_POST['table'], 'edit')) {
					header('Location: view/');
				}
			}
			elseif ($_POST['action'] == 'add') {
				if ($oModel->setRows($_POST['table'], 'add')) {
					header('Location: view/');
				}
			}
		}

		if ($this->oClean->getParts(1) == 'delete') {
			$oModel->setRows($_POST['table'], 'delete');
		}
		return $this->oView->getHtml();
	}
}