<?php //	namespace Controllers\Phpmailer;

/**
 *	class Controllers_Phpmailer_Pop3
 *	Specifically for PHPMailer to allow POP before SMTP authentication. Does not yet work with APOP - if you have an APOP account, contact me and we can test changes to this script. This class is based on the structure of the SMTP class by Chris Ryan. This class is rfc 1939 compliant and implements all the commands required for POP3 connection, authentication and disconnection.
 *
 *	@package PHPMailer
 *	@author Richard Davey
 */
class C_Phpmailer_Pop3 {

	public $POP3_PORT = 110; 	// 	Default POP3 port
	public $POP3_TIMEOUT = 30; 	// 	Default timeout
	public $CRLF = "\r\n";		// 	POP3 Carriage Return + Line Feed
	public $do_debug = 2;		// 	Displaying Debug warnings? (0 = now, 1+ = yes)
	public $host;				// 	POP3 Mail Server
	public $port;				// 	POP3 Port
	public $tval;				// 	POP3 Timeout Value
	public $username;			// 	POP3 Username
	public $password;			// 	POP3 Password

	private $pop_conn;
	private $connected;
	private $error;				// 	Error log array


	/**
	 *	function __construct
	 *	Sets the initial values
	 *
	 *	@access public
	 *	@return void
	 */
	public function __construct() {
		$this->pop_conn = 0;
		$this->connected = false;
		$this->error = null;
	}

	/**
	 *	function Authorise
	 *	Combination of public events - connect, login, disconnect
	 *
	 *	@access public
	 *	@param string host
	 *	@param integer port
	 *	@param integer tval
	 *	@param string username
	 *	@param string password
	 *	@param integer debug_level
	 *	@return boolean
	 */
	public function Authorise ($host, $port = false, $tval = false, $username, $password, $debug_level = 0) {
		$this->host = $host;

		//  If no port value is passed, retrieve it
		if ($port == false) {
			$this->port = $this->POP3_PORT;
		} else {
			$this->port = $port;
		}

		//  If no port value is passed, retrieve it
		if ($tval == false) {
			$this->tval = $this->POP3_TIMEOUT;
		} else {
			$this->tval = $tval;
		}

		$this->do_debug = $debug_level;
		$this->username = $username;
		$this->password = $password;

		//  Refresh the error log
		$this->error = null;

		//  Connect
		$result = $this->Connect($this->host, $this->port, $this->tval);

		if ($result) {
			$login_result = $this->Login($this->username, $this->password);

			if ($login_result) {
				$this->Disconnect();

				return true;
			}
		}

		//  We need to disconnect regardless if the login succeeded
		$this->Disconnect();

		return false;
	}

	/**
	 *	function Connect
	 *	Connect to the POP3 server
	 *
	 *	@access public
	 *	@param string host
	 *	@param integer port
	 *	@param integer tval
	 *	@return boolean
	 */
	public function Connect ($host, $port = false, $tval = 30) {
		//  Are we already connected?
		if ($this->connected) {
			return true;
		}

		// 	On Windows this will raise a PHP Warning error if the hostname doesn't exist. Rather than supress it with @fsockopen, let's capture it cleanly instead
		set_error_handler(array(&$this, 'catchWarning'));

		//  Connect to the POP3 server
		$this->pop_conn = fsockopen($host,    //  POP3 Host
					  $port,    //  Port #
					  $errno,   //  Error Number
					  $errstr,  //  Error Message
					  $tval);   //  Timeout (seconds)

		//  Restore the error handler
		restore_error_handler();

		//  Does the Error Log now contain anything?
		if ($this->error && $this->do_debug >= 1) {
			$this->displayErrors();
		}

		//  Did we connect?
		if ($this->pop_conn == false) {
			//  It would appear not...
			$this->error = array(
				'error' => "Failed to connect to server $host on port $port",
				'errno' => $errno,
				'errstr' => $errstr
			);

			if ($this->do_debug >= 1) {
				$this->displayErrors();
			}

			return false;
		}

		//  Increase the stream time-out

		//  Check for PHP 4.3.0 or later
		if (version_compare(phpversion(), '5.0.0', 'ge')) {
			stream_set_timeout($this->pop_conn, $tval, 0);
		}
		else {
			//  Does not work on Windows
			if (substr(PHP_OS, 0, 3) !== 'WIN') {
				socket_set_timeout($this->pop_conn, $tval, 0);
			}
		}

		//  Get the POP3 server response
		$pop3_response = $this->getResponse();

		//  Check for the +OK
		if ($this->checkResponse($pop3_response)) {
			//  The connection is established and the POP3 server is talking
			$this->connected = true;
			return true;
		}
	}

	/**
	 *	function Login
	 *	Login to the POP3 server (does not support APOP yet)
	 *
	 *	@access public
	 *	@param string username
	 *	@param string password
	 *	@return boolean
	 */
	public function Login ($username = '', $password = '') {
		if ($this->connected == false) {
			$this->error = 'Not connected to POP3 server';

			if ($this->do_debug >= 1) {
				$this->displayErrors();
			}
		}

		if (empty($username)) {
			$username = $this->username;
		}

		if (empty($password)) {
			$password = $this->password;
		}

		$pop_username = "USER $username" . $this->CRLF;
		$pop_password = "PASS $password" . $this->CRLF;

		//  Send the Username
		$this->sendString($pop_username);
		$pop3_response = $this->getResponse();

		if ($this->checkResponse($pop3_response)) {
			//  Send the Password
			$this->sendString($pop_password);
			$pop3_response = $this->getResponse();

			if ($this->checkResponse($pop3_response)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	/**
	 *	function Disconnect
	 *	Disconnect from the POP3 server
	 *
	 *	@access public
	 *	@return void
	 */
	public function Disconnect () {
		$this->sendString('QUIT');

		fclose($this->pop_conn);
	}

	/**
	 *	function getResponse
	 *	Get the socket response back. $size is the maximum number of bytes to retrieve
	 *
	 *	@access private
	 *	@param integer size
	 *	@return string
	 */
	private function getResponse ($size = 128) {
		$pop3_response = fgets($this->pop_conn, $size);

		return $pop3_response;
	}

	/**
	 *	function sendString
	 *	Send a string down the open socket connection to the POP3 server
	 *
	 *	@access private
	 *	@param string string
	 *	@return integer
	 */
	private function sendString ($string) {
		$bytes_sent = fwrite($this->pop_conn, $string, strlen($string));

		return $bytes_sent;
	}

	/**
	 *	function checkResponse
	 *	Checks the POP3 server response for +OK or -ERR
	 *
	 *	@access private
	 *	@param string string
	 *	@return boolean
	 */
	private function checkResponse ($string) {
		if (substr($string, 0, 3) !== '+OK') {
			$this->error = array(
				'error' => "Server reported an error: $string",
				'errno' => 0,
				'errstr' => ''
			);

			if ($this->do_debug >= 1) {
				$this->displayErrors();
			}

			return false;
		}
		else {
			return true;
		}
	}

	/**
	 *	function displayErrors
	 *	If debug is enabled, display the error message array
	 *
	 *	@access private
	 *	@return void
	 */
	private function displayErrors () {
		echo '<pre>';

		foreach ($this->error as $single_error) {
			print_r($single_error);
		}

		echo '</pre>';
	}

	/**
	 *	function catchWarning
	 *	Takes over from PHP for the socket warning handler
	 *
	 *	@access private
	 *	@param integer errno
	 *	@param string errstr
	 *	@param string errfile
	 *	@param integer errline
	 *	@return void
	 */
	private function catchWarning ($errno, $errstr, $errfile, $errline) {
		$this->error[] = array(
		  'error' => "Connecting to the POP3 server raised a PHP warning: ",
		  'errno' => $errno,
		  'errstr' => $errstr
		);
	}
}