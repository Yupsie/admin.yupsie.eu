<?php //	namespace Views;

/**
 *	class V_Main
 *	Load the main View, other Views can extend this class
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_Main {
	protected $sData;
	protected $oModel;
	protected $__CFG;
	protected $__DB;
	protected $oClean;

	/**
	 *	function __construct
	 *	Set the default page title and a default content string for views that have been created but not filled yet
	 *
	 *	@access public
	 *	@return void
	 */
	public function __construct() {
		global $__CFG;
		global $__DB;
		$this->__CFG = $__CFG;
		$this->__DB = $__DB;

		$this->oClean = new C_Cleanurl_Main;
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
		<script type="text/javascript" src="/views/js/tiny4/tinymce.min.js"></script>
		<script type="text/javascript" src="/views/js/tiny4/jquery.tinymce.min.js"></script>
		<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
		<script type="text/javascript" src="http://code.highcharts.com/modules/exporting.js"></script>
		<script type="text/javascript" src="http://' . $_SERVER['HTTP_HOST'] . '/views/js/jquery.function.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".submit").click(function() {
					$("form").submit();
				});
				$("input[type=file]").change(function() {
					$(this).parent().parent().clone().appendTo(".edit tbody");
				});
			});
		</script>
	</head>
	<body>
		<header>
			<img src="/views/images/logo.svg" id="svg">
			<h1 style="font-size:80px;margin-top:-10px;color:rgba(85,85,255,0.2);margin-right:-50px;letter-spacing:-4px;">' . CMS_TITLE . '</h1><h1>' . SITE_TITLE . '</h1>
		</header>
		<menu>';

		if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == 1) {
			$sOutput .= '
			<li' . (strtolower($this->oClean->getParts(0)) == 'view' || strtolower($this->oClean->getParts(0)) == 'edit'?' class="active"':'') . '><a>t</a>
				<div>
					<ul>';
				
			$oMenu = new M_Main;
			$aDataTables = $oMenu->getData();
			foreach ($aDataTables as $aValueTables) {
				$sOutput .= '
						<li' . (strtolower($this->oClean->getParts(1)) == $aValueTables['tablename']?' class="active"':'') . '>
							<a href="/view/' . $aValueTables['tablename'] . '/">' . ucwords(str_replace(array(DB_PREFIX, '_'), array('', ' '), $aValueTables['tablename'])) . '</a>
						</li>';
			}
			$sOutput .= '
					</ul>
				</div>

			</li>
			<li' . (strtolower($this->oClean->getParts(0)) == 'files'?' class="active"':'') . '><a href="/files/">b</a></li>
			<li' . (strtolower($this->oClean->getParts(0)) == 'stats'?' class="active"':'') . '><a href="/stats/">s</a></li>
			<li' . (strtolower($this->oClean->getParts(0)) == 'cron'?' class="active"':'') . '><a href="/cron/">c</a></li>
			<li' . (strtolower($this->oClean->getParts(0)) == 'language'?' class="active"':'') . '><a>L</a>
				<div>
					<ul>';
		$sDir = CMS_PATH . "langs/";

		// 	Open a known directory, and proceed to read its contents
		if (is_dir($sDir)) {
		    if ($rDir = opendir($sDir)) {
		        while (($sFile = readdir($rDir)) !== false) {
					if (($sFile != '.') && ($sFile != '..')) {
						$rFile = fopen($sDir . $sFile, "r");
						$sFileContents = fread($rFile, 80);
						$sFileContents = substr($sFileContents, 53);
						$aFileContents = explode("'", $sFileContents);
						fclose($rFile);
						$sFile = str_replace('.php', '', $sFile);
						$sOutput .= '
						<li>
							<a href="/language/' . $sFile . '/"' . ($sFile == $_SESSION['lang'] ? ' class="active"' : '') . '>' . $aFileContents[0] . '</a>
						</li>';
					}
		        }
		        closedir($rDir);
		    }
		}
		$sOutput .= '</ul>
				</div>
			</li>';

			if (defined('DB_ADMIN') && DB_ADMIN == true) {
				$sOutput .= '
			<li' . (strtolower($this->oClean->getParts(0)) == 'info' ? ' class="active admin"' : '') . ' class="admin"><a href="/info/">Q</a>
			<li' . (strtolower($this->oClean->getParts(0)) == 'user' ? ' class="active admin"' : '') . ' class="admin"><a href="/user/">g</a>
				<div>
					<ul>';

			$aDataUsers = $oMenu->getUsers();
			foreach ($aDataUsers['rows'] as $aValueUsers) {
				$sOutput .= '
						<li' . (strtolower($this->oClean->getParts(1)) == $aValueUsers['usename']?' class="active"':'') . '>
							<a href="/user/' . $aValueUsers['usename'] . '/">' . $aValueUsers['usename'] . '</a>
						</li>';
			}
			$sOutput .= '
					</ul>
				</div>
			</li>
			<li' . (strtolower($this->oClean->getParts(0)) == 'sites' ? ' class="active admin"' : '') . ' class="admin"><a href="/sites/">T</a>
				<div>
					<ul>';

			$aDataConfig = $oMenu->getSiteConfigs();
			foreach ($aDataConfig as $aValueConfig) {
				$sOutput .= '
						<li' . (strtolower($this->oClean->getParts(1)) == $aValueConfig['sitename']?' class="active"':'') . '>
							<a href="/sites/' . $aValueConfig['sitename'] . '/">' . $aValueConfig['sitename'] . '</a>
						</li>';
			}
			$sOutput .= '
					</ul>
				</div>
			</li>';
			}
		}
		$sOutput .= '
			<li' . (strtolower($this->oClean->getParts(0)) == 'main'?
				' class="active"><a href="/login/">j':
				'><a href="/logout/">i</object>'
			) . '</a></li>
		</menu>';
					
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
		<footer>
			<p>&copy; <a href="http://www.yupsie.eu">Yupsie.eu</a> ' . date('Y') . '</p>
		</footer>
	</body>
</html>';
		return $sOutput;
	}

	/**
	 *	function setContent
	 *	Set the default page title and a default content string for views that have been created but not filled yet
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {
		$this->sData = '
		<section class="login">
			<form method="post" action="/">
				<table>
					<thead>
						<tr>
							<th>' . CMS_TABLE_FIELDNAME . '</th>
							<th>' . CMS_TABLE_FIELDCONTENT . '</th>
							<th><input type="submit" value="%"></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>' . CMS_NAME . '</td>
							<td class="input"><input type="text" name="name"></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>' . CMS_PASS . '</td>
							<td class="input"><input type="password" name="password"></td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th>' . CMS_TABLE_FIELDNAME . '</th>
							<th>' . CMS_TABLE_FIELDCONTENT . '</th>
							<th><input type="submit" value="%"></th>
						</tr>
					</tfoot>
				</table>
			</form>
		</section>';
	}

	/**
	 *	function getHtml
	 *	Print the output to screen
	 *
	 *	@access public
	 *	@return void
	 */
	public function getHtml() {
		echo $this->getHeader(get_class($this));
		echo $this->sData;
		echo $this->getFooter();
	}

	/**
	 *	function stylizeCode
	 *	
	 *	@access protected static
	 *	@param array aMatches
	 *	@return string
	 */
	protected static function stylizeCode($aMatches) {    
		$aMatches[4] = preg_replace("/^[\s]+|[\s]+$/", "", $aMatches[4]); 
		$oGeshi = new V_Geshi_Main($aMatches[4], $aMatches[1]);
		$oGeshi->enable_classes(); 
		$oGeshi->set_header_type(GESHI_HEADER_PRE_TABLE);
		$oGeshi->set_header_content('Taal: <LANGUAGE>');
		$oGeshi->set_footer_content('Parsetijd: <TIME> seconden');
		$oGeshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS, 2);
		$aHighlightlines = explode(",", $aMatches[3]);
		$aHighlightlines = array_map(create_function('$value', 'return (int)$value;'), $aHighlightlines);
		$oGeshi->highlight_lines_extra($aHighlightlines);
		return '<div class="mycode">' . $oGeshi->parse_code() . '</div>';
	}
}