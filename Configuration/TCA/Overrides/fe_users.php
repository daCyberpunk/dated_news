<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
$tmp_columns = [

    'applications' => [
        'exclude' => 1,
        'label'   => 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:tx_datednews_domain_model_news.application',
        'config'  => [
            'type'              => 'inline',
            'foreign_table'     => 'tx_datednews_domain_model_application',
            'MM'                => 'tx_datednews_feuser_application_mm',
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

];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_columns);

$GLOBALS['TCA']['fe_users']['types']['Tx_Extbase_Domain_Model_FrontendUser']['showitem'] .= ',USER,';
$GLOBALS['TCA']['fe_users']['types']['Tx_Extbase_Domain_Model_FrontendUser']['showitem'] .= 'applications';

// Use first and last name as default label instead of username:
$GLOBALS['TCA']['fe_users']['ctrl']['label'] = 'last_name';
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt'] = 'first_name';
$GLOBALS['TCA']['fe_users']['ctrl']['label_alt_force'] = true;
