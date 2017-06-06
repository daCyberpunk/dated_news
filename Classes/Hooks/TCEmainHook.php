<?php
namespace FalkRoeder\DatedNews\Hooks;

use \Recurr\Rule;
use \Recurr\Transformer\ArrayTransformer;
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
        unset($fieldArray['ud_req_behavior']);
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

        $news = $newsRepository->findByIdentifier($id);


        


        if($settings['recurrence'] === 0) {
            //delete all recurrences if exist
        } else {
            $rule = new Rule;
            $rule->setStartDate($news->getEventstart());
            switch ($settings['recurrence']) {
                case 1:
                    // daily
                    $rule->setFreq('DAILY');
                    break;
                case 2:
                    // weekly
                    $rule->setFreq('Weekly');
                    break;
                case 3:
                    // workdays
                    $rule->setByDay([0,1,2,3,4]);
                    break;
                case 4:
                    // every other week
                    $rule->setFreq('Weekly')->setInterval(2);
                    break;
                case 5:
                    // monthly
                    $rule->setFreq('Monthly');
                    break;
                case 6:
                    // yearly
                    $rule->setFreq('Yearly');
                    break;
                case 7:
                    // user defined
                    break;
                default:
            }
            if($settings['recurrence_type'] === 1) {
                $rule->setEndDate($settings['recurrence_until']);
            } else {
                $rule->setCount($settings['recurrence_count']);
            }
//                    \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rule,'TCEmainHook:78');
            $transformer = new ArrayTransformer();
            $recurrences = $transformer->transform($rule);
//            foreach ($recurrences as $rec) {
//                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rec,'TCEmainHook:88');
//
//            }
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