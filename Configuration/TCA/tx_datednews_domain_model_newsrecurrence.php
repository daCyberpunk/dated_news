<?php

return [
    'ctrl' => [
        'title'                    => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence',
        'label'                    => 'eventstart',
        'tstamp'                   => 'tstamp',
        'crdate'                   => 'crdate',
        'cruser_id'                => 'cruser_id',
//        'sortby'                   => 'eventstart',
        'default_sortby'           => 'eventstart',
        'versioningWS'             => true,
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete'                   => 'deleted',
        'enablecolumns'            => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'eventstart,eventend,eventlocation,bodytext,teaser,parent_event',
        'iconfile'     => 'EXT:dated_news/Resources/Public/Icons/tx_datednews_domain_model_newsrecurrence.svg',
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, eventstart, eventend, bodytext, teaser, modified, parent_event, application,slots,early_bird_date,locations,persons,enable_application,showincalendar,disregard_changes_on_saving',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, modified, enable_application, showincalendar, eventstart, eventend, early_bird_date, slots, locations,persons, bodytext, teaser, parent_event, application,disregard_changes_on_saving'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config'  => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'special'    => 'languages',
                'items'      => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => true,
            'label'       => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config'      => [
                'type'       => 'select',
                'renderType' => 'selectSingle',
                'items'      => [
                    ['', 0],
                ],
                'foreign_table'       => 'tx_datednews_domain_model_newsrecurrence',
                'foreign_table_where' => 'AND tx_datednews_domain_model_newsrecurrence.pid=###CURRENT_PID### AND tx_datednews_domain_model_newsrecurrence.sys_language_uid IN (-1,0)',
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
            'exclude' => true,
            'label'   => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config'  => [
                'type'  => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],

        'eventstart' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventstart',
            'config'  => [
                'type' => 'input',
                'size' => 16,
                'max'  => 20,
                'eval' => 'datetime',
            ],
        ],
        'eventend' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventend',
            'config'  => [
                'type' => 'input',
                'size' => 16,
                'max'  => 20,
                'eval' => 'datetime',
            ],
        ],
        'eventlocation' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventlocation',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'bodytext' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.bodytext',
            'config'  => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
        ],
        'teaser' => [
            'exclude' => false,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.teaser',
            'config'  => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
        ],
        'modified' => [
            'label'  => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.modified',
            'config' => [
                'type'  => 'check',
                'items' => [
                    ['lock record', 0],
                ],
                'cols'    => 'inline',
                'default' => 0,
            ],
        ],
        'parent_event' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.parent_event',
            'config'  => [
                'type'              => 'inline',
                'foreign_table'     => 'tx_news_domain_model_news',
                'MM'                => 'tx_datednews_news_newsrecurrence_mm',
                'MM_opposite_field' => 'newsrecurrence',
                'maxitems'          => 9999,
                'appearance'        => [
                    'collapseAll'                     => 1,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink'         => 1,
                ],
            ],
        ],
        'application' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.application',
            'config'  => [
                'type'              => 'inline',
                'foreign_table'     => 'tx_datednews_domain_model_application',
                'MM'                => 'tx_datednews_newsrecurrence_application_mm',
                'maxitems'          => 9999,
                'appearance'        => [
                    'collapseAll'                     => 1,
                    'levelLinksPosition'              => 'top',
                    'showSynchronizationLink'         => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'useSortable'                     => 0,
                    'showAllLocalizationLink'         => 1,
                    'enabledControls'                 => [
                        'info'     => true,
                        'new'      => false,
                        'dragdrop' => true,
                        'sort'     => false,
                        'hide'     => true,
                        'delete'   => false,
                        'localize' => true,
                    ],
                ],
            ],
        ],

        'showincalendar' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.showincalendar',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'enable_application' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.enable_application',
            'config'  => [
                'type'    => 'check',
                'default' => 0,
            ],
        ],
        'slots' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.slots',
            'config'  => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'early_bird_date' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.early_bird_date',
            'config'  => [
                'type' => 'input',
                'size' => 16,
                'max'  => 20,
                'eval' => 'datetime',
            ],
        ],
        'locations' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.locations',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_datednews_domain_model_location',
                'MM'            => 'tx_datednews_news_location_mm',
                'size'          => 10,
                'autoSizeMax'   => 30,
                'maxitems'      => 9999,
                'multiple'      => 0,
                'wizards'       => [
                    '_PADDING'  => 1,
                    '_VERTICAL' => 1,
                    'edit'      => [
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'type'                     => 'popup',
                        'title'                    => 'Edit',
                        'icon'                     => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                    'add' => [
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                        'type'   => 'script',
                        'title'  => 'Create new',
                        'icon'   => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
                        'params' => [
                            'table'    => 'tx_datednews_domain_model_location',
                            'pid'      => '###CURRENT_PID###',
                            'setValue' => 'prepend',
                        ],
                    ],
                ],
            ],
        ],
        'persons' => [
            'exclude' => 1,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.persons',
            'config'  => [
                'type'          => 'select',
                'renderType'    => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_datednews_domain_model_person',
                'MM'            => 'tx_datednews_newsrecurrence_person_mm',
                'size'          => 10,
                'autoSizeMax'   => 30,
                'maxitems'      => 9999,
                'multiple'      => 0,
                'wizards'       => [
                    '_PADDING'  => 1,
                    '_VERTICAL' => 1,
                    'edit'      => [
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'type'                     => 'popup',
                        'title'                    => 'Edit',
                        'icon'                     => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_edit.gif',
                        'popup_onlyOpenIfSelected' => 1,
                        'JSopenParams'             => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                    'add' => [
                        'module' => [
                            'name' => 'wizard_add',
                        ],
                        'type'   => 'script',
                        'title'  => 'Create new',
                        'icon'   => 'EXT:backend/Resources/Public/Images/FormFieldWizard/wizard_add.gif',
                        'params' => [
                            'table'    => 'tx_datednews_domain_model_person',
                            'pid'      => '###CURRENT_PID###',
                            'setValue' => 'prepend',
                        ],
                    ],
                ],
            ],
        ],
        // set by JS if recurrence_updated_behavior of parent event is set > 3. This will disregard changes directly made in this model.
        'disregard_changes_on_saving' => [
            'exclude' => false,
            'config' => [
                'type'  => 'check',
                'items' => [
                    ['', 0],
                ],
                'cols'    => 'inline',
                'default' => 0,
            ],
        ],
    ],
];
