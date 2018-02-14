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

/**
 * Class PageLayoutView
 *
 */
class PageLayoutView
{
    /**
     * pageLayout
     *
     * @var \GeorgRinger\News\Hooks\PageLayoutView
     */
    protected $pageLayout = null;

    /**
     * Path to the locallang file
     *
     * @var string
     */
    const LLPATH = 'LLL:EXT:dated_news/Resources/Private/Language/locallang_db.xlf:';

    /**
     * Provide an extension summary for the month selection
     *
     * @param array $params
     * @param \GeorgRinger\News\Hooks\PageLayoutView $pageLayout
     * @return void
     */
    public function extensionSummary(array $params, \GeorgRinger\News\Hooks\PageLayoutView $pageLayout)
    {
        $this->pageLayout = $pageLayout;

        switch ($params['action']) {
            case 'news_list':
//                $this->addViewName($params['action']);
                break;
            case 'news_calendar':
                $this->addViewName($params['action']);
                $pageLayout->getStartingPoint();
                $pageLayout->getTimeRestrictionSetting();
                $pageLayout->getTopNewsRestrictionSetting();
                $pageLayout->getOrderSettings();
                $pageLayout->getCategorySettings();
                $pageLayout->getArchiveSettings();
                $pageLayout->getOffsetLimitSettings();
                $pageLayout->getDetailPidSetting();
                $pageLayout->getListPidSetting();
                $pageLayout->getTagRestrictionSetting();
                break;
            case 'news_eventdetail':
                $this->addViewName($params['action']);
                $pageLayout->getSingleNewsSettings();
                $pageLayout->getDetailPidSetting();
                $pageLayout->getTemplateLayoutSettings($params['row']['pid']);
                break;
            case 'news_createapplication':
                $this->addViewName($params['action']);
                $this->getConfirmationPidSetting();
                break;
            case 'news_confirmapplication':
                $this->addViewName($params['action']);
                break;

        }
    }

    /**
     * Render Confirmation PID settings
     *
     */
    public function getConfirmationPidSetting()
    {
        $pid = (int)$this->pageLayout->getFieldFromFlexform('settings.confirmationPid', 'additional');
        if ($pid > 0) {
            $content = $this->pageLayout->getRecordData($pid);

            $this->pageLayout->tableData[] = [
                $this->pageLayout->getLanguageService()->sL(self::LLPATH . 'tx_dated_news.mode.news_confirmapplication'),
                $content
            ];
        }
    }

    /**
     * Adds the name of the current view
     *
     */
    public function addViewName($actionName)
    {
        $this->pageLayout->tableData[] = [
                '<div class="alert alert-info" style="color:black; display: inherit;">' . $this->pageLayout->getLanguageService()->sL(self::LLPATH . 'tx_dated_news.mode.' . $actionName) . '</div>',
                ''
            ];
    }
}
