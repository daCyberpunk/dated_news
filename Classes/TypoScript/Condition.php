<?php

namespace FalkRoeder\DatedNews\TypoScript;

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
