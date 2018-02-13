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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FlexFormHook.
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
            $dataStructArray = $this->getManipulatedDataStructure($row, $dataStructArray);


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

            $dataStructArray['sheets']['sDEF'] = 'EXT:dated_news/Configuration/FlexForms/settings.xml';
            $dataStructArray['sheets']['additional'] = 'EXT:dated_news/Configuration/FlexForms/additional_original.xml';

            $dataStructArray = $this->getManipulatedDataStructure($row, $dataStructArray);



            $identifier = [
                'type'       => 'file',
                'flexformDS' => $dataStructArray,
            ];
        } else {
            $identifier = [];
        }
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
        if (!empty($identifier['flexformDS'])) {
            return $identifier['flexformDS'];
        } else {
            return [];
        }
    }


    /**
     * getManipulatedDataStructure
     *
     * @param array $row
     * @param array $dataStructArray
     * @return array
     */
    protected function getManipulatedDataStructure($row, $dataStructArray)
    {
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
            $dataStructArray['sheets']['application'] = 'EXT:dated_news/Configuration/FlexForms/application.xml';
            $dataStructArray['sheets']['additional'] = 'EXT:dated_news/Configuration/FlexForms/additional.xml';
        }

        if ($selectedView === 'News->confirmApplication') {
            $dataStructArray['sheets']['confirmation'] = 'EXT:dated_news/Configuration/FlexForms/confirmation.xml';
        }

        if ($selectedView === 'News->eventDetail') {
            $dataStructArray['sheets']['additional'] = 'EXT:dated_news/Configuration/FlexForms/additional.xml';
        }

        if ($selectedView === 'News->calendar') {
            $dataStructArray['sheets']['calendar'] = 'EXT:dated_news/Configuration/FlexForms/calendar.xml';
        }

        if ($selectedView === 'News->list') {
            $dataStructArray['sheets']['additional'] = 'EXT:dated_news/Configuration/FlexForms/additional.xml';
            $dataStructArray['sheets']['confirmation'] = 'EXT:dated_news/Configuration/FlexForms/confirmation.xml';
        }


        return $dataStructArray;
    }
}
