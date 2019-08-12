<?php
namespace FalkRoeder\DatedNews\Hooks;

/***
 *
 * This file is part of the "Dated News" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 * Author Falk Röder <mail@falk-roeder.de>
 *
 ***/

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class/Function which adds the necessary ExtJS.
 *
 */
class PageRenderer implements SingletonInterface
{
    
    /**
     * wrapper function called by hook (\TYPO3\CMS\Core\Page\PageRenderer->render-preProcess)
     *
     * @param array $parameters : An array of available parameters
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer : The parent object that triggered this hook
     *
     * @return void
     */
    public function addBackendJS($parameters, &$pageRenderer)
    {
        if (!empty($GLOBALS['SOBE']) && (get_class($GLOBALS['SOBE'])  === 'TYPO3\CMS\Backend\Controller\EditDocumentController' && array_keys($GLOBALS['SOBE']->editconf)[0] === 'tx_news_domain_model_news')) {
            $lang = $GLOBALS['LANG'];
            $languageLabels = [
                'datedNews.modalHeader' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.header', 1),
                'datedNews.overwrite.all' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.all', 1),
                'datedNews.overwrite.noneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.noneModified', 1),
                'datedNews.overwrite.allFieldsAll' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.allFieldsAll', 1),
                'datedNews.overwrite.allFieldsNoneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.allFieldsNoneModified', 1),
                'datedNews.overwrite.changedFieldsAll' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.changedFieldsAll', 1),
                'datedNews.overwrite.changedFieldsNoneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.changedFieldsNoneModified', 1),
                
            ];
            $pageRenderer->addInlineLanguageLabelArray($languageLabels);
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/DatedNews/Backend/ConfirmationDialog');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/DatedNews/Backend/RecurrenceOptions');
            return;
        }
    }
}
