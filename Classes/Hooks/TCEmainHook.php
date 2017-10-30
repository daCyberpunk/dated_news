<?php

namespace FalkRoeder\DatedNews\Hooks;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TCEmainHook.
 */
class TCEmainHook
{

    /**
     * @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $extbaseObjectManager = null;

    /**
     * @var $newsRepository \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository = null;

    /**
     * @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository
     */
    protected $newsRecurrenceRepository = null;

    /**
     * @var $applicationRepository \FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository
     */
    protected $applicationRepository = null;

    /**
     * @var $locationRepository \FalkRoeder\DatedNews\Domain\Repository\LocationRepository
     */
    protected $locationRepository = null;

    /**
     * @var $persistenceManager \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    /**
     * @var $recurrenceService \FalkRoeder\DatedNews\Services\RecurrenceService
     */
    protected $recurrenceService = null;

    /**
     * @var $messageService \TYPO3\CMS\Core\Messaging\FlashMessageService
     */
    protected $messageService = null;

    /**
     * @var $availableFields array
     */
    protected $availableFields = ['bodytext','teaser','slots','early_bird_date','enable_application','showincalendar','locations','persons'];

    /**
     * __construct.
     */
    public function __construct()
    {
        //initialize some objects/services we need
        $this->extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->newsRepository = $this->extbaseObjectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        $this->newsRecurrenceRepository = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository');
        $this->applicationRepository = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository');
        $this->recurrenceService = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Services\RecurrenceService');
        $this->persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(date_default_timezone_get(),'NewsController:177');
    }

    /**
     * processDatamap_preProcessFieldArray.
     *
     * @param array $fieldArray
     * @param $table
     * @param $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
        if ($table === 'tx_datednews_domain_model_newsrecurrence' ) {
            // unset all fields except hidden field under following conditions:
            // record was saved as inline element of a news record AND
            // update behavior of parent record was set  > 3 (copying fields from parent to child)
            // otherwise the changes would be overwritten through the normal saving behavior of TCEMain
            if((int)$fieldArray['disregard_changes_on_saving'] === 1) {
                foreach ($fieldArray as $key => $field) {
                    if($key !== 'hidden') {
                        unset($fieldArray[$key]);
                    }
                }
                return;
            }
            unset($fieldArray['disregard_changes_on_saving']);
        }

        if ($table !== 'tx_news_domain_model_news' ||
            $fieldArray['recurrence_updated_behavior'] === 1 && (int) $fieldArray['recurrence'] > 0
        ) {
            unset($fieldArray['recurrence_updated_behavior']);
            return;
        }



        //if eventdates invalid or startdate after enddate, do nothing on recurring events, just store their on data
        $eventDates = $this->hasValidEventdates($fieldArray);
        if($eventDates === false ) {
            unset($fieldArray['recurrence_updated_behavior']);
            return;
        }

        //get all recurrence settings
        $settings = [];
        foreach ($fieldArray as $key => $val) {
            if ($this->startsWith($key, 'recurrence') || $this->startsWith($key, 'ud_')) {
                $settings[$key] = $val;
            }
        }
        unset($fieldArray['recurrence_updated_behavior']);



        $news = $this->newsRepository->findByIdentifier($id);
        //get collection of all recurrences
        $recurrences = $this->recurrenceService->getRecurrences($eventDates[0], $eventDates[1], $settings);

        if ((int) $settings['recurrence'] === 0) {
            // if recurrence option is set to none recurrences, delete all existing recurrences
            if ( NULL !== $news) {
                $emptyObj = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
                $news->setNewsRecurrence($emptyObj);
                $this->newsRepository->update($news);
                $oldRecurrences = $news->getNewsRecurrence();
                foreach ($oldRecurrences as $oldRec) {
                    $this->newsRecurrenceRepository->remove($oldRec);
                }
                $this->persistenceManager->persistAll();
            }
        } else {

            if ((int) $settings['recurrence_updated_behavior'] > 1) {
                //filter recurrences if only none modified events should be changed
                $filteredRecurrences = $this->filterRecurrences($news, $recurrences, (int) $settings['recurrence_updated_behavior']);
                $oldRecurrences = $filteredRecurrences[0];
                $newRecurrences = $filteredRecurrences[1];

                //build new recurring events
                if ((int) $settings['recurrence_updated_behavior'] < 4) {
                    //remove old (none modified) recurrences
                    foreach ($oldRecurrences as $oldRec) {
                        //todo remove hidden childobjects too
                        $news->removeNewsRecurrence($oldRec);
                        $this->newsRecurrenceRepository->remove($oldRec);
                    }
                    $this->persistenceManager->persistAll();

                    //add new recurrences
                    foreach ($newRecurrences as $rec) {
                        $newRecurrence = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Model\NewsRecurrence');
                        $newRecurrence = $this->copyFieldsFromEventToRecurrence($newRecurrence, $fieldArray, $news);
                        $newRecurrence->setEventstart($rec->getStart());
                        $newRecurrence->setEventend($rec->getEnd());
                        $newRecurrence->addParentEvent($news);
                        if ($news->getEarlyBirdDate() instanceof DateTime) {
                            $newRecurrence->setEarlyBirdDate($this->getEarlyBirdDateOfRecurrence($news, $rec->getStart()));
                        }
                        $this->newsRecurrenceRepository->add($newRecurrence);
                        $this->newsRepository->update($news);
                        $this->persistenceManager->persistAll(); // persist here, otherwise newRecurrence is lost when next loop starts
                    }
                }

                // change fields in recurring events
                if ((int) $settings['recurrence_updated_behavior'] > 3) {
                    //todo: request if subscribers to events chould be automatically informed about the changes, if yes create E-Mail
                    foreach ($oldRecurrences as $oldRec) {
                        $onlyChangedFields = $settings['recurrence_updated_behavior'] > 5 ? true : false;
                        $oldRec = $this->copyFieldsFromEventToRecurrence($oldRec, $fieldArray, $news, $onlyChangedFields);
                        $this->newsRecurrenceRepository->update($oldRec);
                    }
                    $this->persistenceManager->persistAll();
                }
            }
        }
    }

    /**
     * getEarlyBirdDateOfRecurrence
     * 
     * @param $event
     * @param $startdateRecurrence
     * @return mixed
     */
    public function getEarlyBirdDateOfRecurrence($event, $startdateRecurrence){

        $eventStart = clone $event->getEventstart();
        $birdDate = clone $event->getEarlyBirdDate();
        $eventStart->setTime(0,0,0);
        $birdDate->setTime(0,0,0);
        $newEarlyBirdDate = clone $startdateRecurrence;
        $diffDays = $birdDate->diff($eventStart)->format('%a');
        return $newEarlyBirdDate->sub(new \DateInterval('P'.$diffDays.'D'));
    }

    /**
     * getMethodNameFromString
     *
     * @param $string
     * @return mixed
     */
    public function getMethodNameFromString($string){
        return str_replace(' ', '',
            ucwords(
                str_replace('_', ' ', $string)
            )
        );
    }

    /**
     * isValidEventdates
     *
     * @param $fieldArray
     * @return bool
     */
    public function hasValidEventdates($fieldArray){
        if($fieldArray['eventstart'] === '' || $fieldArray['eventstart'] === '0'){
            $this->addFlashMessage(
                'Date Error',
                'Field Eventstart shouldn\'t be empty. No Recurrences changed.',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            return false;
        }
        if($fieldArray['eventend'] === '' || $fieldArray['eventend'] === '0'){
            $this->addFlashMessage(
                'Date Error',
                'Field Eventend shouldn\'t be empty. No Recurrences changed.',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            return false;
        }

        try {
            $eventstart = new \DateTime($fieldArray['eventstart']);
            $eventend = new \DateTime($fieldArray['eventend']);
        } catch (\Exception $exception) {
            $this->addFlashMessage(
                'Date Error',
                'Either Eventstart or Eventend could not be converted into DateTime Object. Error: ' . $exception->getMessage() . ' No Recurrence changed.',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            return false;
        }
        if($eventstart->diff($eventend)->format('%R') === '-'){
            $this->addFlashMessage(
                'Date Error',
                'Eventstart is set after Eventend. No Recurrences changed.',
                \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
            );
            return false;
        }
        return [$eventstart,$eventend];
    }

    /**
     * filterOutUnchangedFields
     *
     * @param $fieldArray
     * @param $news
     */
    public function filterOutUnchangedFields($fieldArray, $news){
        foreach($this->availableFields as $key => $name){
            $method = $this->getMethodNameFromString($name);
            $oldValue = $news->{'get' . $method}();
            $newValue = $fieldArray[$name];
            switch (gettype($oldValue)) {
                case 'string':
                    if(trim($oldValue) === trim($newValue)) {
                        unset($this->availableFields[$key]);
                    }
                    break;
                case 'integer':
                    if($oldValue === (int)$newValue) {
                        unset($this->availableFields[$key]);
                    }
                    break;
                case 'object':
                    if ($oldValue instanceof \DateTime && $oldValue->diff(new \DateTime($newValue))->format('%a') === '0') {
                        unset($this->availableFields[$key]);
                    } elseif (is_a($oldValue, 'TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage') || is_a($oldValue, 'TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage')){
                        $oldValue = $oldValue->toArray();
                        $oldValueUids = [];
                        foreach ($oldValue as $val) {
                            array_push($oldValueUids, $val->getUid());
                        }
                        sort($oldValueUids);
                        if(implode(',', $oldValueUids) === $newValue){
                            unset($this->availableFields[$key]);
                        }
                    }
                    break;
                case 'boolean':
                    if($oldValue === (bool)$newValue) {
                        unset($this->availableFields[$key]);
                    }
                    break;
            }
        }
    }

    /**
     * addFlashMessage
     *
     * @param $title
     * @param $body
     * @param $type
     */
    public function addFlashMessage($title, $body, $type){
        $this->messageService = $this->extbaseObjectManager->get(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
        $flashMessage = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessage::class,
            $body,
            $title,
            $type,
            true
        );
        $messageQueue = $this->messageService->getMessageQueueByIdentifier();
        $messageQueue->addMessage($flashMessage);
    }

    /**
     * copyFieldsFromEventToRecurrence
     *
     * @param $recurrence
     * @param $fieldArray
     * @param $news
     * @param bool $onlyChangedFields
     * @return mixed
     */
    public function copyFieldsFromEventToRecurrence($recurrence, $fieldArray, $news, $onlyChangedFields = false){

        $this->locationRepository = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\LocationRepository');
        $this->personRepository = $this->extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\PersonRepository');

        if($onlyChangedFields === true){
            $this->filterOutUnchangedFields($fieldArray, $news);
        }

        foreach($this->availableFields as $key => $name){
            $method = $this->getMethodNameFromString($name);
            $oldValue = $news->{'get' . $method}();
            switch (gettype($oldValue)) {
                case 'integer':
                    $recurrence->{'set'.$method}($fieldArray[$name]);
                    break;
                case 'string':
                    $recurrence->{'set'.$method}($fieldArray[$name]);
                    break;
                case 'object':
                    if ($oldValue instanceof \DateTime) {
                        $recurrence->{'set'.$method}(new \DateTime($fieldArray[$name]));
                    } elseif (is_a($oldValue, 'TYPO3\CMS\Extbase\Persistence\Generic\LazyObjectStorage') || is_a($oldValue, 'TYPO3\CMS\Extbase\Persistence\Generic\ObjectStorage')){
                        $recurrence->{'empty'.$method}();
                        foreach(explode(',', $fieldArray[$name]) as $uid) {
                            $object = $this->{substr($name, 0, -1) . 'Repository'}->findByIdentifier($uid);
                            if (NULL !== $object) {
                                $recurrence->{'add'.substr($method, 0, -1)}($object);
                            }

                        }
                    }
                    break;
                case 'boolean':
                    $recurrence->{'set'.$method}((bool)$fieldArray[$name]);
                    break;
            }
        }
        return $recurrence;
    }

    /**
     * hideAllRecurrences.
     *
     * @param $news
     *
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function hideAllRecurrences($news)
    {
        $recurrences = $news->getNewsRecurrence();
        $recurrences = $recurrences->toArray();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($recurrences, 'TCEmainHook:110');

        foreach ($recurrences as $key => $rec) {
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rec->isHidden(), $rec->getUid().' TCEmainHook:114');
            $rec->setHidden(true);
            $this->newsRecurrenceRepository->update($rec);
            $this->persistenceManager->persistAll();
        }
        $this->newsRepository->update($news);
        $this->persistenceManager->persistAll();
    }

    /**
     * filterRecurrences.
     *
     * @param $news
     * @param $recurrences
     * @param $updateBehavior
     *
     * @return array
     */
    public function filterRecurrences($news, $recurrences, $updateBehavior)
    {
        $oldRecurrences = $news->getNewsRecurrence();
        $oldRecurrences = $oldRecurrences->toArray();

        //filter modified recurrences
        if ($updateBehavior === 3 || $updateBehavior === 5 || $updateBehavior === 7) {
            foreach ($oldRecurrences as $key => $oldRec) {
                if ($oldRec->isModified()) {
                    unset($oldRecurrences[$key]);
                    $recurrences->removeElement($recurrences->startsBetween($oldRec->getEventstart(), $oldRec->getEventstart(), true)->getValues()[0]);
                }
            }
        }
        // filter recurrences with existing applications
        if ($updateBehavior > 2) {
            foreach ($oldRecurrences as $key => $oldRec) {
//                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump([$oldRec->getApplication(),$oldRec->getApplication()->count(),$this->applicationRepository->countApplicationsForNewsRecurrence($oldRec->getUid(),true)],'TCEmainHook:401');

                if ((int)$this->applicationRepository->countApplicationsForNewsRecurrence($oldRec->getUid(),true) > 0) {
                    unset($oldRecurrences[$key]);
                    $recurrences->removeElement($recurrences->startsBetween($oldRec->getEventstart(), $oldRec->getEventstart(), true)->getValues()[0]);
                }
            }
        }
        return [$oldRecurrences, $recurrences];
    }

    /**
     * startsWith.
     *
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * processDatamap_postProcessFieldArray.
     *
     * @param $status
     * @param $table
     * @param $id
     * @param array                                    $fieldArray
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
        if ($table === 'tx_news_domain_model_news' || $fieldArray['recurrence_updated_behavior'] === 1) {
            unset($fieldArray['recurrence_updated_behavior']);
        }
        if ($table === 'tx_datednews_domain_model_newsrecurrence' ) {
//            unset($fieldArray['disregard_changes_on_saving']);
        }
    }

    //other available but unused hooks
    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
    }
    public function processCmdmap_preProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {


    }
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {

    }
    public function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
    }
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj)
    {
    }
}
