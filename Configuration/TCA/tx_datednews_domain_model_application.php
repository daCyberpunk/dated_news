<?php

defined('TYPO3_MODE') or die();

return [
    'ctrl' => [
        'title'                    => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application',
        'label'                    => 'application_title',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
        'dividers2tabs'            => true,
        'sortby'                   => 'sorting',
        'versioningWS'             => 2,
        'versioning_followPages'   => true,
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled' => 'hidden',

        ],
        'searchFields' => 'name,surname,email,events,',
        'iconfile'     => 'EXT:dated_news/Resources/Public/Icons/tx_datednews_domain_model_application.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, name, surname, email, events',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden,--palette--;;1, application_title, title, name, surname, email, reserved_slots, events, company, address, address2, zip, city, country, costs, phone, mobile,taxid,message,terms_accept, confirmed, '],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'crdate' => [
            'exclude' => 1,
            'label'   => 'Creation date',
            'config'  => [
                'type'   => 'none',
                'format' => 'date',
                'eval'   => 'date',
            ],
        ],
        'sys_language_uid' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'                => 'select',
                'renderType'          => 'selectSingle',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items'               => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0],
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_datednews_domain_model_application',
                'foreign_table_where' => 'AND tx_datednews_domain_model_application.pid=###CURRENT_PID### AND tx_datednews_domain_model_application.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max'  => 255,
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type' => 'check',
            ],
        ],

        'name' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.name',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.title',
            'config'  => [
                'type' => 'input',
                'size' => 100,
                'eval' => 'trim,required',
            ],
        ],
        'application_title' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.applicationTitle',
            'config'  => [
                'type' => 'input',
                'size' => 100,
                'eval' => 'trim,required',
            ],
        ],
        'surname' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.surname',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.email',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required',
            ],
        ],
        'reserved_slots' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.reserved_slots',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'form_timestamp' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.form_timestamp',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'events' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.events',
            'config'  => [
                'type'              => 'inline',
                'foreign_table'     => 'tx_news_domain_model_news',
                'MM'                => 'tx_datednews_news_application_mm',
                'MM_opposite_field' => 'application',
                'maxitems'          => 9999,
                'appearance'        => [
                    'collapseAll'                     => 1,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'useSortable'                     => 1,
                    'showAllLocalizationLink'         => 1,
                ],
            ],

        ],
        'recurringevents' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.events',
            'config'  => [
                'type'              => 'inline',
                'foreign_table'     => 'tx_datednews_domain_model_newsrecurrence',
                'MM'                => 'tx_datednews_newsrecurrence_application_mm',
                'MM_opposite_field' => 'application',
                'maxitems'          => 9999,
                'appearance'        => [
                    'collapseAll'                     => 1,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'useSortable'                     => 1,
                    'showAllLocalizationLink'         => 1,
                ],
            ],
        ],
        'company' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.company',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'address' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.address',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'address2' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.address2',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.zip',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.city',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'country' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.country',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'costs' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.costs',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.phone',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'mobile' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.mobile',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'taxid' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.taxid',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'message' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.message',
            'config'  => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 30,
                'eval' => 'trim',
            ],
        ],
        'terms_accept' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.terms_accept',
            'config'  => [
                'type'    => 'check',
                'default' => '0',
            ],
        ],
        'confirmed' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_application.confirmed',
            'config'  => [
                'type'    => 'check',
                'default' => '0',
            ],
        ],

    ],
]; //# EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder
