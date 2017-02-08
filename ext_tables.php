<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Dated News');

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
	'application' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.application',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_application',
			'MM' => 'tx_datednews_news_application_mm',
			'size' => 5,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'type' => 'popup',
					'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_edit_title',
					'module' => array(
						'name' => 'wizard_edit',
					),
					'popup_onlyOpenIfSelected' => 1,
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1'
				),
				'add' => Array(
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => array(
						'table' => 'tx_datednews_domain_model_application',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					),
					'module' => array(
						'name' => 'wizard_add'
					),
				),
			),
		),
	),
	'showincalendar' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.showincalendar',
		'config' => array(
			'type' => 'check',
			'default' => 0
		)
	),
	'enable_application' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.enable_application',
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
				['is not an event', ''],
				['Event', 'Event'],
				['Publication Event', 'PublicationEvent'],
				['Exhibition', 'ExhibitionEvent'],
				['Visual Arts Event', 'VisualArtsEvent'],
				['Business Event', 'BusinessEvent'],
				['Education Event', 'EducationEvent'],
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
	'targetgroup' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.targetgroup',
		'config' => array(
			'type' => 'text',
			'cols' => 60,
			'rows' => 5
		),
	),
	'slots' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.slots',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'price' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.price',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'early_bird_price' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.early_bird_price',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'locations' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.locations',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_location',
			'MM' => 'tx_datednews_news_location_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'module' => array(
						'name' => 'wizard_edit',
					),
					'type' => 'popup',
					'title' => 'Edit',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
				'add' => Array(
					'module' => array(
						'name' => 'wizard_add',
					),
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => array(
						'table' => 'tx_datednews_domain_model_location',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					),
				),
			),
		),
	),
	'persons' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.persons',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_person',
			'MM' => 'tx_datednews_news_person_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => array(
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => array(
					'module' => array(
						'name' => 'wizard_edit',
					),
					'type' => 'popup',
					'title' => 'Edit',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				),
				'add' => Array(
					'module' => array(
						'name' => 'wizard_add',
					),
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => array(
						'table' => 'tx_datednews_domain_model_person',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					),
				),
			),
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',$tmp_dated_news_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	"tx_news_domain_model_news",
	",--div--;LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news, 
	showincalendar;;;;1-1-1, 
	enable_application;;;;1-1-1, 
	eventstart;;;;1-1-1, 
	eventend;;;;1-1-1, 
	fulltime;;;;1-1-1, 
	slots;;;;1-1-1, 
	eventtype;;;;1-1-1, 
	price;;;;1-1-1, 
	early_bird_price;;;;1-1-1,
	targetgroup;;;;1-1-1,
	locations;;;;1-1-1, 
	persons;;;;1-1-1, 
	textcolor;;;;1-1-1, 
	backgroundcolor;;;;1-1-1, 
	application;;;;1-1-1"
);

$GLOBALS['TCA']['tx_news_domain_model_news']['columns'][$TCA['tx_news_domain_model_news']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.tx_extbase_type.Tx_DatedNews_News','Tx_DatedNews_News');
//\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', $GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'],'','after:' . $TCA['tx_news_domain_model_news']['ctrl']['label']);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    $_EXTKEY,
    'tx_news_domain_model_news'
);

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByNews'] .= ',eventstart';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_datednews_domain_model_application', 'EXT:dated_news/Resources/Private/Language/locallang_csh_tx_datednews_domain_model_application.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_datednews_domain_model_application');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_datednews_domain_model_location', 'EXT:dated_news/Resources/Private/Language/locallang_csh_tx_datednews_domain_model_location.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_datednews_domain_model_location');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_datednews_domain_model_person', 'EXT:dated_news/Resources/Private/Language/locallang_csh_tx_datednews_domain_model_person.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_datednews_domain_model_person');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tt_content.pi_flexform.news_pi1.list',
	'EXT:dated_news/Resources/Private/Language/locallang_csh_flexform_dated_news.xlf'
);