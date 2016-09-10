<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'datednews');

if (!isset($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumns = array();
	$tempColumns[$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'items' => array(),
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $tempColumns, 1);
}

$tmp_dated_news_columns = array(
	'showincalendar' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.showincalendar',
		'config' => array(
			'type' => 'check',
			'default' => 0
		)
	),
	'fulltime' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.fulltime',
		'config' => array(
			'type' => 'check',
			'default' => 0
		)
	),
	'eventstart' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventstart',
		'config' => array(
			'type' => 'input',
			'size' => 16,
			'max' => 20,
			'eval' => 'datetime',
		),
	),
	'eventend' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventend',
		'config' => array(
			'type' => 'input',
			'size' => 16,
			'max' => 20,
			'eval' => 'datetime',
		),
	),
	'eventlocation' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventlocation',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'eventtype' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventtype',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => [
				['Event', 'Event'],
				['Publication Event', 'PublicationEvent'],
				['Exhibition', 'ExhibitionEvent'],
				['Visual Arts Event', 'VisualArtsEvent'],
			],
			'size' => 1,
			'maxitems' => 1,
		),
	),
	'textcolor' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.textcolor',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	
	'backgroundcolor' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.backgroundcolor',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',$tmp_dated_news_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	"tx_news_domain_model_news",
	",--div--;LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news, showincalendar;;;;1-1-1, eventstart;;;;1-1-1, eventend;;;;1-1-1, fulltime;;;;1-1-1, eventlocation;;;;1-1-1, eventtype;;;;1-1-1, textcolor;;;;1-1-1, backgroundcolor;;;;1-1-1"
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	"tx_news_domain_model_news",
	",--div--;LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.reasearch"
);
/*\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette(
	'tx_news_domain_model_news',
	'paletteCore',
	'thumbnailposition',
	'after:istopnews'
);*/


$GLOBALS['TCA']['tx_news_domain_model_news']['columns'][$TCA['tx_news_domain_model_news']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.tx_extbase_type.Tx_DatedNews_News','Tx_DatedNews_News');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', $GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'],'','after:' . $TCA['tx_news_domain_model_news']['ctrl']['label']);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $_EXTKEY,
    'tx_news_domain_model_news'
);

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByNews'] .= ',eventstart';