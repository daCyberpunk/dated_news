<?php

namespace FalkRoeder\DatedNews\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormHook
 */
class FlexFormHook
{
    /**
     * For TYPO3 V7.
     *
     * @param array  $dataStructArray
     * @param array  $conf
     * @param array  $row
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
            $selectedView = '';
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
                $dataStructArray['sheets']['confirmation'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/application.xml';
            }

            if ($selectedView === 'News->detail') {
                $dataStructArray['sheets']['additional'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/additional.xml';
            }

            if ($selectedView === 'News->calendar') {
                $dataStructArray['sheets']['calendar'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/calendar.xml';
            }

            if ($selectedView === 'News->list') {
                $dataStructArray['sheets']['additional'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/additional.xml';
                $dataStructArray['sheets']['confirmation'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/application.xml';
            }

            $dataStructArray['sheets']['sDEF'] = 'typo3conf/ext/dated_news/Configuration/FlexForms/settings.xml';
        }
    }

    /**
     * TYPO3 V8.
     *
     * @param array  $fieldTca  Full TCA of the field in question that has type=flex set
     * @param string $tableName The table name of the TCA field
     * @param string $fieldName The field name
     * @param array  $row       The data row
     *
     * @return array Identifier
     */
    public function getDataStructureIdentifierPreProcess(array $fieldTca, string $tableName, string $fieldName, array $row)
    {
        if ($tableName === 'tt_content' && $row['CType'] === 'list' && $row['list_type'] === 'news_pi1') {
            $dataStructArray = GeneralUtility::xml2array(
                file_get_contents(PATH_site.'typo3conf/ext/news/Configuration/FlexForms/flexform_news.xml')
            );
            $settings = file_get_contents(PATH_site.'typo3conf/ext/dated_news/Configuration/FlexForms/settings.xml');
            $calendar = file_get_contents(PATH_site.'typo3conf/ext/dated_news/Configuration/FlexForms/calendar.xml');
            $application = file_get_contents(PATH_site.'typo3conf/ext/dated_news/Configuration/FlexForms/application.xml');
            $additional = file_get_contents(PATH_site.'typo3conf/ext/dated_news/Configuration/FlexForms/additional.xml');
            $confirmation = file_get_contents(PATH_site.'typo3conf/ext/dated_news/Configuration/FlexForms/confirmation.xml');

            $dataStructArray['sheets']['sDEF'] = GeneralUtility::xml2array($settings);

            if (is_string($row['pi_flexform'])) {
                $flexformSelection = GeneralUtility::xml2array($row['pi_flexform']);
            } else {
                $flexformSelection = $row['pi_flexform'];
            }
            $selectedView = '';
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
                $dataStructArray['sheets']['application'] = GeneralUtility::xml2array($application);
                $dataStructArray['sheets']['additional'] = GeneralUtility::xml2array($additional);
            }

            if ($selectedView === 'News->confirmApplication') {
                $dataStructArray['sheets']['application'] = GeneralUtility::xml2array($application);
            }

            if ($selectedView === 'News->detail') {
                $dataStructArray['sheets']['additional'] = GeneralUtility::xml2array($additional);
            }

            if ($selectedView === 'News->calendar') {
                $dataStructArray['sheets']['calendar'] = GeneralUtility::xml2array($calendar);
            }

            if ($selectedView === 'News->list') {
                $dataStructArray['sheets']['additional'] = GeneralUtility::xml2array($additional);
                $dataStructArray['sheets']['confirmation'] = GeneralUtility::xml2array($confirmation);
            }
        }

        $identifier = [
            'type'       => 'file',
            'flexformDS' => $dataStructArray,
        ];

        return $identifier;
    }

    /**
     * TYPO3 V8.
     *
     * @param array $identifier identifier from getDataStructureIdentifierPreProcess hook
     *
     * @return array
     */
    public function parseDataStructureByIdentifierPreProcess(array $identifier)
    {
        return $identifier['flexformDS'];
    }
}
