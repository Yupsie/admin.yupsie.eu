<?php //	namespace Models;

/**
 * 	class M_Main
 *	Load the main Model, other Models can extend this class
 *
 * 	@author yupsie
 */
class M_Main {
	protected $dbConnection;
	protected $sData = '';
	
	/**
	 *	function __construct
	 *	Connect to database
	 * 
	 *	@access public
	 *	@param string sHost
	 *	@param string sName
	 *	@param string sUser
	 *	@param string sPass 
	 *	@return void
	 */
	public function __construct() {
		if (!$this->dbConnection = pg_connect('dbname=' . DB_NAME . ' user=' . DB_USER . ' password=' . DB_PASS . ' host=' . DB_HOST)) {
			throw new Exception(CMS_ERROR_CONN);
		}
	}
	
	/**
	 *	function getData
	 *	Get all tables in a database starting with the given prefix
	 * 
	 *	@access public
	 *	@return array 
	 */
	public function getData() {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		tablename 
				FROM 		pg_tables 
				WHERE 		tablename ~ '^" . DB_PREFIX . "+'
					AND 	tablename != '" . DB_PREFIX . "bezoeken'
			", 
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			$aOutput[] = $aFetch;
		}
		return $aOutput;
	}


	/**
	 * 	function getPrivileges
	 * 
	 * 	@access public
	 * 	@return array
	 */
	public function getPrivileges($sUser, $sTable = false, $sAction = false) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		usename, 
							relname AS relation,
       						priv
				FROM 		pg_class 
				JOIN 		pg_namespace 
					ON 		pg_namespace.oid = pg_class.relnamespace,
     						pg_user,
     						(values('SELECT', 1), ('INSERT', 2), ('UPDATE', 3), ('DELETE', 4)) privs(priv, privorder)
				WHERE 		relkind IN('r', 'v')
					AND 	nspname = 'public'
					AND		usename = $1
					AND 	relkind = 'r'
      				AND 	has_table_privilege(pg_user.usesysid, pg_class.oid, priv)
      				AND 	NOT(nspname ~ '^pg_' OR nspname = 'information_schema')
				ORDER BY 	2, 1, 3, privorder;
			", 
			array($sUser))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			$aOutput[$aFetch['relation']][] = $aFetch['priv'];
		}
		if ($sTable) {
			if ($sAction) {
				if (in_array($sAction, $aOutput[$sTable])) {
					return true;
				}
				else {
					return false;
				}
			}
			return $aOutput[$sTable];
		}
		return $aOutput;
	}

	/**
	 *	function setData
	 *
	 *	@access public
	 *	@param string sTable
	 *	@param string sQueryString
	 *	@param array aPosted
	 *	@return boolean
	 */
	public function setData($sTable, $sQueryString, $aPosted) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				UPDATE 		" . $sTable . " 
				SET 		" . $sQueryString . " 
				WHERE 		id = $1
			", 
			$aPosted)) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			return true;
		}
	}

	/**
	 *	function addData
	 *
	 *	@access public
	 *	@param string sTable
	 *	@param string sQueryStringA
	 *	@param string sQueryStringB
	 *	@param array aPosted
	 *	@return boolean
	 */
	public function addData($sTable, $sQueryStringA, $sQueryStringB, $aPosted) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				INSERT INTO " . $sTable . " (
							" . $sQueryStringA . "
				) 
				VALUES (
							" . $sQueryStringB . "
				)
			", 
			$aPosted)) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			return true;
		}
	}

	/**
	 *	function trashData
	 *
	 *	@access public
	 *	@param string sTable
	 *	@param integer iPosted
	 *	@return boolean
	 */
	public function trashData($sTable, $iPosted) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				UPDATE 		" . $sTable . " 
				SET 		verwijderd = 't'
				WHERE 		id = $1
			", 
			array($iPosted))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			return true;
		}
	}
	
	/**
	 *	function getColumns
	 *	Get all columns of the specified table, removed columns get the status 'dropped' so avoid those
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@return array
	 */
	public function getColumns($sTableName) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		attname 
				FROM 		pg_attribute 
				WHERE 		attrelid = (
								SELECT 		oid 
								FROM 		pg_class 
								WHERE 		relname = $1
							) 
					AND 	attnum > 0 
					AND 	attname NOT LIKE '%dropped%'
					AND 	attname != 'verwijderd'
			", 
			array($sTableName))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			$aOutput[] = $aFetch;
		}
		return $aOutput;
	}
	
	/**
	 *	function getColumnsNum
	 *	Get number of columns of the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@return integer
	 */
	public function getColumnsNum($sTableName) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		* 
				FROM 		" . $sTableName . " 
				ORDER BY 	id DESC
			", 
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$iFields = pg_num_fields($dbExecute);
		return $iFields;
	}
	
	/**
	 *	function getColumnsType
	 *	Get the type of the given field in the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@param integer iColumn
	 *	@return string
	 */
	public function getColumnsType($sTableName, $iColumn) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		* 
				FROM 		" . $sTableName
			, 
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			$sType = pg_field_type($dbExecute, $iColumn);
		}
		return $sType;
	}
	
	/**
	 *	function getColumnsSize
	 *	Get the bytesize of the given field in the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@param integer iColumn
	 *	@return string
	 */
	public function getColumnsSize($sTableName, $iColumn) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		* 
				FROM 		" . $sTableName
			, 
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			$iSize = pg_field_size($dbExecute, $iColumn);
		}
		return $iSize;
	}
	
	/**
	 *	function getRows
	 *	Get all rows of the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@return array
	 */
	public function getRows($sTableName, $iRow = 0) {
		if ($iRow == 0) {
			if (!$dbExecute = pg_query_params(
				$this->dbConnection, "
					SELECT 		* 
					FROM 		" . $sTableName . " 
					WHERE		verwijderd = 'f'
					ORDER BY 	id DESC
				", 
				array())) {
				throw new Exception(CMS_ERROR_QUERY);
			}
		}
		else {
			if (!$dbExecute = pg_query_params(
				$this->dbConnection, "
					SELECT 		* 
					FROM 		" . $sTableName . " 
					WHERE 		id = $1
				", 
				array($iRow))) {
				throw new Exception(CMS_ERROR_QUERY);
			}
		}
		
		$aOutput = array();
		while ($aFetch = pg_fetch_assoc($dbExecute)) {
			$aOutput[] = $aFetch;
		}
		return $aOutput;
	}
	
	/**
	 *	function getRowsNum
	 *	Get the number of rows of the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@return integer
	 */
	public function getRowsNum($sTableName) {
		if (!$dbExecute = pg_query_params(
			$this->dbConnection, "
				SELECT 		* 
				FROM 		" . $sTableName . " 
				ORDER BY 	id DESC
			", 
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$iRows = pg_num_rows($dbExecute);
		return $iRows;
	}
	
	/**
	 *	function setRows
	 *	Insert or update a row in the given table
	 * 
	 *	@access public
	 *	@param string sTableName
	 *	@param boolean bInsert
	 *	@return boolean
	 */
	public function setRows($sTableName, $sAction = 'edit') {
		$aPosted = array();
		$sQueryString = '';
		$i = 0;
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			foreach ($_POST as $sPostField => $sPostValue) {
				if (($sPostField != 'table') && ($sPostField != 'id') && ($sPostField != 'action')) {
					$aPosted[$i] = $sPostValue;
					$i++;
					$j = $i + 1;
					$sQueryString .= (($i != 1)?', ':'') . $sPostField . '= $' .$j;
				}
			}
			array_unshift($aPosted, $_POST['id']);
		}
		
		if ($sAction == 'edit') {
			if (!$dbExecute = pg_query_params(
				$this->dbConnection, "
					UPDATE 		" . $_POST['table'] . " 
					SET 		" . $sQueryString . " 
					WHERE 		id = $1
				", 
				$aPosted)) {
				throw new Exception(CMS_ERROR_QUERY);
			}
		}
		elseif ($sAction == 'add') {
			if (!$dbExecute = pg_query_params(
				$this->dbConnection, "
					INSERT INTO " . $_POST['table'] . " (
								" . $sQueryStringA . "
					) 
					VALUES (
								" . $sQueryStringB . "
					)
				", 
				$aPosted)) {
				throw new Exception(CMS_ERROR_QUERY);
			}
		}
		elseif ($sAction == 'delete') {
			if (!$dbExecute = pg_query_params(
				$this->dbConnection, "
					UPDATE 		" . $_POST['table'] . " 
					SET 		verwijderd = 't' 
					WHERE 		id = $1
				", 
				$aPosted)) {
				throw new Exception(CMS_ERROR_QUERY);
			}
		}
		return true;
	}

	/**
	 *	function getUsers
	 * 
	 *	@access public
	 *	@return array
	 */
	public function getUsers() {
			
		if (!$dbExecuteColumns = pg_query_params(
			$this->dbConnection, "
				SELECT 		attname 
				FROM 		pg_attribute 
				WHERE 		attrelid = (
								SELECT 		oid 
								FROM 		pg_class 
								WHERE 		relname = $1
							) 
					AND 	attnum > 0 
					AND 	attname NOT LIKE '%dropped%'
			", 
			array('pg_user'))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			$dbExecuteColumnsFoot = $dbExecuteColumns;
			while ($aFetchColumns = pg_fetch_assoc($dbExecuteColumns)) {
				$aOutput['columns'][] = $aFetchColumns;
			}
			
			if (!$dbExecuteRows = pg_query_params(
				$this->dbConnection, "
					SELECT 	*
					FROM 	pg_user
				",
				array())) {
				throw new Exception(CMS_ERROR_QUERY);
			}
			else {
				$aOutput['num_fields'] = pg_num_fields($dbExecuteRows);
				$aOutput['num_rows'] = pg_num_rows($dbExecuteRows);
				while ($aFetchRows = pg_fetch_assoc($dbExecuteRows)) {
					if (!$dbExecuteRowColumns = pg_query_params(
						$this->dbConnection, "
							SELECT 		attname
							FROM 		pg_attribute 
							WHERE 		attrelid = (
											SELECT 		oid 
											FROM 		pg_class 
											WHERE 		relname = $1
										) 
								AND 	attnum > 0 
								AND 	attname NOT LIKE '%dropped%'
						", 
						array('pg_user'))) {
						throw new Exception(CMS_ERROR_QUERY);
					}
					else {
						$aOutput['rows'][] = $aFetchRows;
					}
				}
			}
		}
			
		if (!$dbExecuteColumns = pg_query_params(
			$this->dbConnection, "
				SELECT 		attname 
				FROM 		pg_attribute 
				WHERE 		attrelid = (
								SELECT 		oid 
								FROM 		pg_class 
								WHERE 		relname = $1
							) 
					AND 	attnum > 0 
					AND 	attname NOT LIKE '%dropped%'
			", 
			array('pg_user'))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			$dbExecuteColumnsFoot = $dbExecuteColumns;
			while ($aFetchColumns = pg_fetch_assoc($dbExecuteColumns)) {
			//	$this->sData .= '
			//			<th>' . $aFetchColumns['attname'] . '</th>';
			}
		}
		
		if (!$dbExecuteDb = pg_query_params(
			$this->dbConnection, "
				SELECT 		datname 
				FROM 		pg_database
			",
			array())) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		$this->sData .= '<select>';
		while ($aFetchDb = pg_fetch_assoc($dbExecuteDb)) {
			if (!$dbExecuteTable = pg_query_params(
				$this->dbConnection, "
					SELECT 		tablename 
					FROM 		pg_tables 
					WHERE 		tablename ~ '^" . DB_PREFIX . "+'
				", 
				array())) {
				$this->sData .= pg_last_error();
			}

			while ($aFetchTable = pg_fetch_assoc($dbExecuteTable)) {
				$aOutput['db'][$aFetchDb['datname']][] = $aFetchTable['tablename'];
			}
		}
		return $aOutput;
	}

	/**
	 *	function setUser
	 * 
	 *	@access public
	 *	@return void
	 */
	public function setUser() {

		if (!$dbExecuteSelect = pg_query_params(
			$this->dbConnection, "
				SELECT 		* 
				FROM 		" . $_GET['table'] . " 
				WHERE 		usesysid = $1
			", 
			array($_GET['id']))) {
			throw new Exception(CMS_ERROR_QUERY);
		}
		else {
			$aFetchSelect = pg_fetch_assoc($dbExecuteSelect);
			$i = 0;
			//foreach($aFetchSelect as $sField => $sValue)
		}
	}

	/**
	 *	function getVisitData
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@return array
	 */
	public function getVisitData() {
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
			//	$aOutputXY[$sUserAgent][] = array($sKey, $iValue);
				$aOutputXY[$sUserAgent][$sKey] = $iValue;
			}
			$aOutputXYe[$sUserAgent] = $aOutputXY[$sUserAgent];
		}
		unset($aOutputXYe['spider']);
		ksort($aOutputXYe);
		return $aOutputXYe;
	}
	
	/**
	 *	function getVisitDataPie
	 *	Return the data from the query in a multidimensional array
	 *
	 *	@access public
	 *	@param integer iYear
	 *	@return array
	 */
	public function getVisitDataPie($iYear = false) {
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
	 *	function getVisitDataCount
	 *	Return the number of records
	 *
	 *	@access public
	 *	@return integer
	 */
	public function getVisitDataCount() {
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

	/**
	 * 	function getSiteConfigs
	 * 
	 * 	@access public
	 * 	@return array
	 */
	public function getSiteConfigs() {
		$aData = array();

		if ($dirHandle = opendir(SITES_PATH)) {
		    while (false !== ($sFileName = readdir($dirHandle))) {
		        if (($sFileName != ".") && ($sFileName != "..")) {
					$aData[] = array(
						'name'	 		=> $sFileName,
						'path'			=> $sFileName . '/config.php',
						'sitename'		=> $sFileName,
					//	'permissions' 	=> C_Assets::file_perms($sFileName . '/config.php')
					);
		        }
		    }
		    closedir($dirHandle);
		}
		return $aData;
	}
}