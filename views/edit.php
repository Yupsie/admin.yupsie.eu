<?php //	namespace Views;

/**
 *	class V_Edit
 *	Load the main View, other Views can extend this class
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_Edit extends V_Main {

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {
		$this->sData = '
		<section class="edit">
			<h2 style="font-variant:small-caps;">' . ucwords(str_replace('_', ' ', $aData['output']['table'])) . '</h2>
			<form method="post">
				<input type="hidden" name="table" value="' . $aData['output']['table'] . '">
				<input type="hidden" name="id" value="' . $aData['output']['id'] . '">
				<!--<input type="hidden" name="action" value="' . $aData['output']['action'] . '">-->
				<table>
					<thead>
						<tr>
							<th>' . CMS_TABLE_FIELDNAME . '</th>
							<th>' . CMS_TABLE_FIELDCONTENT . '</th>
							<th><input type="submit" value="%"></th>
						</tr>
					</thead>
					<tbody>';

		$i = 0;
		foreach($aData['rows'][0] as $sField => $sValue) {
			$this->sData .= '
						<tr>
							<td>' . $sField . '<br><em>' . $aData['columntype'][$i] . ' (' . $aData['columnsize'][$i] . ')</em></td>';

			if (!in_array(strtolower($aData['output']['table']), $this->__DB) && !in_array($sField, $this->__DB[strtolower($aData['output']['table'])])) {
				switch ($this->__DB[strtolower($aData['output']['table'])]['FIELDS'][$sField]) {
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

					case 'boolean':
						$sFieldHtml = '
							<td>
								<input type="radio" name="' . $sField . '" value="t" ' . ($sValue == 't'?' checked="checked"':'') .'> True
								<input type="radio" name="' . $sField . '" value="f" ' . ($sValue == 'f'?' checked="checked"':'') .'> False
							</td>';
						break;

					case 'file':
						$sValueNew = (isset($aData['output']['file'])?$aData['output']['file']:$sValue);
						$sFieldHtml = '
							<td class="input">
								<input type="hidden" name="' . $sField . '" value="' . $sValueNew . '">
								<a href="/files/' . $aData['output']['table'] . '/' . $aData['output']['id'] . '/' . $aData['output']['action'] . '/">' . CMS_FILE_MANAGER . '</a> | 
								' . CMS_FILE_SELECTED . ': ' . $sValueNew . ' ';

						if (in_array(mime_content_type('../uploads/' . $sValueNew), array('image/png', 'image/gif', 'image/jpeg', 'image/jpg'))) {
							$sFieldHtml .= '
								<img src="../uploads/' . $sValueNew . '" alt="' . $sValueNew . '" style="height:40px;">';
						}
						$sFieldHtml .= '
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

		$this->sData .= '
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
		</section>

		<div class="dialog">
			<h2 style="font-variant:small-caps;">' . ucwords(str_replace('_', ' ', CMS_UPLOADED_FILES)) . '<span></span></h2>
			<iframe src="/filemanager/"></iframe>
		</div>';
	}
}