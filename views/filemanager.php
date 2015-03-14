<?php //	namespace Views;

/**
 *	class V_Filemanager
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_Filemanager extends V_Main {

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {
		$this->sData .= '
		<section>
			<h2>' . ucwords(str_replace('_', ' ', CMS_UPLOADED_FILES)) . '</h2>
			<form method="post" enctype="multipart/form-data">
				<table>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>' . CMS_FILE_NAME . '</th>
							<th>' . CMS_FILE_TYPE . '</th>
							<th>' . CMS_FILE_SIZE . '</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>';

		if ($this->oClean->getParts(1)) {
			$aPath = explode('+', strtolower($this->oClean->getParts(1)));
			array_pop($aPath);
			$sPath = implode('+', $aPath);

			$this->sData .= '
						<tr>
							<td colspan="2"><strong><a href="/files/' . $sPath . '">..</a></strong></td>
							<td>&nbsp;<br>&nbsp;</td>
							<td>&nbsp;<br>
								<em>&nbsp;</em></td>
							<td>&nbsp;</td>
						</tr>';
		}

		foreach ($aData as $aFile) {

			$aDimensions = ($aFile['image'] ? getimagesize($aFile['path'] . $aFile['name']) : array());
			if (is_dir($aFile['path'] . $aFile['name'])) {
				$this->sData .= '
						<tr' . (substr($aFile['name'], 0, 1) == '.' ? ' class="hidden"' : '') . '>
							<td colspan="2"><strong><a href="' . str_replace('/', '+', strtolower(trim($this->oClean->getParts(1) . '/' . $aFile['name'], '/'))) . '">' . $aFile['name'] . '</a></strong></td>
							<td>' . $aFile['mimetype'] . '<br>&nbsp;</td>
							<td class="permissions">&nbsp;<br>
								<em>' . $aFile['permissions'] . '</em></td>
							<td><a href="' . $aFile['name'] . '/del/" class="del_icon">I</a></td>
						</tr>';
			}
			else {
				$this->sData .= '
						<tr' . (substr($aFile['name'], 0, 1) == '.' ? ' class="hidden"' : '') . '>
							<td class="flaticon">';

				if ($aFile['image']) {
					$this->sData .= '
								<a href="' . $aFile['url'] . $aFile['name'] . '"><img src="' . $aFile['url'] . $aFile['name'] . '" alt="' . $aFile['name'] . '"></a>';
				}
				else {
					$aInfo = pathinfo($aFile['name']);
					if (file_exists(CMS_PATH . 'views/css/icons/' . $aInfo['extension'] . '.svg')) {
						$this->sData .= '
								<a href="' . $aFile['url'] . $aFile['name'] . '"><img src="/views/css/icons/' . $aInfo['extension'] . '.svg"></a>';
					}
					else {
						$this->sData .= '
								<a href="' . $aFile['url'] . $aFile['name'] . '">&nbsp;</a>';
					}
				}
				$this->sData .= '
							</td>
							<td><a href="' . $aFile['url'] . $aFile['name'] . '">' . $aFile['name'] . '</a></td>
							<td>' . $aFile['mimetype'] . '<br>' . (isset($aDimensions[1]) ? $aDimensions[0] . 'x' . $aDimensions[1] : '') . '</td>
							<td class="permissions">' . $aFile['size'] . '<br>
								<em>' . $aFile['permissions'] . '</em></td>
							<td><a href="' . $aFile['name'] . '/del/" class="del_icon">I</a></td>
						</tr>';
			}
		}
		$this->sData .= '
					</tbody>
					<tfoot>
						<tr>
							<th>&nbsp;</th>
							<th>' . CMS_FILE_NAME . '</th>
							<th>' . CMS_FILE_TYPE . '</th>
							<th>' . CMS_FILE_SIZE . '</th>
							<th>&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</form>
		</section>';
	}

	public function getHeader() {
		$sOutput = '<!doctype html>
<html>
	<head>
		<title>' . CMS_TITLE . ' [' . SITE_TITLE . ']</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="http://' . $_SERVER['HTTP_HOST'] . '/views/css/cms.css">
		<link rel="icon" type="image/png" href="http://' . $_SERVER['HTTP_HOST'] . '/views/images/favicon.png">
		<script type="text/javascript" src="http://cdn.yupsie.eu/jquery/jquery.js"></script>
		<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/views/js/jquery.function.js"></script>
	</head>
	<body>';
					
		return $sOutput;
	}

	/**
	 *	function getFooter
	 *
	 *	@access public
	 *	@return string
	 */
	public function getFooter() {
		$sOutput = '
	</body>
</html>';
		return $sOutput;
	}
}