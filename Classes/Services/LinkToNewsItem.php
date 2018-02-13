<?php

namespace FalkRoeder\DatedNews\Services;

/***
 *
 * This file is part of the "" Extension for TYPO3 CMS.
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
 * Link.
 */
class LinkToNewsItem
{
    /**
     * @var array
     */
    protected $detailPidDeterminationCallbacks = [
        'flexform'   => 'getDetailPidFromFlexform',
        'categories' => 'getDetailPidFromCategories',
        'default'    => 'getDetailPidFromDefaultDetailPid',
    ];

    /** @var $cObj ContentObjectRenderer */
    protected $cObj;

    /**
     * Gets detailPid from categories of the given news item. First will be return.
     *
     * @param array $settings
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     *
     * @return int
     */
    protected function getDetailPidFromCategories($settings, $newsItem)
    {
        $detailPid = 0;

        if ($newsItem->getCategories()) {
            foreach ($newsItem->getCategories() as $category) {
                if ($detailPid = (int) $category->getSinglePid()) {
                    break;
                }
            }
        }

        return $detailPid;
    }

    /**
     * Gets detailPid from defaultDetailPid setting.
     *
     * @param array $settings
     * @return int
     */
    protected function getDetailPidFromDefaultDetailPid($settings)
    {
        return (int) $settings['defaultDetailPid'];

    }

    /**
     * Gets detailPid from flexform of current plugin.
     *
     * @param array $settings
     * @return int
     */
    protected function getDetailPidFromFlexform($settings)
    {
        return (int) $settings['detailPid'];
    }

    /**
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     *
     * @return int
     */
    protected function getNewsId(\GeorgRinger\News\Domain\Model\News $newsItem)
    {
        $uid = $newsItem->getUid();
        // If a user is logged in and not in live workspace
        if ($GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->workspace > 0) {
            $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getLiveVersionOfRecord('tx_news_domain_model_news',
                $newsItem->getUid());
            if ($record['uid']) {
                $uid = $record['uid'];
            }
        }

        return $uid;
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
    public function getLink(
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

        $configuration['forceAbsoluteUrl'] = true;

        $configuration['useCacheHash'] = $GLOBALS['TSFE']->sys_page->versioningPreview ? 0 : 1;
        $configuration['additionalParams'] .= '&tx_news_pi1[news]='.$this->getNewsId($newsItem);

        // action is set to "detail" in original Viewhelper, but we overwiritten this action
        if ((int) $tsSettings['link']['skipControllerAndAction'] !== 1) {
            $configuration['additionalParams'] .= '&tx_news_pi1[controller]=News'.
                '&tx_news_pi1[action]=eventDetail';
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
        $this->cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        $url = $this->cObj->typoLink_URL($configuration);

        return $url;
    }
}


