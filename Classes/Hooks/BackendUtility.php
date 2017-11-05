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
 * Hook into \TYPO3\CMS\Backend\Utility\BackendUtility to change flexform behaviour
 * depending on action selection.
 */
class BackendUtility extends \GeorgRinger\News\Hooks\BackendUtility
{
    /**
     * Fields which are removed in calendar view.
     *
     * @var array
     */
    public $removedFieldsInCalendarView = [
        'sDEF'       => 'dateField,singleNews,previewHiddenRecords, orderBy, orderDirection',
        'additional' => 'hidePagination,itemsPerPage,topNewsFirst',
        'template'   => 'templateLayout',
    ];

    /**
     * @param array $params
     * @param array $reference
     *
     * @return void
     */
    public function updateFlexformsDatedNews(&$params, &$reference)
    {
        if ($params['selectedView'] === 'News->calendar') {
            $removedFields = $this->removedFieldsInCalendarView;
            $this->deleteFromStructure($params['dataStructure'], $removedFields);
        }
    }
}
