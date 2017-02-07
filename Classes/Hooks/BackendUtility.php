<?php
namespace FalkRoeder\DatedNews\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook into \TYPO3\CMS\Backend\Utility\BackendUtility to change flexform behaviour
 * depending on action selection
 *
 */
class BackendUtility extends \GeorgRinger\News\Hooks\BackendUtility {

    /**
     * Fields which are removed in calendar view
     *
     * @var array
     */
    public $removedFieldsInCalendarView = [
        'sDEF' => 'dateField,singleNews,previewHiddenRecords, orderBy, orderDirection',
        'additional' => 'hidePagination,itemsPerPage,topNewsFirst',
        'template' => 'template'
    ];
    
    

    /**
     * @param array $params
     * @param array $reference
     * @return void
     */
    public function updateFlexformsDatedNews(&$params, &$reference) {
        if ($params['selectedView'] === 'News->calendar') {
            $removedFields = $this->removedFieldsInCalendarView;
            $this->deleteFromStructure($params['dataStructure'], $removedFields);
        }
        
    }
}