<?php //	namespace Views;

/**
 *	class V_User
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_User extends V_Main {

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {

		if (isset($aData['columns'])) {
			$this->sData .= '
		<section id="pg_user">
			<h2 style="font-variant:small-caps;">' . ucwords(str_replace('_', ' ', 'pg_user')) . '</h2>
			<table>
				<thead>
					<tr>';

			foreach ($aData['columns'] as $aColumns) {
				$this->sData .= '
						<th>' . $aColumns['attname'] . '</th>';
			}
		
			$this->sData .= '
						<th class="edit">
							<a href="add/"  class="del_icon">N</a>
						</th>
						<th class="delete"><sup>' . $aData['num_fields'] . '</sup>/<sub>' . $aData['num_rows'] . '</sub></th>
					</tr>
				</thead>
				<tbody>';

			foreach ($aData['rows'] as $aRows) {
				$this->sData .= '
					<tr>';
				foreach ($aRows as $sRow) {
					$this->sData .= '
						<td>' . $sRow . '</td>';
				}
				$this->sData .= '
						<td><a href="' . $aRows['usesysid'] . '/edit/" class="edit_icon">V</a></td>
						<td><a href="' . $aRows['usesysid'] . '/delete/" class="del_icon">I</a></td>
					</tr>';
			}
			$this->sData .= '
				</tbody>
				<tfoot>
					<tr>';

			foreach ($aData['columns'] as $aColumns) {
				$this->sData .= '
						<th>' . $aColumns['attname'] . '</th>';
			}
			$this->sData .= '
						<th class="edit">
							<a href="add/" class="del_icon">N</a>
						</th>
						<th class="delete"><sup>' . $aData['num_fields'] . '</sup>/<sub>' . $aData['num_rows'] . '</sub></th>
					</tr>
				</tfoot>
			</table>
		</section>';
		}
		else {
			$this->sData .= '
		<section id="pg_user">
			<h2 style="font-variant:small-caps;">' . ucwords(str_replace('_', ' ', 'pg_user')) . '</h2>
			<table>
				<thead>
					<tr>
						<th>Tablename</th>
						<th>DELETE</th>
						<th>INSERT</th>
						<th>SELECT</th>
						<th>UPDATE</th>
						<th class="edit">
							<a href="add/" class="del_icon">N</a>
						</th>
						<th class="delete">&nbsp;</th>
					</tr>
				</thead>
				<tbody>';
			foreach ($aData as $sTable => $aPrivileges) {
				$this->sData .= '
					<tr>
						<td>' . $sTable . '</td>
						<td>' . (in_array('DELETE', $aPrivileges) ? 'true' : 'false') . '</td>
						<td>' . (in_array('INSERT', $aPrivileges) ? 'true' : 'false') . '</td>
						<td>' . (in_array('SELECT', $aPrivileges) ? 'true' : 'false') . '</td>
						<td>' . (in_array('UPDATE', $aPrivileges) ? 'true' : 'false') . '</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>';

			}
			$this->sData .= '
				</tbody>
				<tfoot>
					<tr>
						<th>Tablename</th>
						<th>DELETE</th>
						<th>INSERT</th>
						<th>SELECT</th>
						<th>UPDATE</th>
						<th class="edit">
							<a href="add/" class="del_icon">N</a>
						</th>
						<th class="delete">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</section>';
		}
	}

	/**
	 *	function setUser
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setUser($aData = array()) {
		$this->sData .= '
		<div class="padding">
		<section class="edit">
			<h2>' . $aData['table'] . '</h2>
			<form method="post">
				<input type="hidden" name="table" value="'.$aData['table'].'">
				<input type="hidden" name="id" value="'.$aData['id'].'">
				<table>
					<thead>
						<tr>
							<th>' . CMS_TABLE_FIELDNAME . '</th>
							<th>' . CMS_TABLE_FIELDCONTENT . '</th>
							<th><input type="submit" value="%"></th>
						</tr>
					</thead>
					<tbody>';
		if (!$dbExecuteSelect = pg_query_params(
			$dbConnection, "
				SELECT 		* 
				FROM 		" . $_GET['table'] . " 
				WHERE 		usesysid = $1
			", 
			array($_GET['id']))) {
			throw new Exception(DB_ERROR_QUERY);
		}
		else {
			$aFetchSelect = pg_fetch_assoc($dbExecuteSelect);
			$i = 0;
			foreach ($aFetchSelect as $sField => $sValue) {
				$this->sData .= '
						<tr>
							<td>' . $sField . '<br><em>'.pg_field_type($dbExecuteSelect, $i).' ('.pg_field_size($dbExecuteSelect, $i). ')</em></td>';

				//if ((pg_field_type($dbExecuteSelect, $i) == 'varchar') && (pg_field_size($dbExecuteSelect, $i) < 0)) {
				if ((!in_array($_GET['table'], $this->__DB)) && (!in_array($sField, $this->__DB[$_GET['table']]))) {
					switch ($this->__DB[$_GET['table']]['FIELDS'][$sField]) {
						case 'editor':
							$sFieldHtml = '
							<td class="input"><textarea name=' . $sField . '>' . $sValue .'</textarea></td>';
							break;

						case 'text':
							$sFieldHtml = '
							<td class="input"><input type="text" name="' . $sField . '" value="' . $sValue .'"></td>';
							break;

						case 'numeric':
							$sFieldHtml = '
							<td class="input"><input type="numeric" name="' . $sField . '" value="' . $sValue .'"></td>';
							break;

						case 'password':
							$sFieldHtml = '
							<td class="input"><input type="password" name="' . $sField . '" value="' . $sValue .'"></td>';
							break;

						case 'bool':
							$sFieldHtml = '
							<td>
								<input type="radio" name="' . $sField . '" value="t" ' . ($sValue == 't'?'checked="checked"':'') . '>True
								<input type="radio" name="' . $sField . '" value="f" ' . ($sValue == 'f'?'checked="checked"':'') . '>False
							</td>';
							break;

						default:
							$sFieldHtml = '
							<td>' . $sValue . '</td>';
							break;
					}
				}
				else {
					$sFieldHtml = '
							<td>' . $sValue . '</td>';
				}
				$this->sData .= $sFieldHtml . '
							<td>&nbsp;</td>
						</tr>';
					$i++;
			}
		}
		$this->sData .= '
					</tbody>
					<thead>
						<tr>
							<th>' . CMS_TABLE_FIELDNAME . '</th>
							<th>' . CMS_TABLE_FIELDCONTENT . '</th>
							<th><input type="submit" value="%"></th>
						</tr>
					</thead>
				</table>
				
			</form>
		</section>
		</div>';
	}
}