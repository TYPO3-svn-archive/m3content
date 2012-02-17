<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// add geometry field for the common content record types
$tempColumns = Array (
	"tx_m3geom" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:m3content/locallang_db.xml:tt_content.tx_m3geom",		
		"config" => Array (
			"type" => "text",
			"cols" => "48",	
			"rows" => "5",
			"wizards" => Array(
				"_PADDING" => 8,
				"edit_geom" => Array(
					"notNewRecords" => false,
					"type" => "userFunc",
					"userFunc" => "EXT:m3content/class.tx_m3content.php:"
									. "tx_m3content->wizard",
					"params" => array(
							'multiple' => true,
							'geometryTypes' => array('point', 'polyline', 'polygon')
						)
				),
			),
		)
	),
);

t3lib_div::loadTCA('tt_content');
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes(
		"tt_content",
		"--div--;LLL:EXT:m3content/locallang_db.xml:tt_content.tx_m3geom_div, "
			. "tx_m3geom;;;;1-1-1, "
			. "--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access",
		'header,text,textpic,image,bullets,table,uploads,multimedia,media,html',
		'before:starttime'
	);

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

// use flexforms
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:m3lib/pibase/flexform.xml');

// add to plugin selection list
t3lib_extMgm::addPlugin(Array('LLL:EXT:m3content/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'), 'list_type');


if (TYPO3_MODE=="BE") {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_m3content_pi1_wizicon"]
		= t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_m3content_pi1_wizicon.php';
}

?>