<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Gabriel Neumann <gabe@gmx.eu>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

require_once(t3lib_extMgm::extPath('m3lib') . 'pibase/class.tx_m3openlayers_pibase.php');


/**
 * Plugin 'Meridian3 map for tt_content' for the 'm3content' extension.
 *
 * @author	Gabriel Neumann <gabe@gmx.eu>
 */
class tx_m3content_pi1 extends tx_m3openlayers_pibase {
	
	public $prefixId		= 'tx_m3content_pi1';				// Same as class name
	public $scriptRelPath	= 'pi1/class.tx_m3content_pi1.php';	// Path to this script relative to the extension dir.
	public $extKey			= 'm3content';						// The extension key.
	
	
	/**
	 * Gives the content records with their geometry and the rendered summary for
	 * the map popups.
	 * 
	 * @return array
	 */
	public function getGeomRecords() {
		
		global $TYPO3_DB;
		
		$ret = array();
		
		$storage = $this->cObj->data['pid'];
		if ($this->cObj->data['pages'] != '') {
			$storage = $this->cObj->data['pages'];
			if (intval($this->cObj->data['recursive']) != 0) {
				$storage = $this->pi_getPidList($storage, intval($this->cObj->data['recursive']));
			}
		}
		
		$records = $TYPO3_DB->exec_SELECTgetRows(
				'*',
				'tt_content',
				'pid IN (' . $storage . ')'
				. " AND tx_m3geom<>''"
				. $this->cObj->enableFields('tt_content')
			);
		
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		foreach ($records as $record) {
			// modify attributes to customize output for map popups
			if ($record['imagewidth'] > 0) {
				$record['imagewidth'] = max(150, $record['imagewidth']);
			}
			if ($record['imageheight'] > 0) {
				$record['imageheight'] = max(150, $record['imageheight']);
			}
			$record['linkToTop'] = 0;
			$record['header_link'] = $record['pid'] . '#' . $record['uid'];
			if (in_array($record['CType'], array('text', 'textpic', 'html'))) {
				$record['bodytext'] = $this->cObj->cropHTML(
						$this->cObj->cropHTML($record['bodytext'], '500| &nbsp;<i>[...]</i>'),
						'1000|'
					);
			}
			
			$tmpConf = array(
					"stdWrap." => array(
						"innerWrap." => array(
								"cObject." => array(
										"default." => array(
												"10." => array(
														"value" => '<div id="map' . $this->cObj->data['uid'] . '_c{field:uid}"'
													)
											)
									)
							)
					)
				);
			
			// render output with the custom content object
			$orig = $GLOBALS['TSFE']->currentRecord;
			$GLOBALS['TSFE']->currentRecord = 'tt_content:' . $record['uid'];
			$cObj->start($record, 'tt_content');
			$html = $cObj->cObjGetSingle('<tt_content', $tmpConf, '');
			$GLOBALS['TSFE']->currentRecord = $orig;
			
			if ($record['header'] == '') {
				$html .= '<div style="text-align: center;">' . $this->cObj->getTypoLink(
						$this->pi_getLL('link.detail'), $record['pid'] . '#' . $record['uid']
					) . '</div>';
			}
			
			$ret[] = array(
					'html' => '<div' . $this->pi_classParam('popup') . '>' . $html . '</div>',
					'geom' => $record['tx_m3geom']
				);
		}
		
		return $ret;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/m3content/pi1/class.tx_m3content_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/m3content/pi1/class.tx_m3content_pi1.php']);
}

?>
