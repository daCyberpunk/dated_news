<?php
namespace FalkRoeder\DatedNews\Hooks;

use \FalkRoeder\DatedNews\Services\RecurringService;
class TCEmainHook {

   


    public function processCmdmap_preProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($command,'TCEmainHook:6');

    }
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }

    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        // render hook only for news table
        if($table !== 'tx_news_domain_model_news' || $fieldArray['recurrence_updated_behavior'] === 1){
            return;
        }

        //get all recurrence settings
        $settings = [];
        foreach ($fieldArray as $key => $val){
            if($this->startsWith($key, 'recurrence') || $this->startsWith($key, 'ud_')){
                $settings[$key] = $val;
            }
        }
        unset($fieldArray['recurrence_updated_behavior']);

        /** @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var $newsRepository \GeorgRinger\News\Domain\Repository\NewsRepository */
        $newsRepository = $extbaseObjectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        /** @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository */
        $newsRecurrenceRepository = $extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository');

        $news = $newsRepository->findByIdentifier($id);

        /** @var $recurrenceService \FalkRoeder\DatedNews\Services\RecurrenceService */
        $recurrenceService = $extbaseObjectManager->get('FalkRoeder\DatedNews\Services\RecurrenceService');
        
        
        if($settings['recurrence'] === 0 || $settings['recurrence'] === null ) {
            //delete all recurrences if exist
        } else {
            $recurrences = $recurrenceService->getRecurrences($news->getEventstart(), $news->getEventend(), $settings);
            foreach ($recurrences as $rec) {
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rec,'TCEmainHook:88');
            }
        }
    }

   

    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted=NULL, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pObj,'TCEmainHook:22');
        
    }
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
}