<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence',
        'label' => 'eventstart',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'eventstart,eventend,eventlocation,bodytext,teaser,parent_event',
        'iconfile' => 'EXT:dated_news/Resources/Public/Icons/tx_datednews_domain_model_newsrecurrence.gif'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, eventstart, eventend, eventlocation, bodytext, teaser, modified, parent_event',
    ],
    'types' => [
        '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, modified, eventstart, eventend, eventlocation, bodytext, teaser, parent_event'],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_datednews_domain_model_newsrecurrence',
                'foreign_table_where' => 'AND tx_datednews_domain_model_newsrecurrence.pid=###CURRENT_PID### AND tx_datednews_domain_model_newsrecurrence.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:lang/locallang_core.xlf:labels.enabled'
                    ]
                ],
            ],
        ],

        'eventstart' => [
            'exclude' => false,
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventstart',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'max'  => 20,
                'eval' => 'datetime',
            ],
        ],
        'eventend' => [
            'exclude' => false,
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventend',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'max'  => 20,
                'eval' => 'datetime',
            ],
        ],
        'eventlocation' => [
            'exclude' => false,
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.eventlocation',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'bodytext' => [
            'exclude' => false,
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.bodytext',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim',
            ],
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]'
        ],
        'teaser' => [
            'exclude' => false,
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'modified' => [
            'label' => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.modified',
            'config' => [
                'type' => 'check',
                'items' => [
                    [ 'lock record', 0 ],
                ],
                'cols' => 'inline',
                'default' => 0
            ],
        ],
        'parent_event' => [
            'exclude' => 0,
            'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_newsrecurrence.parent_event',
            'config'  => [
                'type' => 'inline',
                'foreign_table' => 'tx_news_domain_model_news',
                'MM' => 'tx_datednews_news_newsrecurrence_mm',
                'MM_opposite_field' => 'newsrecurrence',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],
    
    ],
];
