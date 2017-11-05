<?php
namespace FalkRoeder\DatedNews\Hooks;

/***************************************************************
 *  Copyright notice
 *  (c) 2013 Jo Hasenau <info@cybercraft.de>, Tobias Ferger <tobi@tt36.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Backend\Clipboard\Clipboard;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class/Function which adds the necessary ExtJS.
 *
 * @package TYPO3
 * @subpackage dated_news
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

        if (get_class($GLOBALS['SOBE']) === 'TYPO3\CMS\Backend\Controller\EditDocumentController' && array_keys($GLOBALS['SOBE']->editconf)[0] === 'tx_news_domain_model_news') {
            $lang = $GLOBALS['LANG'];
            $languageLabels = array(
                'datedNews.modalHeader' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.header',1),
                'datedNews.overwrite.all' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.all',1),
                'datedNews.overwrite.noneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.noneModified',1),
                'datedNews.overwrite.allFieldsAll' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.allFieldsAll',1),
                'datedNews.overwrite.allFieldsNoneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.allFieldsNoneModified',1),
                'datedNews.overwrite.changedFieldsAll' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.changedFieldsAll',1),
                'datedNews.overwrite.changedFieldsNoneModified' => $lang->sL('LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xml:tx_dated_news.modal.overwrite.changedFieldsNoneModified',1),

            );
            $pageRenderer->addInlineLanguageLabelArray($languageLabels);
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/DatedNews/Backend/ConfirmationDialog');
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/DatedNews/Backend/RecurrenceOptions');
            return;
        }

    }
}