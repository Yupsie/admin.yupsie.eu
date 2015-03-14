<?php //	namespace Controllers;

class C_Assets {

	/**
	 *	function file_size
	 *
	 *	@access public static
	 *	@param string sFile
	 *	@param string sSetup
	 *	return string
	 */
	public static function file_size($sFile, $sSetup = null) {
	    $iFileSize = ($sFile && @is_file($sFile)) ? filesize($sFile) : NULL;
	    $aSizeNames = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
	   
	   	if ($iFileSize > 0) {
		    if (!$sSetup && $sSetup !== 0) {
		        return number_format($iFileSize / pow(1024, $i = floor(log($iFileSize, 1024))), ($i >= 1) ? 2 : 0) . ' ' . $aSizeNames[$i];
		    } 
			elseif ($sSetup == 'INT') {
				return number_format($iFileSize);
			}
		    else {
				return number_format($iFileSize / pow(1024, $sSetup), ($sSetup >= 1) ? 2 : 0 ). ' ' . $aSizeNames[$sSetup];
			}
		}
		else {
			return 0;
		}
	}

	/**
	 *	function file_perms
	 *
	 *	@access public static
	 *	@param string sFile
	 *	return string
	 */
	public static function file_perms($sFile) {
		$hPerms = fileperms($sFile);

		if (($hPerms & 0xC000) == 0xC000) {
			// 	Socket
			$sInfo = 's';
		}
		elseif (($hPerms & 0xA000) == 0xA000) {
			// 	Symbolic Link
			$sInfo = 'l';
		}
		elseif (($hPerms & 0x8000) == 0x8000) {
			// 	Regular
			$sInfo = '-';
		}
		elseif (($hPerms & 0x6000) == 0x6000) {
			// 	Block special
			$sInfo = 'b';
		}
		elseif (($hPerms & 0x4000) == 0x4000) {
			// 	Directory
			$sInfo = 'd';
		}
		elseif (($hPerms & 0x2000) == 0x2000) {
			// 	Character special
			$sInfo = 'c';
		}
		elseif (($hPerms & 0x1000) == 0x1000) {
			// 	FIFO pipe
			$sInfo = 'p';
		}
		else {
			// 	Unknown
			$sInfo = 'u';
		}

		// 	Owner
		$sInfo .= (($hPerms & 0x0100) ? 'r' : '-');
		$sInfo .= (($hPerms & 0x0080) ? 'w' : '-');
		$sInfo .= (($hPerms & 0x0040) ?
					(($hPerms & 0x0800) ? 's' : 'x' ) :
					(($hPerms & 0x0800) ? 'S' : '-'));

		// 	Group
		$sInfo .= (($hPerms & 0x0020) ? 'r' : '-');
		$sInfo .= (($hPerms & 0x0010) ? 'w' : '-');
		$sInfo .= (($hPerms & 0x0008) ?
					(($hPerms & 0x0400) ? 's' : 'x' ) :
					(($hPerms & 0x0400) ? 'S' : '-'));

		// 	World
		$sInfo .= (($hPerms & 0x0004) ? 'r' : '-');
		$sInfo .= (($hPerms & 0x0002) ? 'w' : '-');
		$sInfo .= (($hPerms & 0x0001) ?
					(($hPerms & 0x0200) ? 't' : 'x' ) :
					(($hPerms & 0x0200) ? 'T' : '-'));

		return $sInfo;
	}
	
	/**
	 *	function is_constant
	 *
	 *	@access public static
	 *	@param string sToken
	 *	return string
	 */
	public static function is_constant($sToken) {
	    return $sToken == T_CONSTANT_ENCAPSED_STRING || $sToken == T_STRING || $sToken == T_LNUMBER || $sToken == T_DNUMBER;
	}

	/**
	 *	function dump
	 *
	 *	@access public static
	 *	@param integer iState
	 *	@param string sToken
	 *	return string
	 */
	public static function dump($iState, $token) {
	    if (is_array($token)) {
	        echo $iState . ': ' . token_name($token[0]) . ' ['  . $token[1] . '] on line ' . $token[2] . "\n";
	    }
	    else {
	        echo $iState . ': Symbol ' . $token . "\n";
	    }
	}

	/**
	 *	function strip
	 *
	 *	@access public static
	 *	@param string sValue
	 *	return string
	 */
	public static function strip($sValue) {
	    return preg_replace('!^([\'"])(.*)\1$!', '$2', $sValue);
	}
}