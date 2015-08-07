<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'news_calendar';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['templateLayouts']['myext'] = array('calendar', '99');