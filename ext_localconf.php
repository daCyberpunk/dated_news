<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'dated_news';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Controller/NewsController'][] = 'dated_news';

//$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['templateLayouts']['dated_news'] = array('calendar', '99');
//$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['templateLayouts']['dated_news'] = array('Eventlist for Reasearch and teaching page', '98');
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['templateLayouts']['dated_news_tag_filter'] = array('Taglist for filtering Calendarview', '99999');


//neue Action Anlegen in Flexforms
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->calendar'] = 'DatedNews Calendar';

//Bei der neuen Action werden manche Flexformfelder nicht mehr benötigt
//$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Hooks/BackendUtility.php']['updateFlexforms']['dated_news']
//	= 'FalkRoeder\\DatedNews\\Hooks\\BackendUtility->updateFlexformsDatedNews';


