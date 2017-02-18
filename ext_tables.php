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
	$tempColumns = [];
	$tempColumns[$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type']] = [
		'exclude' => 1,
		'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews.tx_extbase_type',
		'config' => [
			'type' => 'select',
			'items' => [],
			'size' => 1,
			'maxitems' => 1,
		]
	];
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $tempColumns, 1);
}

$tmp_dated_news_columns = [
	'application' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.application',
		'config' => [
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_application',
			'MM' => 'tx_datednews_news_application_mm',
			'size' => 5,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => [
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => [
					'type' => 'popup',
					'title' => 'LLL:EXT:lang/locallang_tca.xlf:be_users.usergroup_edit_title',
					'module' => [
						'name' => 'wizard_edit',
					],
					'popup_onlyOpenIfSelected' => 1,
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1'
				],
				'add' => [
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => [
						'table' => 'tx_datednews_domain_model_application',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					],
					'module' => [
						'name' => 'wizard_add'
					],
				],
			],
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'showincalendar' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.showincalendar',
		'config' => [
			'type' => 'check',
			'default' => 0
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'enable_application' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.enable_application',
		'config' => [
			'type' => 'check',
			'default' => 0
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'fulltime' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.fulltime',
		'config' => [
			'type' => 'check',
			'default' => 0
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'eventstart' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventstart',
		'config' => [
			'type' => 'input',
			'size' => 16,
			'max' => 20,
			'eval' => 'datetime',
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'eventend' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventend',
		'config' => [
			'type' => 'input',
			'size' => 16,
			'max' => 20,
			'eval' => 'datetime',
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'eventlocation' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventlocation',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'eventtype' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.eventtype',
		'config' => [
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
		],
	],
	'textcolor' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.textcolor',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'backgroundcolor' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.backgroundcolor',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'targetgroup' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.targetgroup',
		'config' => [
			'type' => 'text',
			'cols' => 60,
			'rows' => 5
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'slots' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.slots',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'price' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.price',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'early_bird_price' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.early_bird_price',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'locations' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.locations',
		'config' => [
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_location',
			'MM' => 'tx_datednews_news_location_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => [
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => [
					'module' => [
						'name' => 'wizard_edit',
					],
					'type' => 'popup',
					'title' => 'Edit',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				],
				'add' => [
					'module' => [
						'name' => 'wizard_add',
					],
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => [
						'table' => 'tx_datednews_domain_model_location',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					],
				],
			],
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
	'persons' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.persons',
		'config' => [
			'type' => 'select',
			'renderType' => 'selectMultipleSideBySide',
			'foreign_table' => 'tx_datednews_domain_model_person',
			'MM' => 'tx_datednews_news_person_mm',
			'size' => 10,
			'autoSizeMax' => 30,
			'maxitems' => 9999,
			'multiple' => 0,
			'wizards' => [
				'_PADDING' => 1,
				'_VERTICAL' => 1,
				'edit' => [
					'module' => [
						'name' => 'wizard_edit',
					],
					'type' => 'popup',
					'title' => 'Edit',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
					'popup_onlyOpenIfSelected' => 1,
					'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
				],
				'add' => [
					'module' => [
						'name' => 'wizard_add',
					],
					'type' => 'script',
					'title' => 'Create new',
					'icon' => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
					'params' => [
						'table' => 'tx_datednews_domain_model_person',
						'pid' => '###CURRENT_PID###',
						'setValue' => 'prepend'
					],
				],
			],
		],
		'displayCond' => [
			'AND' => [
				'FIELD:eventtype:REQ:TRUE',
			],
		],
	],
];

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
	price;;;;1-1-1, 
	early_bird_price;;;;1-1-1,
	targetgroup;;;;1-1-1,
	locations;;;;1-1-1, 
	persons;;;;1-1-1, 
	textcolor;;;;1-1-1, 
	backgroundcolor;;;;1-1-1, 
	application;;;;1-1-1"
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes("tx_news_domain_model_news", 'eventtype;;;;1-1-1', '', 'after:istopnews');

$GLOBALS['TCA']['tx_news_domain_model_news']['columns'][$TCA['tx_news_domain_model_news']['ctrl']['type']]['config']['items'][] = ['LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.tx_extbase_type.Tx_DatedNews_News','Tx_DatedNews_News'];
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

$TCA['tx_news_domain_model_news']['ctrl']['requestUpdate'] = 'eventtype';