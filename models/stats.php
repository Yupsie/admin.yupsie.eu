<?php //	namespace Models;

/**
 * 	class M_Main_Stats
 *	Load the main Model, other Models can extend this class
 *
 * 	@author yupsie
 */
class M_Stats extends M_Main {

	/**
	 *	function getData
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@return array
	 */
	public function getData() {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT		COUNT(*) AS totaal,
							to_char(bezoeken.datum_tijd, 'MM') AS maand,
							to_char(bezoeken.datum_tijd, 'YYYY') AS jaar,
							bezoeken.user_agent AS user_agent
				FROM		" . DB_PREFIX . "bezoeken AS bezoeken
				GROUP BY	to_char(bezoeken.datum_tijd, 'YYYYMM'),
							bezoeken.datum_tijd,
							bezoeken.user_agent
			",
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			if ((strpos(strtolower($aFetch['user_agent']), 'bot') !== false) || (strpos(strtolower($aFetch['user_agent']), 'spider') !== false) || (strpos(strtolower($aFetch['user_agent']), 'crawler') !== false)) {
				$sUserAgent = 'spider';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'firefox') !== false) {
				$sUserAgent = 'Firefox';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'opera') !== false) {
				$sUserAgent = 'Opera';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'chrom') !== false) {
				$sUserAgent = 'Chrome';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'msie 6') !== false) {
				$sUserAgent = 'Explorer 6.0';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'msie 7') !== false) {
				$sUserAgent = 'Explorer 7.0';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'msie 8') !== false) {
				$sUserAgent = 'Explorer 8.0';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'msie 9') !== false) {
				$sUserAgent = 'Explorer 9.0';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'msie 10') !== false) {
				$sUserAgent = 'Explorer 10.0';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'safari') !== false) {
				$sUserAgent = 'Safari';
			}
			elseif (strpos(strtolower($aFetch['user_agent']), 'khtml') !== false) {
				$sUserAgent = 'Konqueror';
			}
			else {
				$sUserAgent = 'Unknown';
			}	
			if (isset($aOutput[$sUserAgent][$aFetch['jaar'] . '-' . $aFetch['maand']])) {
				$aOutput[$sUserAgent][$aFetch['jaar'] . '-' . $aFetch['maand']] += $aFetch['totaal'];
			}
			else {
				$aOutput[$sUserAgent][$aFetch['jaar'] . '-' . $aFetch['maand']] = $aFetch['totaal'];
			}
		}
		foreach ($aOutput as $sUserAgent => $aValue) {
			foreach ($aValue as $sKey => $iValue) {
				$aOutputXY[$sUserAgent][] = $iValue;
			}
			$aOutputXYe[$sUserAgent] = $aOutputXY[$sUserAgent];
		}
		unset($aOutputXYe['spider']);
		ksort($aOutputXYe);
		return $aOutputXYe;
	}
	
	/**
	 *	function getDataPie
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@param integer iYear
	 *	@return array
	 */
	public function getDataPie($iYear = false) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT		COUNT(bezoeken.count) AS totaal,
							to_char(bezoeken.datum_tijd, 'YYYY') AS jaar,
							CASE 
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'bot') 		> 0 THEN 'Spider'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'spider') 	> 0 THEN 'Spider'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'crawler') 	> 0 THEN 'Spider'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'firefox') 	> 0 THEN 'Firefox'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'opera') 	> 0 THEN 'Opera'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'chrom') 	> 0 THEN 'Chrome'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'msie 6') 	> 0 THEN 'Explorer 6.0'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'msie 7') 	> 0 THEN 'Explorer 7.0'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'msie 8') 	> 0 THEN 'Explorer 8.0'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'msie 9') 	> 0 THEN 'Explorer 9.0'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'msie 10') 	> 0 THEN 'Explorer 10.0'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'safari') 	> 0 THEN 'Safari'
								WHEN STRPOS(LOWER(bezoeken.user_agent), 'khtml') 	> 0 THEN 'Konqueror'
								ELSE 'Onbekend'
							END AS agent
				FROM		" . DB_PREFIX . "bezoeken AS bezoeken
				GROUP BY	to_char(bezoeken.datum_tijd, 'YYYY'),
							agent
			",
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			if (isset($aOutput[$aFetch['agent']])) {
				$aOutput[$aFetch['jaar']][$aFetch['agent']] += $aFetch['totaal'];
			}
			else {
				$aOutput[$aFetch['jaar']][$aFetch['agent']] = $aFetch['totaal'];
			}
			ksort($aOutput[$aFetch['jaar']]);
			unset($aOutput[$aFetch['jaar']]['Spider']);
		}
		krsort($aOutput);
		return $aOutput;
	}
	
	/**
	 *	function getRowsNum
	 *	Return the number of records
	 *
	 *	@access public
	 *	@return integer
	 */
	public function getRowsNum() {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT		COUNT(*) AS totaal
				FROM		" . DB_PREFIX . "bezoeken AS bezoeken
			",
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$aFetch = pg_fetch_assoc($dbExecute);
		return $aFetch['totaal'];
	}
}