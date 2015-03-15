<?php //	namespace Controllers;

/**
 *	class C_Files
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Files extends C_Main {

	/**
	 *	function run
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {

		//	@todo: Finish the filemanager, perhaps with a database connection
		chdir('..');

		if (strtolower($this->oClean->getParts(2)) == 'del') {
			if (file_exists(UPLOAD_PATH . str_replace('+', '/', strtolower($this->oClean->getParts(1)))) && unlink(UPLOAD_PATH . str_replace('+', '/', strtolower($this->oClean->getParts(1))))) {
				header('Location: /files/');
			}
			else {
				throw new Exception('Delete failed');
			}
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			foreach ($_FILES['file']['error'] as $sKey => $sError) {
				if ($_FILES['file']['error'][$sKey] == UPLOAD_ERR_OK) {
					if (file_exists(UPLOAD_PATH . '/' . $_FILES['file']['name'][$sKey])) {
						$i = 1;
						$aFileName = explode('.', $_FILES['file']['name'][$sKey]);
						$aFileName[0] = $aFileName[0] . '_00' . $i;
						$sFileNameNew = implode('.', $aFileName);
						while (file_exists(UPLOAD_PATH . '/' . $sFileNameNew)) {
							$aFileName = explode('.', $_FILES['file']['name'][$sKey]);
							$aFileName[0] = $aFileName[0] . '_00' . $i;
							$sFileNameNew = implode('.', $aFileName);		
							$i++;
						}
						move_uploaded_file($_FILES['file']['tmp_name'][$sKey], UPLOAD_PATH . str_replace('+', '/', strtolower($this->oClean->getParts(1))) . '/' . $sFileNameNew);
					}
					else {
						move_uploaded_file($_FILES['file']["tmp_name"][$sKey], UPLOAD_PATH . str_replace('+', '/', strtolower($this->oClean->getParts(1))) . '/' . $_FILES['file']['name'][$sKey]);
					}
				}
			}
		}

		$aData = array();
		$sRelPath = str_replace('+', '/', strtolower($this->oClean->getParts(1))) . '/';

		if (is_dir(UPLOAD_PATH . $sRelPath) && $dirHandle = opendir(UPLOAD_PATH . $sRelPath)) {
		    while (false !== ($sFileName = readdir($dirHandle))) {
		        if (($sFileName != ".") && ($sFileName != "..") && ((substr($sFileName, 0, 1) != '.') || (defined('DB_ADMIN') && DB_ADMIN == true))) {
					$aData[] = array(
						'name'	 		=> $sFileName,
						'path'			=> UPLOAD_PATH . $sRelPath,
						'url'			=> UPLOAD_URL . $sRelPath,
						'mimetype'	 	=> mime_content_type(UPLOAD_PATH . $sRelPath . $sFileName),
						'size'	 		=> C_Assets::file_size(UPLOAD_PATH . $sRelPath . $sFileName),
						'permissions' 	=> C_Assets::file_perms(UPLOAD_PATH . $sRelPath . $sFileName),
						'image' 		=> (
							(mime_content_type(UPLOAD_PATH . $sRelPath . $sFileName) == 'image/png') || 
							(mime_content_type(UPLOAD_PATH . $sRelPath . $sFileName) == 'image/gif') || 
							(mime_content_type(UPLOAD_PATH . $sRelPath . $sFileName) == 'image/jpeg') || 
							(mime_content_type(UPLOAD_PATH . $sRelPath . $sFileName) == 'image/jpg')
						)
					);
		        }
		    }
		    closedir($dirHandle);
		}

		usort($aData, function($a, $b) {
			$a['mimetype'] = str_replace('directory', 'aaa', $a['mimetype']);
			$b['mimetype'] = str_replace('directory', 'aaa', $b['mimetype']);
		    if (strcmp($a["mimetype"], $b["mimetype"]) === 0) {
		        return strcmp($a["name"], $b["name"]);
		    }
		    return strcmp($a["mimetype"], $b["mimetype"]);
		});

		$this->oView->setContent($aData);
		
		return $this->oView->getHtml();
	}
}