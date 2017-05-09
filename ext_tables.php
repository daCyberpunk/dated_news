<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Dated News');

//TYPO3 V7
$TCA['tx_news_domain_model_news']['ctrl']['requestUpdate'] = 'eventtype';