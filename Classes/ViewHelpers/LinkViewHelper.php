<?php

namespace FalkRoeder\DatedNews\ViewHelpers;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * ViewHelper to render links from news records to detail view or page.
 *
 * # Example: Basic link
 * <code>
 * <n:link newsItem="{newsItem}" settings="{settings}">
 *    {newsItem.title}
 * </n:link>
 * </code>
 * <output>
 * A link to the given news record using the news title as link text
 * </output>
 *
 * # Example: Set an additional attribute
 * # Description: Available: class, dir, id, lang, style, title, accesskey, tabindex, onclick
 * <code>
 * <n:link newsItem="{newsItem}" settings="{settings}" class="a-link-class">fo</n:link>
 * </code>
 * <output>
 * <a href="link" class="a-link-class">fo</n:link>
 * </output>
 *
 * # Example: Return the link only
 * <code>
 * <n:link newsItem="{newsItem}" settings="{settings}" uriOnly="1" />
 * </code>
 * <output>
 * The uri is returned
 * </output>
 */
class LinkViewHelper extends \GeorgRinger\News\ViewHelpers\LinkViewHelper
{

    /**
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Utility\Div
     * @inject
     */
    protected $div;
    
    public function initializeArguments()
    {
        parent::initializeArguments();
        //argument didn't exist in original viewhelper
        $this->registerArgument('forceAbsoluteUrl', 'boolean', 'force absolute uri', false);
        $this->registerArgument('eventDetail', 'boolean', 'use eventDetail Action instead detail', false);
    }

    /**
     * Generate the link configuration for the link to the news item.
     *
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     * @param array                               $tsSettings
     * @param array                               $configuration
     *
     * @return array
     */
    protected function getLinkToNewsItem(
        \GeorgRinger\News\Domain\Model\News $newsItem,
        $tsSettings,
        array $configuration = []
    ) {
        if (!isset($configuration['parameter'])) {
            $detailPid = 0;
            $detailPidDeterminationMethods = GeneralUtility::trimExplode(',', $tsSettings['detailPidDetermination'],
                true);

            // if TS is not set, prefer flexform setting
            if (!isset($tsSettings['detailPidDetermination'])) {
                $detailPidDeterminationMethods[] = 'flexform';
            }

            foreach ($detailPidDeterminationMethods as $determinationMethod) {

                if ($callback = $this->detailPidDeterminationCallbacks[$determinationMethod]) {
                    if ($detailPid = call_user_func([$this, $callback], $tsSettings, $newsItem)) {
                        break;
                    }
                }
            }

            if (!$detailPid) {
                $detailPid = $GLOBALS['TSFE']->id;
            }
            $configuration['parameter'] = $detailPid;
        }




        // missing in original Viewhelper
        if ($this->arguments['forceAbsoluteUrl']) {
            $configuration['forceAbsoluteUrl'] = true;
        }

        $configuration['useCacheHash'] = $GLOBALS['TSFE']->sys_page->versioningPreview ? 0 : 1;
        $configuration['additionalParams'] .= '&tx_news_pi1[news]='.$this->getNewsId($newsItem);
        $action = 'detail';
        if ($this->arguments['eventDetail'] || $tsSettings['switchableControllerActions'] === 'News->list;News->eventDetail') {
            $action = 'eventDetail';
        }
        if ((int) $tsSettings['link']['skipControllerAndAction'] !== 1) {
            $configuration['additionalParams'] .= '&tx_news_pi1[controller]=News'.
                '&tx_news_pi1[action]=' . $action;
        }

        // Add date as human readable
        if ($tsSettings['link']['hrDate'] == 1 || $tsSettings['link']['hrDate']['_typoScriptNodeValue'] == 1) {
            $dateTime = $newsItem->getDatetime();

            if (!empty($tsSettings['link']['hrDate']['day'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[day]='.$dateTime->format($tsSettings['link']['hrDate']['day']);
            }
            if (!empty($tsSettings['link']['hrDate']['month'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[month]='.$dateTime->format($tsSettings['link']['hrDate']['month']);
            }
            if (!empty($tsSettings['link']['hrDate']['year'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[year]='.$dateTime->format($tsSettings['link']['hrDate']['year']);
            }
        }

        return $configuration;
    }
}
