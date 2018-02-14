<?php

defined('TYPO3_MODE') or die();

$ll = 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:';

/**
 * Add extra fields to the sys_category record.
 */
$newSysCategoryColumns = [
    'textcolor' => [
        'exclude' => 0,
        'label'   => $ll . 'tx_datednews_domain_model_category.textcolor',
        'config'  => [
            'type'    => 'input',
            'size'    => 10,
            'wizards' => [
                'colorChoice' => [
                    'type'   => 'colorbox',
                    'title'  => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_dated_news.colorPick',
                    'module' => [
                        'name' => 'wizard_colorpicker',
                    ],
                    'JSopenParams' => 'height=600,width=380,status=0,menubar=0,scrollbars=1',
                    'exampleImg'   => 'EXT:dated_news/Resources/Public/Images/colorpicker_image.jpg',
                ],
            ],
        ],
    ],
    'backgroundcolor' => [
        'exclude' => 0,
        'label'   => $ll . 'tx_datednews_domain_model_category.backgroundcolor',
        'config'  => [
            'type'    => 'input',
            'size'    => 10,
            'wizards' => [
                'colorChoice' => [
                    'type'   => 'colorbox',
                    'title'  => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_dated_news.colorPick', //pick a color
                    'module' => [
                        'name' => 'wizard_colorpicker',
                    ],
                    'JSopenParams' => 'height=600,width=380,status=0,menubar=0,scrollbars=1',
                    'exampleImg'   => 'EXT:dated_news/Resources/Public/Images/colorpicker_image.jpg',
                ],
            ],
        ],
    ],

];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $newSysCategoryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    'textcolor',
    '',
    'after:shortcut'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    'backgroundcolor',
    '',
    'after:textcolor'
);
