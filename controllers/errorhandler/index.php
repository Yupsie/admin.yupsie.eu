<?php //	namespace Controllers\Errorhandler;


/**
 *	class C_Errorhandler_Main
 *	Load the errorhandler set with set_exception_handler() with run() as the callback function
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 22-04-2010
 *	@version 8.0.0
 */
class C_Errorhandler_Main {

	/**
	 *	function run
	 *	Load the model and view
	 *
	 *	@access public
	 *	@param string e
	 *	@return string
	 */
	public function run($e) {
		
		$this->oModel = new M_Errorhandler_Main;
		$this->oView = new V_Errorhandler_Main;

		$aLog = array(
			'message' => $e->getMessage(), 
			'file' => $e->getFile(), 
			'line' => $e->getLine(), 
			'code' => $e->getCode(), 
			'stacktrace' => $e->getTraceAsString(), 
			'ip' => $_SERVER['REMOTE_ADDR'],
			'agent' => $_SERVER['HTTP_USER_AGENT'],
			'cookie' => $_SERVER['HTTP_COOKIE'],
			'querystring' => $_SERVER['QUERY_STRING'],
			'request' => $_SERVER['REQUEST_METHOD'],
			'date' => date('d-m-Y H:i:s')
		);
		if (strpos($aLog['agent'], 'bot') === false && strpos($aLog['agent'], 'spider') === false) {
			$this->oModel->setData($aLog);
		}
		$oMail = new C_Phpmailer_Main(DEBUG_MODE);
		$oMail->IsSMTP();
		$sMailBody = '
			<body>
				<style>
					body,
					table {
						background-color:#ffffff;
						width:100%;
						font-family:\'Josefin Sans Std\',\'Helvetica\',sans-serif;
					}
					table {
						margin-top:70px;
					}
					tr:first-child {
						box-shadow:0 0 3px 3px rgba(0,0,0,0.5);
					}
					td {
						font-size:14px;
						background-color:#ffffff;
						border-bottom:1px solid #ffffff;
						color:#aaaaff;
						width:16%;
						padding:10px 20px;
						text-align:right;
						vertical-align:top;
					}
					td:nth-child(2) {
						text-align:left;
						color:#222222;
					}
					td:nth-child(2),
					td:nth-last-child(2) {
						background-color:#333333;
						border-bottom:1px solid #222222;
						width:34%;
					}
					td.header {
						background-color:#aaaaff;
						color:#333333;
						text-align:right;
						padding:20px;
						font-family:\'Josefin Sans Std\',\'Helvetica\',sans-serif;
						font-size:32px;
						padding:20px;
						font-weight:bold;
						vertical-align:top;
					}
					td.footer {
						background-color:#aaaaff;
						color:#333333;
						border:0;
					}
					td.header img {
						margin: -60px -168px -60px 0;
					}
					td.code {
						background-color:#222222;
						color:#aaaaff;
						font-family:\'monofur\',\'Courier New\',monospace;
					}
				</style>
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td class="header"><img src="http://admin.yupsie.eu/views/images/logo.svg" alt=""></td>
						<td class="header" colspan="2">
							Oeps... er is iets fout gegaan
						</td>
						<td class="header">&nbsp;</td>
					</tr>';
		foreach ($aLog as $sLogKey => $sLogValue) {
			if ($sLogKey == 'stacktrace') {
				$sMailBody .= '
					<tr>
						<td>&nbsp;</td>
						<td class="code" colspan="2">' . nl2br($sLogValue) . '</td>
						<td>&nbsp;</td>
					</tr>';
			}
			elseif ($sLogKey == 'date') {
				$sMailBody .= '
					<tr>
						<td>&nbsp;</td>
						<td class="footer">&copy; Yupsie.eu ' . date('Y') . '</td>
						<td class="footer">' . nl2br($sLogValue) . '</td>
						<td>&nbsp;</td>
					</tr>';
			}
			else {
				$sMailBody .= '
					<tr>
						<td>&nbsp;</td>
						<td>' . $sLogKey . '</td>
						<td>' . nl2br($sLogValue) . '</td>
						<td>&nbsp;</td>
					</tr>';
			}
		}
		$sMailBody .= '
				</table>
			</body>';

		if (strpos($aLog['agent'], 'bot') === false && strpos($aLog['agent'], 'spider') === false) {
			try
			{
	//			$oMail->SMTPAuth = true;
				$oMail->Host = MAIL_HOST;
				$oMail->Username = MAIL_USER;
				$oMail->Password = MAIL_PASS;
				$oMail->AddAddress('webmaster@yupsie.nl', 'Yupsie');
				$oMail->SetFrom('noreply@yupsie.nl', 'Yupsie No-Reply');
				$oMail->Subject = 'Yupsie.eu - Foutje... ' . $aLog['message'];
				$oMail->MsgHTML($sMailBody);
				$oMail->Send();
			}
			catch (phpmailerException $e)
			{
				echo $e->errorMessage();
			}
		}
		$this->oView->setError($e->getMessage(), str_replace(SITE_PATH, '', $e->getFile()), $e->getLine(), $e->getCode(), $e->getTraceAsString());
		return $this->oView->getHtml(DEBUG_MODE);	//	Set stacktrace true for debugging purposes according to DEBUG_MODE in /config.php
	}
}