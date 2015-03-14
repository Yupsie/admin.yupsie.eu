<?php //	namespace Views;

/**
 *	class V_Sites
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_Sites extends V_Main {

	/**
	 *	function getConfig
	 *
	 *	@access private
	 *	@param string sConfig
	 *	@return array
	 */
	private function getConfig($sConfig) {
		$aDefines = array();
		$aGroupedDefines = array();
		$iState = 0;
		$sKey = '';
		$sValue = '';
		$aTypes = array(
			'DEBUG' => 'Debugging', 
			'DB' => 'Database', 
			'MAIL' => 'E-mail', 
			'SITE' => 'Website', 
			'DT' => 'Datum-tijd formaat'
		);

		$sFile = file_get_contents($sConfig);
		$sTokens = token_get_all($sFile);
		$token = reset($sTokens);
		while ($token) {
		    if (is_array($token)) {
		        if ($token[0] == T_WHITESPACE || $token[0] == T_COMMENT || $token[0] == T_DOC_COMMENT) {
		            // 	Do nothing
		        }
		        elseif ($token[0] == T_STRING && strtolower($token[1]) == 'define') {
		            $iState = 1;
		        }
		        elseif ($iState == 2 && C_Assets::is_constant($token[0])) {
		            $sKey = $token[1];
		            $iState = 3;
		        }
		        elseif ($iState == 4 && C_Assets::is_constant($token[0])) {
		            $sValue = $token[1];
		            $iState = 5;
		        }
		    }
		    else {
		        $sSymbol = trim($token);
		        if ($sSymbol == '(' && $iState == 1) {
		            $iState = 2;
		        }
		        elseif ($sSymbol == ',' && $iState == 3) {
		            $iState = 4;
		        }
		        elseif ($sSymbol == ')' && $iState == 5) {
		            $aDefines[C_Assets::strip($sKey)] = C_Assets::strip($sValue);
		            $iState = 0;
		        }
		    }
		    $token = next($sTokens);
		}
		foreach ($aDefines as $sDefinedKey => $sDefinedValue) {
			$aParts = explode('_', $sDefinedKey);
			$aGroupedDefines[$aTypes[trim($aParts[0])]][$sDefinedKey] = $sDefinedValue;
		}
		return $aGroupedDefines;
	}

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {

		if (defined('DB_ADMIN') && DB_ADMIN == true) {
			if (!is_array($aData)) {
				$this->sData .= preg_replace_callback('/<pre\b[^>]*lang="(.+?)"\s*(lines="([0-9,]*?)")?[^>]*>(.+?)<\/pre>/s', 'V_Main::stylizeCode', '
		<section>
			<h2 style="font-variant:small-caps;">Config</h2>
			<article>
				<pre lang="php">' . $aData . '</pre>
			</article>
		</section>');

			}
			else {
				$this->sData .= '
		<section>
			<form method="post">
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
							<td>DEBUG_MODE</td>
							<td class="input"><input type="text" name="DEBUG_MODE" 	value="1"></td>
							<td></td>
						</tr>

						<tr>
							<td>DB_HOST</td>
							<td class="input"><input type="text" name="DB_HOST" 	value="localhost"></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_PORT</td>
							<td class="input"><input type="text" name="DB_PORT" 	value="5432"></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_USER</td>
							<td class="input"><input type="text" name="DB_USER" 	value=""></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_PASS</td>
							<td class="input"><input type="text" name="DB_PASS" 	value=""></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_NAME</td>
							<td class="input"><input type="text" name="DB_NAME" 	value=""></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_PREFIX</td>
							<td class="input"><input type="text" name="DB_PREFIX" 	value="_"></td>
							<td></td>
						</tr>
						<tr>
							<td>DB_LANG</td>
							<td class="input"><input type="text" name="DB_LANG" 	value="nl"></td>
							<td></td>
						</tr>

						<tr>
							<td>MAIL_HOST</td>
							<td class="input"><input type="text" name="MAIL_HOST" 	value="smtp.mail.pcextreme.nl"></td>
							<td></td>
						</tr>
							<td>MAIL_USER</td>
							<td class="input"><input type="text" name="MAIL_USER" 	value="yupsie@yupsie.nl"></td>
							<td></td>
						</tr>
							<td>MAIL_PASS</td>
							<td class="input"><input type="text" name="MAIL_PASS" 	value=""></td>
							<td></td>
						</tr>
							<td>MAIL_TO</td>
							<td class="input"><input type="text" name="MAIL_TO" 	value=""></td>
							<td></td>
						</tr>
							<td>MAIL_TO_NAME</td>
							<td class="input"><input type="text" name="MAIL_TO_NAME"	value=""></td>
							<td></td>
						</tr>

						<tr>
							<td>SITE_TITLE_SEPARATOR</td>
							<td class="input"><input type="text" name="SITE_TITLE_SEPARATOR" value=" - "></td>
							<td></td>
						</tr>
						<tr>
							<td>SITE_TITLE</td>
							<td class="input"><input type="text" name="SITE_TITLE" value="CMS v2.0"></td>
							<td></td>
						</tr>
						<tr>
							<td>SITE_PATH</td>
							<td class="input"><input type="text" name="SITE_PATH" 	value=""></td>
							<td></td>
						</tr>

						<tr>
							<td>DT_FORMAT_DB_DATE</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_DATE" value="HH24:MI"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_DB_TIME</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_TIME" value="TMday DD TMmonth YYYY"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_DB_DATETIME</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_DATETIME" value="TMday DD TMmonth YYYY om HH24:MI"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_DB_RFC</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_RFC" value="Dy, DD Mon YYYY HH24:MI:SS TZ"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_DB_RSS</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_RSS" value="YYYY-MM-DD\Thh24:MI:SS\Z"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_DB_YEAR</td>
							<td class="input"><input type="text" name="DT_FORMAT_DB_YEAR" value="YYYY"></td>
							<td></td>
						</tr>

						<tr>
							<td>DT_FORMAT_PHP_DATE</td>
							<td class="input"><input type="text" name="DT_FORMAT_PHP_DATE" value="l d F Y"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_PHP_TIME</td>
							<td class="input"><input type="text" name="DT_FORMAT_PHP_TIME" value="H:i"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_PHP_DATETIME</td>
							<td class="input"><input type="text" name="DT_FORMAT_PHP_DATETIME" value="l d F Y \o\m H:i"></td>
							<td></td>
						</tr>
						<tr>
							<td>DT_FORMAT_PHP_RFC</td>
							<td class="input"><input type="text" name="DT_FORMAT_PHP_RFC" value="r"></td>
							<td></td>
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
		}
	}
}