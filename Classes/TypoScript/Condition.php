<?php

namespace FalkRoeder\DatedNews\TypoScript;

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
 * Example condition.
 */
class Condition extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition
{
    /**
     * Evaluate condition.
     *
     * @param array $conditionParameters
     *
     * @return bool
     */
    public function matchCondition(array $conditionParameters)
    {
        if ($conditionParameters[0] == 'isEventDetailView') {
            $params = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET();

            $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
            $newsRepository = $objectManager->get('GeorgRinger\\News\\Domain\\Repository\\NewsRepository');

            if (is_array($params['tx_news_pi1']) && !empty($params['tx_news_pi1'])) {
                $news = $newsRepository->findByIdentifier($params['tx_news_pi1']['news']);
                if (!is_null($news) && $news->getEnableApplication()) {
                    return true;
                }
            }
        }

        return false;
    }
}
