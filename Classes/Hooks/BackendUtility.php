<?php
namespace FalkRoeder\DatedNews\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUtility extends \GeorgRinger\News\Hooks\BackendUtility {

    /**
     * Fields which are removed in calendar view
     *
     * @var array
     */
    public $removedFieldsInCalendarView = array(
        'sDEF' => 'dateField,singleNews,previewHiddenRecords',
        'additional' => '',
        'template' => ''
    );

    /**
     * @param array|string $params
     * @param array $reference
     */
    public function updateFlexforms(&$params, &$reference) {
        if ($params['selectedView'] === 'News->calendar') {
            $removedFields = $this->removedFieldsInCalendarView;

            $this->deleteFromStructure($params['dataStructure'], $removedFields);
        }
        
    }
}