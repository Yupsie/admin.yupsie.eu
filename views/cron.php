<?php //	namespace Views;

/**
 *	class V_Cron
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class V_Cron extends V_Main {

	/**
	 *	function setContent
	 *	Put the content in the view
	 *
	 *	@access public
	 *	@param array aData
	 *	@return void
	 */
	public function setContent($aData = array()) {

		$this->sData = '
		<section>
			<article>
				<h2>' . CMS_CRON . '</h2>
				<table class="cron">';


		foreach ($aData['cron'] as $sTime => $aContentSub) {
			$sTime = substr($sTime, 0, 11);
			if (!isset($sTimePrev) || $sTimePrev != $sTime) {
				$this->sData .= '
					<tr>
						<th colspan="' . (count($aContentSub) - 1) . '">' . $sTime . '</th>
					</tr>';
			}
			$this->sData .= '
					<tr>';

			$aContentSub = array_reverse($aContentSub);
			foreach ($aContentSub as $sColumnName => $sColumn) {
				if ($sColumnName == 'time') {
					$sColumn = '&bull; ' . substr($sColumn, 11, 6);
				}

				if ($sColumnName == 'color') continue;

				$sColumn = str_replace('gelukt', '', $sColumn);
				
				$this->sData .= '
						<td>' . $sColumn . '</td>';

			}
			$this->sData .= '
					</tr>';
			$sTimePrev = $sTime;
		}
		$this->sData .= '
				</table>
			</article>
		</section>
		<section>
			<article>
				<h2>' . CMS_CRON . '</h2>
				<table class="cron">';


		foreach ($aData['errors'] as $sTime => $sContent) {
			$this->sData .= str_replace(
				array(
					'#@####', 
					'###',
					'##-Stack-trace-##',
					'#-Additional-info-#'
				), 
				array(
					'<tr><th colspan="2">', 
					'</th></tr><tr><td colspan="2">',
					'</td></tr><tr><td style="background-color:#e8e8ff;width:50%;">',
					'</td><td style="background-color:#eaeaff;width:50%;">'
				), 
				$sContent . '</td></tr>'
			);
		}
		$this->sData .= '
				</table>
			</article>
		</section>';
	}
}