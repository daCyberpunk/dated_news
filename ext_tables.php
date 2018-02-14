<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Dated News');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_datednews_domain_model_newsrecurrence', 'EXT:dated_news/Resources/Private/Language/locallang_csh_tx_datednews_domain_model_newsrecurrence.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_datednews_domain_model_newsrecurrence');

if (TYPO3_MODE == 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Hooks/PageRenderer.php:FalkRoeder\\DatedNews\\Hooks\\PageRenderer->addBackendJS';
}

//TYPO3 V7
$TCA['tx_news_domain_model_news']['ctrl']['requestUpdate'] = 'eventtype';
if (TYPO3_MODE=='BE') {
    $GLOBALS['TBE_STYLES']['skins'][$_EXTKEY] = [
        'name' => $_EXTKEY,
        'stylesheetDirectories' => [
            'css' => 'EXT:' . $_EXTKEY . '/Resources/Public/CSS/Backend/'
        ]
    ];
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['orderByNews'] .= ',eventstart';
