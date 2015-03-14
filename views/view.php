<?php //	namespace Views;

/**
 *	class V_View
 *	Load the main View, other Views can extend this class
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_View extends V_Main {

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aDataTables
	 *	@return void
	 */
	public function setContent($aData = array()) {

		foreach($aData['tables'] as $aValueTables) {
			if (isset($this->__DB[$aValueTables['tablename']]) && $aValueTables['tablename'] == strtolower($this->oClean->getParts(1))) {
				$this->sData .= '
		<section id="' . $aValueTables['tablename'] . '">
			<h2 style="font-variant:small-caps;">' . ucwords(str_replace('_', ' ', $aValueTables['tablename'])) . '</h2>
			<table>
				<thead>
					<tr>';

				foreach($aData['columns'][$aValueTables['tablename']] as $aValueColumns) {
					if (!in_array($aValueColumns['attname'], $this->__CFG['DISABLED'])) {
						$this->sData .= '
						<th>' . $aValueColumns['attname'] . '</th>';
					}
				}
				$this->sData .= '
						<th class="edit">';

				if (!isset($this->__DB[$aValueTables['tablename']]['ADD_FIELDS']) || $this->__DB[$aValueTables['tablename']]['ADD_FIELDS'] != 'disabled') {
					$this->sData .= '
							<a href="/edit/' . $aValueTables['tablename'] . '/add/" class="del_icon">+</a>';
				}
				$this->sData .= '	
						</th>
						<th class="delete"><sup>' . $aData['numcolumns'][$aValueTables['tablename']] . '</sup>/<sub>' . $aData['numrows'][$aValueTables['tablename']] . '</sub></th>
					</tr>
				</thead>
				<tbody>';

				if ($aData['numrows'][$aValueTables['tablename']] != 0) {
					foreach($aData['rows'][$aValueTables['tablename']] as $aValueRows) {
						$this->sData .= '
					<tr>';

						foreach($aData['columns'][$aValueTables['tablename']] as $aValueColumns) {
							if (!in_array($aValueColumns['attname'], $this->__CFG['DISABLED'])) {
								if ($this->__DB[$aValueTables['tablename']]['FIELDS'][$aValueColumns['attname']] == 'datetime') {
									$this->sData .= '
						<td>' . date(DT_FORMAT_PHP_DATETIME, strtotime($aValueRows[$aValueColumns['attname']])) . '</td>';
								}
								elseif ($this->__DB[$aValueTables['tablename']]['FIELDS'][$aValueColumns['attname']] == 'editor') {
									$this->sData .= '
						<td><iframe class="content" srcdoc="' . $aValueRows[$aValueColumns['attname']] . '" seamless></iframe></td>';
								}
								else {
									$this->sData .= '
						<td>' . $aValueRows[$aValueColumns['attname']] . '</td>';
								}
							}
						}

						$this->sData .= '
						<td><a href="/edit/' . $aValueTables['tablename'] . '/' . $aValueRows['id'] . '/edit/" class="edit_icon">V</a></td>
						<td><a href="/edit/' . $aValueTables['tablename'] . '/' . $aValueRows['id'] . '/delete/" class="del_icon">I</a></td>
					</tr>';
					}
				}
				else {
					$this->sData .= '
					<tr>
						<td colspan="' . $aData['numcolumns'][$aValueTables['tablename']] . '">' . CMS_NOROWS . '</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>';
				}
				$this->sData .= '
				</tbody>
				<tfoot>
					<tr>';
							
				foreach($aData['columns'][$aValueTables['tablename']] as $aValueColumns) {
					if (!in_array($aValueColumns['attname'], $this->__CFG['DISABLED'])) {
						$this->sData .= '
						<th>' . $aValueColumns['attname'] . '</th>';
					}
				}
				$this->sData .= '
						<th class="edit">';

				if (!isset($this->__DB[$aValueTables['tablename']]['ADD_FIELDS']) || $this->__DB[$aValueTables['tablename']]['ADD_FIELDS'] != 'disabled') {
					$this->sData .= '
							<a href="/edit/' . $aValueTables['tablename'] . '/add/" class="del_icon">+</a>';
				}
				$this->sData .= '
						</th>
						<th class="delete"><sup>' . $aData['numcolumns'][$aValueTables['tablename']] . '</sup>/<sub>' . $aData['numrows'][$aValueTables['tablename']] . '</sub></th>
					</tr>
				</tfoot>
			</table>
		</section>';
			}
		}
	}
}