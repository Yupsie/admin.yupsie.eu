<?php //	namespace Views\Errorhandler;

/**
 *	class Views_Errorhandler_Main
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 22-04-2010
 *	@version 8.0.0
 */
class V_Errorhandler_Main extends V_Main {
	private $aErrors = array();
	private $aTrace = array();

	/**
	 *	function setError
	 *	Fill the array
	 *
	 *	@access public
	 *	@param string sTitle
	 *	@param string sFile
	 *	@param integer iLine
	 *	@param integer iNumber
	 *	@param string sTrace
	 *	@return void
	 */
	public function setError($sTitle, $sFile, $iLine, $iNumber = '', $sTrace = '') {
		$this->aErrors['title'] = $sTitle;
		$this->aErrors['file'] = $sFile;
		$this->aErrors['line'] = $iLine;
		$this->aErrors['number'] = $iNumber;
		$this->aErrors['trace'] = $sTrace;
	}

	/**
	 *	function getHtml
	 *	Echo the HTML output
	 *
	 *	@access public
	 *	@param boolean bTrace
	 *	@return void
	 */
	public function getHtml($bTrace = false) {
		$aTitle = explode(' - ', $this->aErrors['title']);

		if (count($aTitle) == 3) {
			switch ($aTitle[1]) {
				/*	
					1xx Informational
					Request received, continuing process.
					This class of status code indicates a provisional response, consisting only of the Status-Line and optional headers, and is terminated by an empty line. 
					Since HTTP/1.0 did not define any 1xx status codes, servers must not send a 1xx response to an HTTP/1.0 client except under experimental conditions.
				*/
				case '100':
					$sMessage = 'Continue';
					break;

				case '101':
					$sMessage = 'Switching Protocols';
					break;

				case '102':
					$sMessage = 'Processing';						//	WebDAV; RFC 2518
					break;

				/*
					2xx Success
					This class of status codes indicates the action requested by the client was received, understood, accepted and processed successfully.
				*/
				case '200':
					$sMessage = 'OK';
					break;

				case '201':
					$sMessage = 'Created';
					break;

				case '202':
					$sMessage = 'Accepted';
					break;

				case '203':
					$sMessage = 'Non-Authoritative Information';	//	since HTTP/1.1
					break;

				case '204':
					$sMessage = 'No Content';
					break;

				case '205':
					$sMessage = 'Reset Content';
					break;

				case '206':
					$sMessage = 'Partial Content';
					break;

				case '207':
					$sMessage = 'Multi-Status';						//	WebDAV; RFC 4918
					break;

				case '208':
					$sMessage = 'Already Reported';					//	WebDAV; RFC 5842
					break;

				case '226':
					$sMessage = 'IM Used';							//	RFC 3229
					break;

				/*
					3xx Redirection
					The client must take additional action to complete the request.
					This class of status code indicates that further action needs to be taken by the user agent to fulfil the request. The action required may be carried 
					out by the user agent without interaction with the user if and only if the method used in the second request is GET or HEAD. A user agent should not 
					automatically redirect a request more than five times, since such redirections usually indicate an infinite loop.
				*/
				case '300':
					$sMessage = 'Multiple Choices';
					break;

				case '301':
					$sMessage = 'Moved Permanently';
					break;

				case '302':
					$sMessage = 'Found';
					break;

				case '303':
					$sMessage = 'See Other';						//	since HTTP/1.1
					break;
				case '304':
					$sMessage = 'Not Modified';
					break;

				case '305':
					$sMessage = 'Use Proxy';						//	since HTTP/1.1
					break;

				case '306':
					$sMessage = 'Switch Proxy';
					break;

				case '307':
					$sMessage = 'Temporary Redirect';				//	since HTTP/1.1
					break;

				case '308':
					$sMessage = 'Permanent Redirect';				//	approved as experimental RFC[12]
					break;

				/*
					4xx Client Error
					The 4xx class of status code is intended for cases in which the client seems to have erred. Except when responding to a HEAD request, the server 
					should include an entity containing an explanation of the error situation, and whether it is a temporary or permanent condition. These status 
					codes are applicable to any request method. User agents should display any included entity to the user.
				*/
				case '400':
					$sMessage = 'Bad Request';
					break;

				case '401':
					$sMessage = 'Unauthorized';
					break;

				case '402':
					$sMessage = 'Payment Required';
					break;

				case '403':
					$sMessage = 'Forbidden';
					break;

				case '404':
					$sMessage = 'Not Found';
					break;

				case '405':
					$sMessage = 'Method Not Allowed';
					break;

				case '406':
					$sMessage = 'Not Acceptable';
					break;

				case '407':
					$sMessage = 'Proxy Authentication Required';
					break;

				case '408':
					$sMessage = 'Request Timeout';
					break;

				case '409':
					$sMessage = 'Conflict';
					break;

				case '410':
					$sMessage = 'Gone';
					break;

				case '411':
					$sMessage = 'Length Required';
					break;

				case '412':
					$sMessage = 'Precondition Failed';
					break;

				case '413':
					$sMessage = 'Request Entity Too Large';
					break;

				case '414':
					$sMessage = 'Request-URI Too Long';
					break;

				case '415':
					$sMessage = 'Unsupported Media Type';
					break;

				case '416':
					$sMessage = 'Requested Range Not Satisfiable';
					break;

				case '417':
					$sMessage = 'Expectation Failed';
					break;

				case '418':
					$sMessage = 'I\'m a teapot';					//	(RFC 2324)
					break;

				case '420':
					$sMessage = 'Enhance Your Calm';				//	(Twitter)
					break;

				case '422':
					$sMessage = 'Unprocessable Entity';				//	(WebDAV; RFC 4918)
					break;

				case '423':
					$sMessage = 'Locked';							//	(WebDAV; RFC 4918)
					break;

				case '424':
					$sMessage = 'Failed Dependency';				//	(WebDAV; RFC 4918)
					break;

				case '424':
					$sMessage = 'Method Failure';					//	(WebDAV)[14]
					break;

				case '425':
					$sMessage = 'Unordered Collection';				//	(Internet draft)
					break;

				case '426':
					$sMessage = 'Upgrade Required';					//	(RFC 2817)
					break;

				case '428':
					$sMessage = 'Precondition Required';			//	(RFC 6585)
					break;

				case '429':
					$sMessage = 'Too Many Requests';				//	(RFC 6585)
					break;

				case '431':
					$sMessage = 'Request Header Fields Too Large';	//	(RFC 6585)
					break;

				case '444':
					$sMessage = 'No Response';						//	(Nginx)
					break;

				case '449':
					$sMessage = 'Retry With';						//	(Microsoft)
					break;

				case '450':
					$sMessage = 'Blocked by Windows Parental Controls';	//	(Microsoft)
					break;

				case '451':
					$sMessage = 'Unavailable For Legal Reasons';	//	(Internet draft)
					break;

				case '451':
					$sMessage = 'Redirect';							//	(Microsoft)
					break;

				case '494':
					$sMessage = 'Request Header Too Large';			//	(Nginx)
					break;

				case '495':
					$sMessage = 'Cert Error';						//	(Nginx)
					break;

				case '496':
					$sMessage = 'No Cert';							//	Nginx
					break;

				case '497':
					$sMessage = 'HTTP to HTTPS';					//	Nginx
					break;

				case '499':
					$sMessage = 'Client Closed Request';			//	Nginx
					break;

				/*
					5xx Server Error
					The server failed to fulfill an apparently valid request.
					Response status codes beginning with the digit "5" indicate cases in which the server is aware that it has encountered an error or is otherwise incapable of performing 
					the request. Except when responding to a HEAD request, the server should include an entity containing an explanation of the error situation, and indicate whether it is 
					a temporary or permanent condition. Likewise, user agents should display any included entity to the user. These response codes are applicable to any request method.
				*/
				case '500':
					$sMessage = 'Internal Server Error';
					break;

				case '501':
					$sMessage = 'Not Implemented';
					break;

				case '502':
					$sMessage = 'Bad Gateway';
					break;

				case '503':
					$sMessage = 'Service Unavailable';
					break;

				case '504':
					$sMessage = 'Gateway Timeout';
					break;

				case '505':
					$sMessage = 'HTTP Version Not Supported';
					break;

				case '506':
					$sMessage = 'Variant Also Negotiates';			//	RFC 2295
					break;

				case '507':
					$sMessage = 'Insufficient Storage';				//	WebDAV; RFC 4918
					break;

				case '508':
					$sMessage = 'Loop Detected';					//	WebDAV; RFC 5842
					break;

				case '509':
					$sMessage = 'Bandwidth Limit Exceeded';			//	Apache bw/limited extension
					break;

				case '510':
					$sMessage = 'Not Extended';						//	RFC 2774
					break;

				case '511':
					$sMessage = 'Network Authentication Required';	//	RFC 6585
					break;

				case '598':
					$sMessage = 'Network read timeout error';		//	Unknown
					break;

				case '599':
					$sMessage = 'Network connect timeout error';	//	Unknown
					break;

				default:
					$sMessage = 'OK';
					break;
			}
			header("HTTP/1.0 " . $aTitle[1] . " " . $sMessage);
		}
		echo $this->getHeader() . '
			<section>
				<h2>' . $aTitle[0] . '</h2>
				<article>
					<p style="text-align:right;">
						<span style="display:inline-block;">' . (isset($sMessage) ? $sMessage : '') . '<br>' . (isset($aTitle[2]) ? $aTitle[2] : '') . '<br></span>
						<strong style="font-size:5.6em;font-weight:normal;display:inline-block;padding-right:20px;">' . (isset($aTitle[1]) ? $aTitle[1] : '') . '</strong>
					</p>
					<div id="game" style="padding-top:50px;"></div>
				</article>';

		if ($bTrace == 1) {
			echo '
				<script type="text/javascript">
					if (window.console && window.console.log) {
						console.error("%c ' . (isset($aTitle[1]) ? $aTitle[1] . ' ' : '') . (isset($sMessage) ? $sMessage : '') . '", "color:#ff0000;font-weight:bold;font-size:32px;");
						console.log("%c Bestand: ' . $this->aErrors['file'] . '", "");
						console.log("%c Regel #: ' . $this->aErrors['line'] . '", "");
						console.log("%c Foutcode: ' . $this->aErrors['number'] . '", "");
						console.log("");
						console.log("%c Stack trace", "color:#ff0000;font-weight:bold;font-size:24px;");';
			$this->aTrace = explode('#', str_replace('#0', '', $this->aErrors['trace']));
			foreach ($this->aTrace as $sKey => $sValue) {
				echo '
						console.log("%c ' . str_replace(array("\n", "\r"), '', substr($sValue, 1)) . '", "");';
			}
			echo '
					}
				</script>';
		}
		echo '
			</section>';
		echo $this->getFooter();
	}
}