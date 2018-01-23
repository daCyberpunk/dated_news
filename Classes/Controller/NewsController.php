<?php

namespace FalkRoeder\DatedNews\Controller;

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

use GeorgRinger\News\Utility\Cache;
use GeorgRinger\News\Utility\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;


/**
 * Class FalkRoeder\DatedNews\Controller\NewsController.
 */
class NewsController extends \GeorgRinger\News\Controller\NewsController
{
    const SIGNAL_NEWS_CALENDAR_ACTION = 'calendarAction';
    const SIGNAL_NEWS_CREATEAPPLICATION_ACTION = 'createApplicationAction';
    const SIGNAL_NEWS_CONFIRMAPPLICATION_ACTION = 'confirmApplicationAction';

    /**
     * applicationRepository.
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository = null;

    /**
     * @var \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository
     * @inject
     */
    protected $newsRecurrenceRepository = null;

    /**
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Utility\Div
     * @inject
     */
    protected $div;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function injectPageRenderer(\TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     */
    public function initializeCreateApplicationAction()
    {
        foreach ($this->arguments as $argument) {
            $argumentName = $argument->getName();
            if ($this->request->hasArgument($argumentName)) {
                if ($argumentName === 'newApplication' && $this->request->getArgument($argumentName) === '') {
                    $GLOBALS['TSFE']->pageNotFoundAndExit('No Application entry found.');
                } else {
                    $argument->setValue($this->request->getArgument($argumentName));
                }
            } else {
                if ($argumentName === 'newApplication') {
                    $GLOBALS['TSFE']->pageNotFoundAndExit('No Application entry found.');
                } else {
                    $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
                }
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     */
    public function initializeConfirmApplicationAction()
    {
        foreach ($this->arguments as $argument) {
            $argumentName = $argument->getName();
            if ($this->request->hasArgument($argumentName)) {
                $argument->setValue($this->request->getArgument($argumentName));
            } else {
                $GLOBALS['TSFE']->pageNotFoundAndExit('No Application entry found.');
            }
        };
    }

    /**
     * Initializes the current action
     *
     */
    public function initializeAction()
    {
        parent::initializeAction();
        $cObj =  $this->configurationManager->getContentObject();
        $pluginConfiguration = $this->div->getPluginConfiguration($cObj->data['uid']);
        $this->settings['switchableControllerActions'] = $pluginConfiguration['switchableControllerActions'];
    }

    /**
     * Calendar view.
     *
     * @param array $overwriteDemand
     *
     * @return void
     */
    public function calendarAction(array $overwriteDemand = null)
    {

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);
        if ($this->settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }
        $newsRecords = $this->newsRepository->findDemanded($demand);
        // Escaping quotes, doublequotes and backslashes for use in Javascript
        foreach ($newsRecords as $news) {
            $news->setTitle(addslashes($news->getTitle()));
            $news->setTeaser(addslashes($news->getTeaser()));
            $news->setDescription(addslashes($news->getDescription()));
            $news->setBodytext(addslashes($news->getBodytext()));
        }
        $this->addCalendarJSLibs($this->settings['dated_news']['includeJQuery'], $this->settings['dated_news']['jsFileCalendar'], $this->settings['qtips']);
        $this->addCalendarCss($this->settings['dated_news']['cssFile']);

        //collect news Uids for ajax request as we do not have the demandobject in our ajaxcall later
        $newsUids = '';
        foreach ($newsRecords as $news) {
            $newsUids .= ',' . $news->getUid();
        }

        $assignedValues = [
            'news'            => $newsRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand'          => $demand,
            'newsUids'        => $newsUids
        ];

        $filterValues = [];
        if ($this->settings['showCategoryFilter'] === '1') {
            $filterValues = array_merge($filterValues, $this->getCategoriesOfNews($newsRecords));
        }
        if ($this->settings['showTagFilter'] === '1') {
            $filterValues = array_merge($filterValues, $this->getTagsOfNews($newsRecords));
        }
        if (!empty($filterValues)) {
            if ($this->settings['sortingFilterlist'] === 'shuffle') {
                $assignedValues['filterValues'] = $this->div->shuffleAssoc($filterValues);
            } else {
                ksort($filterValues, SORT_LOCALE_STRING);
                $assignedValues['filterValues'] = $filterValues;
            }
        }

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CALENDAR_ACTION, $assignedValues);

        $this->view->assignMultiple($assignedValues);
        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * creates a single evcent array from given news / recurrence
     *
     *
     * @return array
     */
    public function createSingleEvent($news, $settings, $recurrence = null)
    {
        $start = $recurrence ? $recurrence->getEventstart() : $news->getEventstart();
        $end = $recurrence ?  $recurrence->getEventend() : $news->getEventend();
        $color = trim($news->getBackgroundcolor());
        $textcolor = trim($news->getTextcolor());
        $categories = $news->getCategories();

        if ($color === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getBackgroundcolor());
                if ($tempColor !== '') {
                    $color = $tempColor;
                }
            }
        }
        if ($textcolor === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getTextcolor());
                if ($tempColor !== '') {
                    $textcolor = $tempColor;
                }
            }
        }

        if (!$start instanceof \DateTime) {
            try {
                $start = new \DateTime($start);
            } catch (\Exception $exception) {
                throw new Exception('"'.$start.'" could not be parsed by DateTime constructor.', 1438925934);
            }
        }
        if (!$end instanceof \DateTime && $end !== null) {
            try {
                $end = new \DateTime($end);
            } catch (\Exception $exception) {
                throw new Exception('"'.$end.'" could not be parsed by DateTime constructor.', 1438925934);
            }
        }





        $uri = $this->getLinkToNewsItem($news, $settings);
        $qtip = ' \''.trim(preg_replace("/\r|\n/", '', $this->renderQtip($settings,$news,$recurrence))).'\'';
        $uid = $recurrence ? 'r' . $recurrence->getUid() : 'n' . $news->getUid();

        $tmpEvt = [
            "title" => $news->getTitle(),
            "id" => $uid,
            "end" => $end->format('Y-m-d H:i:s'),
            "start" => $start->format('Y-m-d H:i:s'),
            "url" => $uri,
            "allDay" => $news->getFulltime(),
            "className" => 'Event_' . $uid,
            "qtip" => $qtip,
            'color' => $color,
            'textColor' => $textcolor
        ];
        return $tmpEvt;
    }

    /**
     * creates list of tags from given news
     *
     *
     * @return string
     */
    public function getTagList($tagArray, $news, $recurrence = null )
    {

        $tags = $news->getTags()->toArray();
        $categories = $news->getCategories()->toArray();
        $tagsAndCats = array_merge($tags,$categories);
        $uid = $recurrence ? 'r' . $recurrence->getUid() : 'n' . $news->getUid();

        foreach ($tagsAndCats as $key => $value) {
            $tagTitle = $value->getTitle();
            if (array_key_exists($tagTitle, $tagArray)) {
                if(!in_array($uid, $tagArray[$tagTitle])){
                    array_push($tagArray[$tagTitle],$uid);
                }
            } else {
                $tagArray[$tagTitle] = [];
                array_push($tagArray[$tagTitle],$uid);
            }
        }

        return $tagArray;

    }

    /**
     * Get events via AJAX .
     *
     * @param array $overwriteDemand
     *
     * @return string
     */
    public function ajaxEventAction(array $overwriteDemand = null)
    {
        date_default_timezone_set('UTC');
        $calendarstart = \DateTime::createFromFormat('Y-m-d H:i:s', $this->request->getArgument('start') . '00:00:00');
        $calendarend = \DateTime::createFromFormat('Y-m-d H:i:s', $this->request->getArgument('end') . '00:00:00');

        //getPluginSettings of Calendar which requested the data
        $settings = $this->div->getPluginConfiguration($this->request->getArgument('cUid'));
        $settings = array_merge($this->settings, $settings['settings']);




//        $newsUids = explode(',',$this->request->getArgument('newsUids') );

        $demand = $this->createDemandObjectFromSettings($settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);
        if ($settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        $newsRecords = $this->newsRepository->findDemanded($demand);
        $result =[
            'events' => [],
            'tags' => []
        ];

        $newsRecords = $newsRecords->toArray();
        foreach ($newsRecords as $key => $news) {
            //newsRecords filter if not an event, has recurrences or showincalendar === False
            if (
                !$news->isEvent() ||
                $news->hasNewsRecurrences() ||
                !$news->isShowincalendar()
            ) {
                unset($newsRecords[$key]);
            } else {
                $newsStart = $news->getEventstart();
                $newsEnd = $news->getEventend();

                if(
                    $newsEnd < $calendarstart ||
                    $newsStart > $calendarend
                ){
                    unset($newsRecords[$key]);
                } else {
                    $result['tags'] = $this->getTagList($result['tags'],$news);
                    array_push($result['events'], $this->createSingleEvent($news, $settings));
                }
            }
        }

        $recurrences = $this->newsRecurrenceRepository->getBetweenDates([$calendarstart, $calendarend]);
        foreach ($recurrences as $key => $evt) {
            $parents = $evt->getParentEvent()->toArray();
            if( isset($parents[0]) ){
                $parent = $parents[0];
                if(
                    $parent->getHidden()||
                    !$parent->isShowincalendar()
                ){
                    unset($recurrences[$key]);
                } else {
                    $result['tags'] = $this->getTagList($result['tags'],$parent, $evt);
                    array_push($result['events'], $this->createSingleEvent($parent, $settings, $evt));
                }
            } else {
                unset($recurrences[$key]);
            }

        }
        
//        return $result;
        return json_encode($result);

    }


    /**
     * @param $settings
     * @param $newsItem
     *
     * @return string html output of Qtip.html
     */
    public function renderQtip($settings, $newsItem, $recurrence = null)
    {

        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $qtip = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $qtip->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials/Calendar/Qtip.html');
        /*$qtip->setLayoutRootPaths(array(
            'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Layouts'
        ));*/
        $qtip->setPartialRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials',
        ]);
        $assignedValues = [
            'newsItem' => $newsItem,
            'settings' => $settings,
        ];
        if(NULL !== $recurrence){
            $assignedValues['recurrence'] = $recurrence;
        }
        $qtip->assignMultiple($assignedValues);

        return $qtip->render();
    }

    /**
     * Single view of a news record.
     *
     * @param \GeorgRinger\News\Domain\Model\News            $news           news item
     * @param int                                            $currentPage    current page for optional pagination
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function eventDetailAction(\GeorgRinger\News\Domain\Model\News $news = null, $currentPage = 1, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication = null)
    {

        if (is_null($news)) {
            $previewNewsId = ((int) $this->settings['singleNews'] > 0) ? $this->settings['singleNews'] : 0;
            if ($this->request->hasArgument('news_preview')) {
                $previewNewsId = (int) $this->request->getArgument('news_preview');
            }

            if ($previewNewsId > 0) {
                if ($this->isPreviewOfHiddenRecordsEnabled()) {
                    $GLOBALS['TSFE']->showHiddenRecords = true;
                    $news = $this->newsRepository->findByUid($previewNewsId, false);
                } else {
                    $news = $this->newsRepository->findByUid($previewNewsId);
                }
            }
        }

        if (is_a($news,
                'GeorgRinger\\News\\Domain\\Model\\News') && $this->settings['detail']['checkPidOfNewsRecord']
        ) {
            $news = $this->checkPidOfNewsRecord($news);
        }

        if (is_null($news) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $this->addCalendarCss($this->settings['dated_news']['cssFile']);

        if($news->getRecurrence() > 0) {
            //todo add JS script to assignedvalues which switches the field where to choose how many places will be booked according to the choosen recurring event in application form
            

            $recurrences = $news->getNewsRecurrence()->toArray();
            $recurrenceOptions = [];
            $sumFreeSlots = 0;
            foreach ($recurrences as $recurrence) {
                // resrervable slot options for each recurrence
                $applicationsCount = $this->applicationRepository->countReservedSlotsForNewsRecurrence($recurrence->getUid());
                $freeSlots = (int) $recurrence->getSlots() - $applicationsCount;
                $recurrence->setSlotsFree($freeSlots);
                $sumFreeSlots = $sumFreeSlots + $freeSlots;
                $slotoptions = [];
                $i = 1;
                while ($i <= $recurrence->getSlotsFree()) {
                    $slotoption = new \stdClass();
                    $slotoption->key = $i;
                    $slotoption->value = $i;
                    $slotoptions[] = $slotoption;
                    $i++;
                }
                $recurrence->setSlotoptions($slotoptions);

                //options for reserable recurrences
                if($recurrence->getSlotsFree() > 0) {
                    $recurrenceOption = new \stdClass();
                    $recurrenceOption->key = $recurrence->getUid();
                    $recurrenceOption->value = $recurrence->getEventstart()->format($this->settings['dated_news']['emailSubjectDateFormat']);
                    $recurrenceOptions[] = $recurrenceOption;
                }
            }
            $news->setSlotsFree($sumFreeSlots);
        } else {
            $applicationsCount = $this->applicationRepository->countReservedSlotsForNews($news->getUid());
            $news->setSlotsFree((int) $news->getSlots() - $applicationsCount);
            $slotoptions = [];
            $i = 1;
            while ($i <= $news->getSlotsFree()) {
                $slotoption = new \stdClass();
                $slotoption->key = $i;
                $slotoption->value = $i;
                $slotoptions[] = $slotoption;
                $i++;
            }
        }

        $assignedValues = [
            'newsItem'       => $news,
            'currentPage'    => (int) $currentPage,
            'demand'         => $demand,
            'newApplication' => $newApplication,
            'formTimestamp' => time(), // for form reload and doubled submit prevention
        ];
        
        if(isset($slotoptions)){
            $assignedValues['slotoptions'] = $slotoptions;
        }
        if(isset($recurrenceOptions)){
            $assignedValues['recurrenceoptions'] = $recurrenceOptions;
        }

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_DETAIL_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);

        Page::setRegisterProperties($this->settings['detail']['registerProperties'], $news);
        if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
            Cache::addCacheTagsByNewsRecords([$news]);
        }
    }

    /**
     * action createApplication.
     *
     * @param \GeorgRinger\News\Domain\Model\News            $news           news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     *
     * @return void
     */
    public function createApplicationAction(\GeorgRinger\News\Domain\Model\News $news = null, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication = null)
    {
        
        if (is_null($news)) {
            $previewNewsId = ((int) $this->settings['singleNews'] > 0) ? $this->settings['singleNews'] : 0;
            if ($this->request->hasArgument('news_preview')) {
                $previewNewsId = (int) $this->request->getArgument('news_preview');
            }

            if ($previewNewsId > 0) {
                if ($this->isPreviewOfHiddenRecordsEnabled()) {
                    $GLOBALS['TSFE']->showHiddenRecords = true;
                    $news = $this->newsRepository->findByUid($previewNewsId, false);
                } else {
                    $news = $this->newsRepository->findByUid($previewNewsId);
                }
            }
        }

        if (is_a($news,
                'GeorgRinger\\News\\Domain\\Model\\News') && $this->settings['detail']['checkPidOfNewsRecord']
        ) {
            $news = $this->checkPidOfNewsRecord($news);
        }

        if (is_null($news) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }



        // prevents form submitted more than once
        $formTimestamp = $this->request->getArgument('newApplication')['formTimestamp'];
        if ($this->applicationRepository->isFirstFormSubmission($formTimestamp)) {
            $newApplication->setPid($news->getPid());
            $newApplication->setHidden(true);
            $newApplication->setSysLanguageUid($news->getSysLanguageUid());
            $newApplication->setFormTimestamp($formTimestamp);

            //set creationdate
            $date = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->setTime(date("H"), date("i"), date("s"));
            $newApplication->setCrdate($date->getTimestamp());


            if($news->getRecurrence() > 0 ) {
                //set total depending on either customer is an early bird or not and on earyBirdPrice is set
                $recurringEvent = $this->newsRecurrenceRepository->findByUid($this->request->getArgument('reservedRecurrence'));
                $reservedSlots = $this->request->getArgument('reservedSlots-' . $this->request->getArgument('reservedRecurrence'));
                $newApplication->setReservedSlots($reservedSlots);
                if ($news->getEarlyBirdPrice() != '' && $recurringEvent->getEarlyBirdDate() != '' && $recurringEvent->getEarlyBirdDate() != '0') {
                    $earlybirdDate = clone $recurringEvent->getEarlyBirdDate();
                    $earlybirdDate->setTime(0, 0, 0);

                    $today = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
                    $today->setTime(0, 0, 0);

                    if ($earlybirdDate >= $today) {
                        $newApplication->setCosts((int)$reservedSlots * floatval(str_replace(',', '.', $news->getEarlyBirdPrice())));
                    } else {
                        $newApplication->setCosts((int)$reservedSlots * floatval(str_replace(',', '.', $news->getPrice())));
                    }
                } else {
                    $newApplication->setCosts((int)$reservedSlots * floatval(str_replace(',', '.', $news->getPrice())));
                }
                $newApplication->addRecurringevent($recurringEvent);
            } else {
                //set total depending on either customer is an early bird or not and on earyBirdPrice is set
                if ($news->getEarlyBirdPrice() != '' && $news->getEarlyBirdDate() != '' && $news->getEarlyBirdDate() != '0') {
                    $earlybirdDate = clone $news->getEarlyBirdDate();
                    $earlybirdDate->setTime(0, 0, 0);

                    $today = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
                    $today->setTime(0, 0, 0);

                    if ($earlybirdDate >= $today) {
                        $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',', '.', $news->getEarlyBirdPrice())));
                    } else {
                        $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',', '.', $news->getPrice())));
                    }
                } else {
                    $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',', '.', $news->getPrice())));
                }
                $newApplication->addEvent($news);
            }


            $this->applicationRepository->add($newApplication);

            $persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
            $persistenceManager->persistAll();
            $newApplication->setApplicationTitle($news->getTitle().' - '.$newApplication->getName().' '.$newApplication->getSurname().'-'.$newApplication->getUid());
            $this->applicationRepository->update($newApplication);
            $persistenceManager->persistAll();

            $demand = $this->createDemandObjectFromSettings($this->settings);
            $demand->setActionAndClass(__METHOD__, __CLASS__);

            $assignedValues = [
                'newsItem'       => $news,
                'demand'         => $demand,
                'newApplication' => $newApplication,
                'settings'       => $this->settings,
            ];

            if($news->getRecurrence() > 0 ) {
                $assignedValues['recurrence'] = $recurringEvent;
            }

            $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CREATEAPPLICATION_ACTION, $assignedValues);
            $this->view->assignMultiple($assignedValues);

            $this->sendMail($news, $newApplication, $this->settings, $recurringEvent);
        } else {
            $this->flashMessageService('applicationSendMessageAllreadySent', 'applicationSendMessageAllreadySentStatus', 'ERROR');
        }
    }

    /**
     * action confirmApplication.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     *
     * @return void
     */
    public function confirmApplicationAction(\FalkRoeder\DatedNews\Domain\Model\Application $newApplication)
    {
        $assignedValues = [];
        if (is_null($newApplication)) {
            $this->flashMessageService('applicationNotFound', 'applicationNotFoundStatus', 'ERROR');
        } else {
            //vor confirmation link validity check
            $date = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->setTime(date("H"), date("i"), date("s"));
            $hoursSinceBookingRequestSent = ($date->getTimestamp() - $newApplication->getCrdate()) / 3600;

            if ($newApplication->isConfirmed() === true) {
                //was allready confirmed
                $this->flashMessageService('applicationAllreadyConfirmed', 'applicationAllreadyConfirmedStatus', 'INFO');
            } elseif ($this->settings['dated_news']['validDaysConfirmationLink'] * 24 < $hoursSinceBookingRequestSent) {
                //confirmation link not valid anymore
                $this->flashMessageService('applicationConfirmationLinkUnvalid', 'applicationConfirmationLinkUnvalidStatus', 'ERROR');
            } else {
                //confirm
                $newApplication->setConfirmed(true);
                $newApplication->setHidden(false);
                $this->applicationRepository->update($newApplication);

                $events = $newApplication->getEvents();
                if($events->count() === 0) {
                    $events = $newApplication->getRecurringevents();
                    $events->rewind();
                    $event = $events->current();
                    $news = $event->getParentEvent();
                    $news->rewind();
                    $news = $news->current();
                } else {
                    $events->rewind();
                    $news = $events->current();
                }

                $persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
                $persistenceManager->persistAll();

                $this->sendMail($news, $newApplication, $this->settings, $event, true);
                $assignedValues = [
                    'newApplication' => $newApplication,
                    'newsItem'       => $news,
                ];
                if($news->getRecurrence() > 0 && !is_null($event)) {
                    $assignedValues['recurrence'] = $event;
                }
            }
        }
        if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
            GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_pages')->flushByTag('tx_news_uid_'.$news->getUid());
        }

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CONFIRMAPPLICATION_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);
    }

    /**
     * reloads Details of news via ajax.
     *
     * @return string
     */
    public function reloadFieldsAction()
    {
        if ($this->request->hasArgument('requestItems')) {
            $requestItems = json_decode($this->request->getArgument('requestItems'));

            $resultArray = [];
            foreach ($requestItems as $uid => $fields) {
                $resultArray[$uid] = [];
                $item = $this->newsRepository->findByUid($uid);

                foreach ($fields as $field) {
                    $func = 'get'.ucfirst(trim($field));
                    if (method_exists($item, $func) === true) {
                        if ($field === 'slotsFree') {
                            $resultArray[$uid][$field] = ((int) $item->getSlots() - $this->applicationRepository->countReservedSlotsForNews($item->getUid()));
                        } else {
                            $resultArray[$uid][$field] = htmlentities($item->{$func}());
                        }
                    }
                }
            }

            return json_encode($resultArray);
        }

        return false;
    }

    /**
     * sendMail to applyer, admins
     * and authors and the ICS invitation
     * if booking is confirmed.
     *
     * @param \GeorgRinger\News\Domain\Model\News            $news           news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @param $settings
     * @param bool $confirmation
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function sendMail(\GeorgRinger\News\Domain\Model\News $news = null, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication, $settings, $recurringEvent = null, $confirmation = false)
    {

        // from
        $sender = [];
        if (!empty($this->settings['senderMail'])) {
            $sender = ([$this->settings['senderMail'] => $this->settings['senderName']]);
        }

        //validate Mailadress of applyer
        $applyerMail = $newApplication->getEmail();

        $applyer = [];
        if (is_string($applyerMail) && GeneralUtility::validEmail($applyerMail)) {
            $applyer = [
                $newApplication->getEmail() => $newApplication->getName().', '.$newApplication->getSurname(),
            ];
        } else {
            $this->flashMessageService('applicationSendMessageNoApplyerEmail', 'applicationSendMessageNoApplyerEmailStatus', 'ERROR');
            $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
        }

        //get filenames of flexform files to send to applyer
        if ($confirmation === false) {
            $cObj = $this->configurationManager->getContentObject();
            $uid = $cObj->data['uid'];
            $fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
            $fileObjects = $fileRepository->findByRelation('tt_content', 'tx_datednews', $uid);
            $filenames = [];
            if (is_array($fileObjects)) {
                foreach ($fileObjects as $file) {
                    $filenames[] = $file->getOriginalFile()->getIdentifier();
                }
            }

            //FAL files does not work with gridelements, so add possibility to add file paths to TS. see https://forge.typo3.org/issues/71436
            $filesFormTS = explode(',', $this->settings['dated_news']['filesForMailToApplyer']);
            foreach ($filesFormTS as $fileName) {
                $filenames[] = trim($fileName);
            }
        } else {
            $filenames = [];
        }

        $recipientsCc = [];
        $recipientsBcc = [];

        $subjectFields = explode(',', $this->settings['dated_news']['emailSubjectFields']);

        if($news->getRecurrence() > 0) {
            $events = $newApplication->getRecurringevents();
            $events->rewind();
            $event = $events->current();
            $eventstart = $event->getEventstart();
            $eventend = $event->getEventend();
            $newsLocation = $news->getLocations();
        } else {
            $eventstart = $news->getEventstart();
            $eventend = $news->getEventend();
            $newsLocation = $news->getLocations();
        }

        $subject = '';
        $fieldIterator = 0;
        foreach ($subjectFields as $field) {
            switch (trim($field)) {
                case 'title':
                    if ($fieldIterator > 0) {
                        $subject .= ', ';
                    }
                    $subject .= $news->getTitle();
                    break;
                case 'eventstart':
                    $subject .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject_eventstart', 'dated_news');
                    $subject .= $eventstart->format($this->settings['dated_news']['emailSubjectDateFormat']);
                    break;
                case 'locationname':
                    $locationIterator = 0;
                    if (isset($newsLocation)) {
                        $subject .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject_locationname', 'dated_news');
                        foreach ($newsLocation as $location) {
                            $locationIterator++;
                            if ($locationIterator === 1) {
                                $subject .= $location->getName();
                            } else {
                                $subject .= ', '.$location->getName();
                            }
                        }
                    }
                    break;
            }
        }

        // send email Customer
        $customerMailTemplate = $confirmation === true ? 'MailConfirmationApplyer' : 'MailApplicationApplyer';
        if (!$this->div->sendEmail(
            $customerMailTemplate,
            $applyer,
            $recipientsCc,
            $recipientsBcc,
            $sender,
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', ['subject' => $subject]),
            ['newApplication' => $newApplication, 'news' => $news, 'settings' => $settings],
            $filenames
        )) {
            $this->flashMessageService('applicationSendMessageApplyerError', 'applicationSendStatusApplyerErrorStatus', 'ERROR');
        } else {
            if ($confirmation === false) {
                $this->flashMessageService('applicationSendMessage', 'applicationSendStatus', 'OK');
            }
        }

        //Send to admins etc only when booking / application confirmed
        if ($confirmation === true) {
            /** @var $to array Array to collect all the receipients */
            $to = [];

            //news Author
            if ($this->settings['notificateAuthor']) {
                $authorEmail = $news->getAuthorEmail();
                if (!empty($authorEmail)) {
                    $to[] = [
                        'email' => $authorEmail,
                        'name'  => $news->getAuthor(),
                    ];
                }
            }

            //Plugins notification mail addresses
            if (!empty($this->settings['notificationMail'])) {
                $tsmails = explode(',', $this->settings['notificationMail']);
                foreach ($tsmails as $tsmail) {
                    $to[] = [
                        'email' => trim($tsmail),
                        'name'  => '',
                    ];
                }
            }

            $recipients = [];
            if (is_array($to)) {
                foreach ($to as $pair) {
                    if (GeneralUtility::validEmail($pair['email'])) {
                        if (trim($pair['name'])) {
                            $recipients[$pair['email']] = $pair['name'];
                        } else {
                            $recipients[] = $pair['email'];
                        }
                    }
                }
            }

            if (!count($recipients)) {
                $this->flashMessageService('applicationSendMessageNoRecipients', 'applicationSendMessageNoRecipientsStatus', 'ERROR');
                $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
            }

            // send email to authors and Plugins mail addresses
            if ($this->div->sendEmail(
                'MailApplicationNotification',
                $recipients,
                $recipientsCc,
                $recipientsBcc,
                $sender,
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', ['subject' => $subject]),
                ['newApplication' => $newApplication, 'news' => $news, 'settings' => $this->settings],
                []
            )) {
                $this->flashMessageService('applicationConfirmed', 'applicationConfirmedStatus', 'OK');
            } else {
                $this->flashMessageService('applicationSendMessageGeneralError', 'applicationSendStatusGeneralErrorStatus', 'ERROR');
            }

            if ($this->settings['ics']) {
                //create ICS File and send invitation
                $newsTitle = $news->getTitle();
                $icsLocation = '';
                $i = 0;
                if (isset($newsLocation) && count($newsLocation) < 2) {
                    foreach ($newsLocation as $location) {
                        $icsLocation .= $location->getName().', '.$location->getAddress().', '.$location->getZip().' '.$location->getCity().', '.$location->getCountry();
                    }
                } else {
                    foreach ($newsLocation as $location) {
                        $i++;
                        if ($i === 1) {
                            $icsLocation .= $location->getName();
                        } else {
                            $icsLocation .= ', '.$location->getName();
                        }
                    }
                }

                $properties = [
                    'dtstart'   => $eventstart->getTimestamp(),
                    'dtend'     => $eventend->getTimestamp(),
                    'location'  => $icsLocation,
                    'summary'   => $newsTitle,
                    'organizer' => $this->settings['senderMail'],
                    'attendee'  => $applyerMail,

                ];

                //add description
                $description = $this->getIcsDescription($news, $event, $settings);
                if ($description !== false) {
                    $properties['description'] = $description;
                }

                $ics = new \FalkRoeder\DatedNews\Services\ICS($properties);
                $icsAttachment = [
                    'content' => $ics->to_string(),
                    'name'    => str_replace(' ', '_', $newsTitle),

                ];
                $senderMail = $this->settings['senderMail'];
                if (!$this->div->sendIcsInvitation(
                    'MailConfirmationApplyer',
                    $applyer,
                    $recipientsCc,
                    $recipientsBcc,
                    [$senderMail => $this->settings['senderName']],
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.invitation_subject', 'dated_news', ['subject' => $subject]),
                    ['newApplication' => $newApplication, 'news' => $news, 'settings' => $settings],
                    $icsAttachment,
                    [substr_replace($this->settings['senderMail'], 'noreply', 0, strpos($this->settings['senderMail'], '@')) => $this->settings['senderName']]
                )) {
                    $this->flashMessageService('applicationSendMessageApplyerError', 'applicationSendStatusApplyerErrorStatus', 'ERROR');
                } else {
                    if ($confirmation === false) {
                        $this->flashMessageService('applicationSendMessage', 'applicationSendStatus', 'OK');
                    }
                }
            }
        }
    }

    /**
     * adds calendar and event detail specific default css.
     *
     * @param string $pathToCss
     */
    public function addCalendarCss($pathToCss = '')
    {
        $this->pageRenderer->addCssFile('/typo3conf/ext/dated_news/Resources/Public/Plugins/fullcalendar/fullcalendar.min.css');
        $this->pageRenderer->addCssFile('/typo3conf/ext/dated_news/Resources/Public/Plugins/qtip3/jquery.qtip.min.css');
        $pathToCss = str_replace('EXT:', '/typo3conf/ext/', $pathToCss);
        $this->pageRenderer->addCssFile($pathToCss);
    }

    /**
     * adds calendar specific default js
     * and if in typoscript settings set to true, also jQuery.
     *
     * @param string $jquery
     * @param string $pathToJS
     */
    public function addCalendarJSLibs($jquery = '0', $pathToJS = '')
    {
        $libs = [
            'jQuery'        => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
            'xmoment'       => 'fullcalendar/lib/moment.min.js',
            'xfullcalendar' => 'fullcalendar/fullcalendar.min.js',
            'xlangall'      => 'fullcalendar/lang-all.js',
            'xqtip'         => 'qtip3/jquery.qtip.min.js',
            'dated_news'    => str_replace('EXT:', '/typo3conf/ext/', $pathToJS),
        ];
        $extPluginPath = 'typo3conf/ext/dated_news/Resources/Public/Plugins/';
        define('NEW_LINE', "\n");
        $contents = [];
        foreach ($libs as $name => $path) {
            if ($name == 'jQuery' && $jquery != '1') {
                continue;
            }
            if ($name !== 'jQuery' && $name != 'dated_news') {
                $path = $extPluginPath.$path;
            }
            if ($name !== 'jQuery') {
                $contents[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($path);
                continue;
            }
            /*jQuery*/
            $this->pageRenderer->addJsFooterLibrary(
                $name,
                $path,
                'text/javascript',
                true
            );
        }

        //other libs
        $file = 'typo3temp/assets/datednews/dated_news_calendar.js';
        if (!file_exists(PATH_site.$file)) {
            // writeFileToTypo3tempDir() returns NULL on success (please double-read!)
            $error = GeneralUtility::writeFileToTypo3tempDir(PATH_site.$file, implode($contents, NEW_LINE));
            if ($error !== null) {
                throw new \RuntimeException('Dated News JavaScript file could not be written to '.$file.'. Reason: '.$error, 1487439381339);
            }
        }

        $this->pageRenderer->addJsFooterLibrary(
            'dated_news',
            $file,
            'text/javascript',
            true
        );
    }

    /**
     * adds needed flashmessages
     * for informations to user.
     *
     * @param \string $messageKey
     * @param \string $statusKey
     * @param \string $level
     *
     * @return void
     */
    public function flashMessageService($messageKey, $statusKey, $level)
    {
        switch ($level) {
            case 'NOTICE':
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::NOTICE;
                break;
            case 'INFO':
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO;
                break;
            case 'OK':
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK;
                break;
            case 'WARNING':
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING;
                break;
            case 'ERROR':
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;
                break;
        }

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($messageKey, 'dated_news'),
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($statusKey, 'dated_news'),
            $level,
            true
        );
    }

    /**
     * takes categories of all
     * news records shown in calendar and put them into a array
     * also adds the colors which might be specified in
     * category to the array to enable filtering of calendar items
     * with colored buttons by category.
     *
     * @param $newsRecords
     *
     * @return array
     */
    public function getCategoriesOfNews($newsRecords)
    {
        $newsCategories = [];
        foreach ($newsRecords as $news) {
            if ($news->isShowincalendar() === true) {
                $categories = $news->getCategories();
                
                foreach ($categories as $category) {
                    $title = $category->getTitle();
                    $bgColor = $category->getBackgroundcolor();
                    $textColor = $category->getTextcolor();
                    if (!array_key_exists($title, $newsCategories)) {
                        $newsCategories[$title] = [];
                        $newsCategories[$title]['count'] = 1;
                        if (trim($bgColor) !== '') {
                            $newsCategories[$title]['bgcolor'] = $bgColor;
                            $newsCategories[$title]['textcolor'] = $textColor;
                        }
                    } else {
                        $newsCategories[$title]['count'] = $newsCategories[$title]['count'] + 1;
                    }
                }
            }
        }

        return $newsCategories;
    }

    /**
     * takes tags of all
     * news records shown in calendar
     * and put them into a array to enable filtering of calendar items by tag.
     *
     * @param $newsRecords
     *
     * @return array
     */
    public function getTagsOfNews($newsRecords)
    {
        $newsTags = [];
        foreach ($newsRecords as $news) {
            if ($news->isShowincalendar() === true) {
                $tags = $news->getTags();
                foreach ($tags as $tag) {
                    $title = $tag->getTitle();
                    if (!array_key_exists($title, $newsTags)) {
                        $newsTags[$title] = [];
                        $newsTags[$title]['count'] = 1;
                    } else {
                        $newsTags[$title]['count'] = $newsTags[$title]['count'] + 1;
                    }
                }
            }
        }

        return $newsTags;
    }

    /**
     * getIcsDescription.
     *
     * creates the ICS description for the
     * invitation send to Customer
     *
     * @param \GeorgRinger\News\Domain\Model\News $news     news item
     * @param array                               $settings
     *
     * @return bool|string
     */
    public function getIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $event = null, $settings)
    {
        switch ($settings['icsDescriptionField']) {
            case 'Teaser':
                if($news->getRecurrence() > 0 && !is_null($event)) {
                    $teaser = $event->getTeaser();
                } else {
                    $teaser = $news->getTeaser();
                }
                if ($teaser == strip_tags($teaser)) {
                    return $teaser;
                } else {
                    return false;
                }
                break;
            case 'Description':
                if ($news->getDescription() == strip_tags($news->getDescription())) {
                    return $news->getDescription();
                } else {
                    return false;
                }
                break;
            case 'Url':
                $uri = $this->getLinkToNewsItem($news, $settings);

                return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', ['url' => $uri]);

//                if($settings['detailPid']){
//                    $uriBuilder = $this->controllerContext->getUriBuilder();
//                    $uri = $uriBuilder
//                        ->reset()
//                        ->setTargetPageUid($settings['detailPid'])
//                        ->setUseCacheHash(TRUE)
//                        ->setArguments(array('tx_news_pi1' => array('controller' => 'News', 'action' => 'detail', 'news' => $news->getUid())))
//                        ->setCreateAbsoluteUri(TRUE)
//                        ->buildFrontendUri();
//                    return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', array('url' => $uri));
//                } else {
//                    return FALSE;
//                }
                break;
            case 'Custom':
                if (trim($settings['icsDescriptionCustomField']) === '') {
                    return false;
                } else {
                    if($news->getRecurrence() > 0 && !is_null($event)) {
                        $object = $event;
                    } else {
                        $object = $news;
                    }
                    $func = 'get'.ucfirst(trim($settings['icsDescriptionCustomField']));
                    if (method_exists($object, $func) === true) {
                        $description = $object->{$func}();
                        if (trim($description) != '' && $description != strip_tags($description)) {
                            return false;
                        } else {
                            return $description;
                        }
                    } else {
                        return false;
                    }
                }
                break;
            default:
                return false;
        }
    }

    /*
     * following stuff is almost full copy of tx_news LinkViewhelper
     * better solution needs to be found(?)
     * maybe put it in an Utility Class and inject it in own LinkViewhelper?
     * or inject LinkViewhelper directly here in Controller?
     * */

    /**
     * @var array
     */
    protected $detailPidDeterminationCallbacks = [
        'flexform'   => 'getDetailPidFromFlexform',
        'categories' => 'getDetailPidFromCategories',
        'default'    => 'getDetailPidFromDefaultDetailPid',
    ];

    /** @var $cObj ContentObjectRenderer */
    protected $cObj;

    /**
     * Gets detailPid from categories of the given news item. First will be return.
     *
     * @param array $settings
     * @param News  $newsItem
     *
     * @return int
     */
    protected function getDetailPidFromCategories($settings, $newsItem)
    {
        $detailPid = 0;
        if ($newsItem->getCategories()) {
            foreach ($newsItem->getCategories() as $category) {
                if ($detailPid = (int) $category->getSinglePid()) {
                    break;
                }
            }
        }

        return $detailPid;
    }

    /**
     * Gets detailPid from defaultDetailPid setting.
     *
     * @param array $settings
     * @param News  $newsItem
     *
     * @return int
     */
    protected function getDetailPidFromDefaultDetailPid($settings, $newsItem)
    {
        return (int) $settings['defaultDetailPid'];

    }

    /**
     * Gets detailPid from flexform of current plugin.
     *
     * @param array $settings
     * @param News  $newsItem
     *
     * @return int
     */
    protected function getDetailPidFromFlexform($settings, $newsItem)
    {
        return (int) $settings['detailPid'];
    }

    /**
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     *
     * @return int
     */
    protected function getNewsId(\GeorgRinger\News\Domain\Model\News $newsItem)
    {
        $uid = $newsItem->getUid();
        // If a user is logged in and not in live workspace
        if ($GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->workspace > 0) {
            $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getLiveVersionOfRecord('tx_news_domain_model_news',
                $newsItem->getUid());
            if ($record['uid']) {
                $uid = $record['uid'];
            }
        }

        return $uid;
    }

    /**
     * Generate the link configuration for the link to the news item.
     *
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     * @param array                               $tsSettings
     * @param array                               $configuration
     *
     * @return array
     */
    protected function getLinkToNewsItem(
        \GeorgRinger\News\Domain\Model\News $newsItem,
        $tsSettings,
        array $configuration = []
    ) {



        if (!isset($configuration['parameter'])) {
            $detailPid = 0;
            $detailPidDeterminationMethods = GeneralUtility::trimExplode(',', $tsSettings['detailPidDetermination'],
                true);

            // if TS is not set, prefer flexform setting
            if (!isset($tsSettings['detailPidDetermination'])) {
                $detailPidDeterminationMethods[] = 'flexform';
            }

            foreach ($detailPidDeterminationMethods as $determinationMethod) {
                if ($callback = $this->detailPidDeterminationCallbacks[$determinationMethod]) {
                    if ($detailPid = call_user_func([$this, $callback], $tsSettings, $newsItem)) {
                        break;
                    }
                }
            }

            if (!$detailPid) {
                $detailPid = $GLOBALS['TSFE']->id;
            }
            $configuration['parameter'] = $detailPid;
        }

        $configuration['forceAbsoluteUrl'] = true;

        $configuration['useCacheHash'] = $GLOBALS['TSFE']->sys_page->versioningPreview ? 0 : 1;
        $configuration['additionalParams'] .= '&tx_news_pi1[news]='.$this->getNewsId($newsItem);

        // action is set to "detail" in original Viewhelper, but we overwiritten this action
        if ((int) $tsSettings['link']['skipControllerAndAction'] !== 1) {
            $configuration['additionalParams'] .= '&tx_news_pi1[controller]=News'.
                '&tx_news_pi1[action]=eventDetail';
        }

        // Add date as human readable
        if ($tsSettings['link']['hrDate'] == 1 || $tsSettings['link']['hrDate']['_typoScriptNodeValue'] == 1) {
            $dateTime = $newsItem->getDatetime();

            if (!empty($tsSettings['link']['hrDate']['day'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[day]='.$dateTime->format($tsSettings['link']['hrDate']['day']);
            }
            if (!empty($tsSettings['link']['hrDate']['month'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[month]='.$dateTime->format($tsSettings['link']['hrDate']['month']);
            }
            if (!empty($tsSettings['link']['hrDate']['year'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[year]='.$dateTime->format($tsSettings['link']['hrDate']['year']);
            }
        }
        $this->cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        $url = $this->cObj->typoLink_URL($configuration);

        return $url;
    }
}
