<?php

namespace FalkRoeder\DatedNews\Hooks;
use TYPO3\CMS\Core\Utility\GeneralUtility;
class FlexFormHook
{
    /**
     * @param array $dataStructArray
     * @param array $conf
     * @param array $row
     * @param string $table
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table)
    {
        if ($table === 'tt_content' && $row['CType'] === 'list' && $row['list_type'] === 'news_pi1') {
            
            if (is_string($row['pi_flexform'])) {
                $flexformSelection = GeneralUtility::xml2array($row['pi_flexform']);
            } else {
                $flexformSelection = $row['pi_flexform'];
            }
            $selectedView= '';
            if (is_array($flexformSelection) && is_array($flexformSelection['data'])) {
                $selectedView = $flexformSelection['data']['sDEF']['lDEF']['switchableControllerActions']['vDEF'];
                if (!empty($selectedView)) {
                    $actionParts = GeneralUtility::trimExplode(';', $selectedView, true);
                    $selectedView = $actionParts[0];
                }

                // new plugin element
            } elseif (GeneralUtility::isFirstPartOfStr($row['uid'], 'NEW')) {
                // use List as starting view
                $selectedView = 'News->list';
            }

            if ($selectedView === 'News->createApplication') {
                $dataStructArray['sheets']['application'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/application.xml';
                $dataStructArray['sheets']['additional'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/additional.xml';
            }

            if ($selectedView === 'News->confirmApplication') {
                $dataStructArray['sheets']['confirmation'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/confirmation.xml';
            }
            
            if ($selectedView === 'News->eventDetail') {
                $dataStructArray['sheets']['additional'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/additional.xml';
            }

            if ($selectedView === 'News->calendar') {
                $dataStructArray['sheets']['calendar'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/calendar.xml';
            }

            $dataStructArray['sheets']['sDEF'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/settings.xml';
            
        }
    }
}
