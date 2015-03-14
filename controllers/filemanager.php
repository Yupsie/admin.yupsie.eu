<?php //	namespace Controllers;

/**
 *	class C_Filemanager
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Filemanager extends C_Main {

	/**
	 *	function run
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {

		chdir('..');

		$aData = array();
		$sRelPath = str_replace('+', '/', strtolower($this->oClean->getParts(1))) . '/';

		if ($dirHandle = opendir(UPLOAD_PATH . $sRelPath)) {
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