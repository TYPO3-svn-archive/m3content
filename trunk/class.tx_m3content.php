<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Gabriel Neumann (gabe@gmx.eu)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This class provides a wizard to edit wkt geometries for tt_content records
 *
 * @author	Gabriel Neumann <gabe@gmx.eu>
 */
class tx_m3content {
	
	/**
	 * extension key
	 * @var string
	 */
	private $extKey = 'm3content';
	
	
	/**
	 * Make the wizard button to edit WKT geometries
	 * @param array $field
	 * @param t3lib_TCEforms $form
	 * @return string
	 */
	public function wizard(array $field, t3lib_TCEforms $form) {
		
		// decode config and translate parameters
		$cfg = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$this->extKey]);
		if (is_array($cfg)) {
			$field['params']['projection'] = $cfg['projection'];
			$field['params']['multiple'] = $cfg['multiple'] != false;
			$field['params']['geometryTypes'] = array();
			if ($cfg['enablePoint']) {
				$field['params']['geometryTypes'][] = 'point';
			}
			if ($cfg['enablePolyline']) {
				$field['params']['geometryTypes'][] = 'polyline';
			}
			if ($cfg['enablePolygon']) {
				$field['params']['geometryTypes'][] = 'polygon';
			}
		}
		
		// generate wizard button
		include_once(t3lib_extMgm::extPath('m3lib') . 'class.tx_m3lib.php');
		if (is_array($cfg) && $cfg['method'] == 'marker') {
			return t3lib_div::makeInstance('tx_m3lib')->editMarker($field, $form);
		}
		else {
			return t3lib_div::makeInstance('tx_m3lib')->editGeometry($field, $form);
		}
	}
}


// XClass integration
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/m3content/class.tx_m3content.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/m3content/class.tx_m3content.php']);
}

?>
