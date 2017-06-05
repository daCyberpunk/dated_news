<?php
namespace FalkRoeder\DatedNews\Hooks;


class TCEmainHook {



    public function processCmdmap_preProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($command,'TCEmainHook:6');

    }
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }

    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        // render hook only for news table
        if($table !== 'tx_news_domain_model_news'){
            return;
        }
        //get all recurrence settings
        $settings = [];
        foreach ($fieldArray as $key => $val){
            if($this->startsWith($key, 'recurrence') || $this->startsWith($key, 'ud_')){
                $settings[$key] = $val;
            }
        }

        /** @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var $newsRepository \GeorgRinger\News\Domain\Repository\NewsRepository */
        $newsRepository = $extbaseObjectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        /** @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository */
        $newsRecurrenceRepository = $extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository');
        


        if($settings['recurrence'] === 0) {
            //delete all recurrences if exist
        } else {
            switch ($settings['recurrence']) {
                case 1:
                    // daily
                    break;
                case 2:
                    // weekly
                    break;
                case 3:
                    // workdays
                    break;
                case 4:
                    // every other week
                    break;
                case 5:
                    // monthly
                    break;
                case 6:
                    // yearly
                    break;
                case 7:
                    // user defined
                    break;
            }
        }
        
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($id,'TCEmainHook:13');
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($fieldArray,'TCEmainHook:13');
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($table,'TCEmainHook:13');
//\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pObj,'TCEmainHook:13');

    }

    public function getDatesFromRecurrenceSettings($settings){

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