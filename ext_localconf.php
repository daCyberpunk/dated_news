<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'dated_news';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/Category'][] = 'dated_news';

//Flexform
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Hooks/BackendUtility.php']['updateFlexforms']['dated_news']
	= 'FalkRoeder\\DatedNews\\Hooks\\BackendUtility->updateFlexformsDatedNews';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][]
	= \FalkRoeder\DatedNews\Hooks\FlexFormHook::class;

//register TypeConverter for mapping also hidden applications in controller actions
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('FalkRoeder\\DatedNews\\Property\\TypeConverters\\ApplicationPersistentObjectConverter');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'GeorgRinger.news',
	'Pi1',
	[
		'News' => 'list,detail,dateMenu,searchForm,searchResult,createApplication,confirmApplication,freeslots',
		'Category' => 'list',
		'Tag' => 'list',
	],
	[
		'News' => 'searchForm,searchResult,createApplication,confirmApplication',
	]
);
