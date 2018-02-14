<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/News'][] = 'dated_news';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['classes']['Domain/Model/Category'][] = 'dated_news';

//Flexform to remove some fields using hook provided by news
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['Hooks/BackendUtility.php']['updateFlexforms']['dated_news']
    = 'FalkRoeder\\DatedNews\\Hooks\\BackendUtility->updateFlexformsDatedNews';

// Modify Flexform Values. removed since TYPO3 V8.5
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass'][]
    = \FalkRoeder\DatedNews\Hooks\FlexFormHook::class;

if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8005000) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][\TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools::class]['flexParsing'][]
        = \FalkRoeder\DatedNews\Hooks\FlexFormHook::class;
}
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['dated_news'] = 'FalkRoeder\\DatedNews\\Hooks\\TCEmainHook';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['dated_news'] = 'FalkRoeder\\DatedNews\\Hooks\\TCEmainHook';

//register TypeConverter for mapping also hidden applications in controller actions
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter('FalkRoeder\\DatedNews\\Property\\TypeConverters\\ApplicationPersistentObjectConverter');

//CLearCacheHook
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'FalkRoeder\\DatedNews\\Hooks\\ClearCacheHook->clearJsCache';

//PageLayoutHook of tx_news
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news'][\GeorgRinger\News\Hooks\PageLayoutView::class]['extensionSummary']['dated_news']
    = \FalkRoeder\DatedNews\Hooks\PageLayoutView::class . '->extensionSummary';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'GeorgRinger.news',
    'Pi1',
    [
        'News'     => 'list,detail,dateMenu,searchForm,searchResult,createApplication,confirmApplication,freeslots,timestamp,ajaxEvent',
        'Category' => 'list',
        'Tag'      => 'list',
    ],
    [
        'News' => 'searchForm,searchResult,createApplication,confirmApplication',
    ]
);
