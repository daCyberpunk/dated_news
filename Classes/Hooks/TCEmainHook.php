<?php
namespace FalkRoeder\DatedNews\Hooks;

use FalkRoeder\DatedNews\Domain\Model\NewsRecurrence;
use \FalkRoeder\DatedNews\Services\RecurringService;
use Recurr\RecurrenceCollection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
class TCEmainHook {

    /** @var $newsRepository \GeorgRinger\News\Domain\Repository\NewsRepository */
    protected $newsRepository = null;

    /** @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository */
    protected $newsRecurrenceRepository = null;
    protected $persistenceManager = null;

    public function processCmdmap_preProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
    }
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
    }

    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        // render hook only for news table
        if($table !== 'tx_news_domain_model_news' ||
            $fieldArray['recurrence_updated_behavior'] === 1 && (int)$fieldArray['recurrence'] > 0
        ){
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
        $this->newsRepository = $extbaseObjectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        $this->newsRecurrenceRepository = $extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository');

        /** @var $recurrenceService \FalkRoeder\DatedNews\Services\RecurrenceService */
        $recurrenceService = $extbaseObjectManager->get('FalkRoeder\DatedNews\Services\RecurrenceService');
        $this->persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');

        $news = $this->newsRepository->findByIdentifier($id);
        if((int)$settings['recurrence'] === 0 ) {
            //delete/hide all recurrences if exist ?
//            $this->hideAllRecurrences($news);
        } else {
            try {
                $eventstart = new \DateTime($fieldArray['eventstart']);
                $eventend = new \DateTime($fieldArray['eventend']);
            } catch (\Exception $exception) {
                return;
            }
            //get collection of all recurrences
            $recurrences = $recurrenceService->getRecurrences($eventstart, $eventend, $settings);
            if((int)$settings['recurrence_updated_behavior'] > 1) {
                //
                $filteredRecurrences = $this->filterRecurrences($news,$recurrences, (int)$settings['recurrence_updated_behavior']);
                $oldRecurrences = $filteredRecurrences[0];
                $newRecurrences = $filteredRecurrences[1];

                if((int)$settings['recurrence_updated_behavior'] < 4) {
                    //remove old (none modified) recurrences
                    foreach ($oldRecurrences as $oldRec) {
                        //todo remove hidden childobjects too
                        $news->removeNewsRecurrence($oldRec);
                        $this->newsRecurrenceRepository->remove($oldRec);
                    }
                    $this->persistenceManager->persistAll();

                    //add new recurrences
                    foreach ($newRecurrences as $rec) {
                        $newRecurrence = $extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Model\NewsRecurrence');
                        $newRecurrence->setEventstart($rec->getStart());
                        $newRecurrence->setEventend($rec->getEnd());
                        $newRecurrence->setBodytext($news->getBodytext());
                        $newRecurrence->setTeaser($news->getTeaser());
                        $newRecurrence->addParentEvent($news);
                        $this->newsRecurrenceRepository->add($newRecurrence);
//                    $this->persistenceManager->persistAll();// persist here, otherwise newRecurrence is lost when next loop starts
                        $this->newsRepository->update($news);
                        $this->persistenceManager->persistAll(); // persist here, otherwise newRecurrence is lost when next loop starts
                    }
                }
                if((int)$settings['recurrence_updated_behavior'] > 3 ) {
                    // change fields in (none modified) recurrences
                    foreach ($oldRecurrences as $oldRec) {
                        $oldRec->setBodytext($fieldArray['bodytext']);
                        $oldRec->setTeaser($fieldArray['teaser']);
                        $this->newsRecurrenceRepository->update($oldRec);
                    }
                    $this->persistenceManager->persistAll();
                }
            }
        }
    }
    /*
     * hideAllRecurrences
     * currently unused
     *
     * */
    public function hideAllRecurrences($news){
        $recurrences = $news->getNewsRecurrence();
        $recurrences = $recurrences->toArray();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($recurrences,'TCEmainHook:110');

        foreach ($recurrences as $key => $rec) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rec->isHidden(),$rec->getUid() . ' TCEmainHook:114');
            $rec->setHidden(true);
            $this->newsRecurrenceRepository->update($rec);
            $this->persistenceManager->persistAll();

        }
        $this->newsRepository->update($news);
        $this->persistenceManager->persistAll();
    }

   public function filterRecurrences($news, $recurrences, $updateBehavior) {
       $oldRecurrences = $news->getNewsRecurrence();
       $oldRecurrences = $oldRecurrences->toArray();
       //todo comment in when able to remove hidden child objects to, see todo above
//       $oldRecurrences = $this->newsRecurrenceRepository->getByParentId($news->getUid());

       if($updateBehavior === 3 || $updateBehavior === 5) {
           //filter modified recurrences
           foreach ($oldRecurrences as $key => $oldRec) {
               if($oldRec->isModified()){
                   unset($oldRecurrences[$key]);
                   $recurrences->removeElement($recurrences->startsBetween($oldRec->getEventstart(),$oldRec->getEventstart(),true)->getValues()[0]);
               }
           }
       }
       return [$oldRecurrences,$recurrences];
   }

    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted=NULL, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
    }
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
    }
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        if($table === 'tx_news_domain_model_news' || $fieldArray['recurrence_updated_behavior'] === 1){
            unset($fieldArray['recurrence_updated_behavior']);
        }
    }
    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
}