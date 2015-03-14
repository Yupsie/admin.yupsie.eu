<?php //	namespace Models;

/**
 * 	class M_Main_Users
 *	Load the main Model, other Models can extend this class
 *
 * 	@author yupsie
 */
class M_Users extends M_Main {

	/**
	 *	function getUsers
	 * 
	 *	@access public
	 *	@return array
	 */
	public function getData() {
			
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
	 *	function setData
	 * 
	 *	@access public
	 *	@return void
	 */
	public function setData() {

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
}