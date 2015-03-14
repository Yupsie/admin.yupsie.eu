<?php //	namespace Controllers;

/**
 *	class C_Edit
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Edit extends C_Main {
	
	/**
	 *	function run
	 *	By default the View with a name matching that of the Controller is loaded
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		$this->oModel = new M_Main;

		chdir('..');

		$aData = array();
		
		$aData['output'] = array();
		$aData['output']['table'] = $this->oClean->getParts(1);
		$aData['output']['id'] = $this->oClean->getParts(2);
		$aData['output']['action'] = $this->oClean->getParts(3);

		$aData['rows'] = $this->oModel->getRows($aData['output']['table'], $aData['output']['id']);
		$i = 0;
		foreach($aData['rows'][0] as $sValue) {
			$aData['columnsize'][$i] = $this->oModel->getColumnsSize($aData['output']['table'], $i);
			$aData['columntype'][$i] = $this->oModel->getColumnsType($aData['output']['table'], $i);
			$i++;
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$aPosted = array();
			$sQueryString = '';
			$sQueryStringA = '';
			$sQueryStringB = '';
			$i = 0;
			foreach ($_POST as $sPostField => $sPostValue) {
				if (($sPostField != 'table') && ($sPostField != 'id')) {
					$aPosted[$i] = $sPostValue;
					$i++;
					$j = $i + 1;
					$sQueryString .= (($i != 1)?', ':'') . $sPostField . '= $' . $j;
					$sQueryStringA .= (($i != 1)?', ':'') . $sPostField;
					$sQueryStringB .= (($i != 1)?', ':'') . '$' . $i;
				}
			}
			if ($_POST['id'] == 'Add') {
				if ($this->oModel->addData(strtolower($_POST['table']), $sQueryStringA, $sQueryStringB, $aPosted)) {
					header('Location: /view/' . $_POST['table'] . '/');
				}
			}
			else {
				array_unshift($aPosted, $_POST['id']);
				if ($this->oModel->setData(strtolower($_POST['table']), $sQueryString, $aPosted)) {
					header('Location: /view/' . $_POST['table'] . '/');
				}
			}
		}

		if ($this->oClean->getParts(3) == 'Delete') {
			$this->oModel->trashData($this->oClean->getParts(1), $this->oClean->getParts(2));
			header('Location: /view/' . $this->oClean->getParts(1) . '/');
		}

		$aData['files'] = array();
		if ($dirHandle = opendir(UPLOAD_PATH)) {
		    while (false !== ($sFileName = readdir($dirHandle))) {
		        if (($sFileName != ".") && ($sFileName != "..")) {
					$aData['files'][] = array(
						'name'	 		=> $sFileName,
						'path'			=> 'http://' . str_replace('admin', 'www', $_SERVER['HTTP_HOST']) . '/data',
						'mimetype'	 	=> mime_content_type(UPLOAD_PATH . '/' . $sFileName),
						'size'	 		=> C_Assets::file_size(UPLOAD_PATH . '/' . $sFileName),
						'permissions' 	=> C_Assets::file_perms(UPLOAD_PATH . '/' . $sFileName),
						'image' 		=> (
							(mime_content_type(UPLOAD_PATH . '/' . $sFileName) == 'image/png') || 
							(mime_content_type(UPLOAD_PATH . '/' . $sFileName) == 'image/gif') || 
							(mime_content_type(UPLOAD_PATH . '/' . $sFileName) == 'image/jpeg') || 
							(mime_content_type(UPLOAD_PATH . '/' . $sFileName) == 'image/jpg')
						)
					);
		        }
		    }
		    closedir($dirHandle);
		}

		$this->oView->setContent($aData, $this->oClean->getParts(1));
		return $this->oView->getHtml();
	}
}