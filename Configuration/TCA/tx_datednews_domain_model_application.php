<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'sortby' => 'sorting',
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',

		),
		'searchFields' => 'name,surname,email,events,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('dated_news') . 'Resources/Public/Icons/tx_datednews_domain_model_application.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, name, surname, email, events',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, title, name, surname, email, reserved_slots, events, company, address, address2, zip, city, country, costs, phone, mobile,taxid,message,terms_accept, '),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(
	
		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_datednews_domain_model_application',
				'foreign_table_where' => 'AND tx_datednews_domain_model_application.pid=###CURRENT_PID### AND tx_datednews_domain_model_application.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),
	
		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),

		'name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'title' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.title',
			'config' => array(
				'type' => 'input',
				'size' => 100,
				'eval' => 'trim,required'
			),
		),
		'surname' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.surname',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'email' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.email',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim,required'
			),
		),
		'reserved_slots' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.reserved_slots',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'form_timestamp' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.form_timestamp',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'events' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.events',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_news_domain_model_news',
				'MM' => 'tx_datednews_news_application_mm',
				'MM_opposite_field' => 'application',
				'maxitems' => 9999,
				'appearance' => array(
					'collapseAll' => 1,
					'levelLinksPosition' => 'top',
					'showSynchronizationLink' => 1,
					'showPossibleLocalizationRecords' => 1,
					'useSortable' => 1,
					'showAllLocalizationLink' => 1
				),
			),

		),
		'company' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.company',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'address' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.address',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'address2' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.address2',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'zip' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.zip',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'city' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.city',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'country' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.country',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'costs' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.costs',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'phone' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.phone',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'mobile' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.mobile',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'taxid' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.taxid',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'message' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.message',
			'config' => array(
				'type' => 'text',
				'cols' => 60,
				'rows' => 30,
				'eval' => 'trim'
			),
		),
		'terms_accept' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.terms_accept',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			),
		),
		
	),
);## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder