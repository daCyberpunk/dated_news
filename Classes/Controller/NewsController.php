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

use FalkRoeder\DatedNews\Service\CalendarService;
use FalkRoeder\DatedNews\Utility\CacheUtility;
use GeorgRinger\News\Utility\Cache;
use GeorgRinger\News\Utility\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     * @var \FalkRoeder\DatedNews\Service\FeuserService
     */
    protected $feuserService;

    /**
     * @var \FalkRoeder\DatedNews\Domain\Repository\FeuserRepository
     * @inject
     */
    protected $feuserRepository;

    /**
     * @var \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository
     * @inject
     */
    protected $newsRecurrenceRepository = null;

    /**
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Utility\MiscUtility
     * @inject
     */
    protected $misc;

    /**
     * MailService
     *
     * @var \FalkRoeder\DatedNews\Service\MailService
     * @inject
     */
    protected $mailService;
    
    /**
     * PluginService
     *
     * @var \FalkRoeder\DatedNews\Service\PluginService
     * @inject
     */
    protected $pluginService;

    /**
     * EventService
     *
     * @var \FalkRoeder\DatedNews\Service\EventService
     * @inject
     */
    protected $eventService;

    /**
     * CalendarService
     *
     * @var \FalkRoeder\DatedNews\Service\CalendarService
     * @inject
     */
    protected $calendarService;

    /**
     * ApplicationService
     *
     * @var \FalkRoeder\DatedNews\Service\ApplicationService
     * @inject
     */
    protected $applicationService;

    /**
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Service\LinkToNewsItem
     * @inject
     */
    protected $linkToNewsItem;

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
        }
    }

    /**
     * Initializes the current action
     *
     */
    public function initializeAction()
    {
        parent::initializeAction();
        //todo check if $this->settings['switchableControllerActions']  really is needed
        $cObj =  $this->configurationManager->getContentObject();
        if ($cObj && isset($cObj->data['uid'])) {
            $pluginConfiguration = $this->pluginService->getPluginConfiguration($cObj->data['uid']);
            $this->settings['switchableControllerActions'] = $pluginConfiguration['switchableControllerActions'];
        }

        $this->feuserService = $this->objectManager->get('FalkRoeder\DatedNews\Service\FeuserService');
        $this->mailService = $this->objectManager->get('FalkRoeder\DatedNews\Service\MailService');
        $this->pluginService = $this->objectManager->get('FalkRoeder\DatedNews\Service\PluginService');
        $this->eventService = $this->objectManager->get('FalkRoeder\DatedNews\Service\EventService');
        $this->calendarService = $this->objectManager->get('FalkRoeder\DatedNews\Service\CalendarService');
        $this->applicationService = $this->objectManager->get('FalkRoeder\DatedNews\Service\ApplicationService');
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
        $this->addCalendarJSLibs($this->settings['dated_news']['includeJQuery'], $this->settings['dated_news']['jsFiles']);
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

        $filterValues = $this->calendarService->getCalendarFilterValues(
            $newsRecords, 
            (bool)$this->settings['showCategoryFilter'], 
            (bool)$this->settings['showTagFilter'],
            $this->settings['sortingFilterlist']
        );
       
        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CALENDAR_ACTION, $assignedValues);

        $this->view->assignMultiple($assignedValues);
        Cache::addPageCacheTagsByDemandObject($demand);
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
     * @param array $libs
     */
    public function addCalendarJSLibs($jquery = '0', $libs = [])
    {
        define('NEW_LINE', "\n");
        $contents = [];
        $fileNames = [
            'xmoment',
            'xfullcalendar',
            'xlang',
            'xqtip',
            'dated_news',
        ];

        /*jQuery*/
        if ($jquery == '1') {
            $this->pageRenderer->addJsFooterLibrary(
                'jquery',
                $libs['jQuery'],
                'text/javascript',
                true
            );
        }

        //other libs
        $file = 'typo3temp/assets/datednews/dated_news_calendar.js';
        if (!file_exists(PATH_site . $file)) {
            foreach ($fileNames as $name) {
                if (!file_exists($libs[$name])) {
                    throw new \InvalidArgumentException('File ' . $libs[$name] . ' not found. (TypoScript settings path: plugins.tx_news.dated_news.jsFiles.' . $name . ')', 1517546715990);
                } else {
                    $contents[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($libs[$name]);
                }
            }

            // writeFileToTypo3tempDir() returns NULL on success (please double-read!)
            $error = GeneralUtility::writeFileToTypo3tempDir(PATH_site . $file, implode($contents, NEW_LINE));
            if ($error !== null) {
                throw new \InvalidArgumentException('Dated News JavaScript file could not be written to ' . $file . '. Reason: ' . $error, 1487439381339);
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
     * Get events via AJAX .
     * when calendarview changes, f.e. user clicks to next month, 
     * the events will be called here
     *
     * @param array $overwriteDemand
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function ajaxEventAction(array $overwriteDemand = null)
    {
        date_default_timezone_set('UTC');
        $calendarstart = \DateTime::createFromFormat('Y-m-d H:i:s', $this->request->getArgument('start') . '00:00:00');
        $calendarend = \DateTime::createFromFormat('Y-m-d H:i:s', $this->request->getArgument('end') . '00:00:00');

        //getPluginSettings of Calendar which requested the data
        $settings = $this->pluginService->getPluginConfiguration($this->request->getArgument('cUid'));
        $settings = array_merge($this->settings, $settings['settings']);

        $demand = $this->createDemandObjectFromSettings($settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);
        if ($settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        $newsRecords = $this->newsRepository->findDemanded($demand);
        $eventsArray =[
            'events' => [],
            'tags' => []
        ];

        $eventsArray = $this->eventService->getSingleEventsFromNewsRecords(
            $newsRecords->toArray(),
            $eventsArray,
            $calendarstart,
            $calendarend,
            $settings
        );

        return json_encode(
            $this->eventService->getRecurrences(
                $eventsArray,
                $calendarstart,
                $calendarend,
                $settings
            )
        );
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
        $news = $this->getNewsOrPreviewNews($news);

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $this->addCalendarCss($this->settings['dated_news']['cssFile']);

        if ($news->getRecurrence() > 0) {
            $recurrences = $news->getNewsRecurrence()->toArray();
            $recurrenceOptions = [];
            foreach ($recurrences as $recurrence) {
                //options for reservable recurrences
                if ($recurrence->getSlotsFree() > 0) {
                    $recurrenceOption = new \stdClass();
                    $recurrenceOption->key = $recurrence->getUid();
                    $recurrenceOption->value = $recurrence->getEventstart()->format($this->settings['dated_news']['emailSubjectDateFormat']);
                    $recurrenceOptions[] = $recurrenceOption;
                }
            }
        } else {
            $slotoptions = $news->getSlotoptions();
        }

        $assignedValues = [
            'newsItem'       => $news,
            'currentPage'    => (int) $currentPage,
            'demand'         => $demand,
            'newApplication' => $newApplication,
            'formTimestamp' => time(), // for form reload and doubled submit prevention
            'feuserLoggedIn' => $this->feuserService->hasLoggedInFrontendUser()
        ];

        if (isset($slotoptions)) {
            $assignedValues['slotoptions'] = $slotoptions;
        }
        if (isset($recurrenceOptions)) {
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
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function createApplicationAction(\GeorgRinger\News\Domain\Model\News $news = null, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication = null)
    {
        $news = $this->getNewsOrPreviewNews($news);

        // prevents form submitted more than once
        $formTimestamp = $this->request->getArgument('newApplication')['formTimestamp'];
        if (!$this->applicationRepository->isFirstFormSubmission($formTimestamp)) {
            $this->flashMessage('applicationSendMessageAllreadySent', 'applicationSendMessageAllreadySentStatus', 'ERROR');
        } else {
            // gets loggedinuser || user with same mail address || create user
            // returns $feuserData['user'] = false on error
            $feuserData = $this->feuserService->getFeuser(
                $newApplication,
                $this->settings['dated_news']['autoAddFeuserAddToGroups'],
                $this->settings['dated_news']['autoAddFeuserStoragePageId']
            );
            $feuser = $feuserData['user'];

            if ($feuser) {
                $newApplication->addFeuser($feuser);
            }

            $newApplication->setPid($news->getPid());
            $newApplication->setHidden(true);
            $newApplication->setSysLanguageUid($news->getSysLanguageUid());
            $newApplication->setFormTimestamp($formTimestamp);

            //set creationdate
            $date = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->setTime(date('H'), date('i'), date('s'));
            $newApplication->setCrdate($date->getTimestamp());

            if ($news->getRecurrence() > 0) {
                //set total depending on either customer is an early bird or not and on earyBirdPrice is set
                $recurringEvent = $this->newsRecurrenceRepository->findByUid(
                    $this->request->getArgument('reservedRecurrence')
                );
                $reservedSlots = $this->request->getArgument(
                    'reservedSlots-' . $this->request->getArgument('reservedRecurrence')
                );
                $newApplication->setReservedSlots($reservedSlots);

                $earlyBirdDate = null;
                if ($recurringEvent->getEarlyBirdDate() != '' && $recurringEvent->getEarlyBirdDate() != '0') {
                    $earlyBirdDate = clone $recurringEvent->getEarlyBirdDate();
                } elseif ($news->getEarlyBirdDate() != '' && $news->getEarlyBirdDate() != '0') {
                    $earlyBirdDate = clone $news->getEarlyBirdDate();
                }
                $newApplication->setCosts(
                    $this->applicationService->getEventTotalCosts($news, $earlyBirdDate, $reservedSlots)
                );

                $newApplication->addRecurringevent($recurringEvent);
            } else {
                $earlyBirdDate = null;
                if ($news->getEarlyBirdDate() != '' && $news->getEarlyBirdDate() != '0') {
                    $earlyBirdDate = clone $news->getEarlyBirdDate();
                }
                $newApplication->setCosts(
                    $this->applicationService->getEventTotalCosts($news, $earlyBirdDate, $newApplication->getReservedSlots())
                );

                $newApplication->addEvent($news);
            }


            $this->applicationRepository->add($newApplication);

            $persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
            $persistenceManager->persistAll();



            if ($feuser) {
                $newApplication->setApplicationTitle($news->getTitle() . ' - ' . $feuser->getLastName() . ' ' . $feuser->getFirstName() . '-' . $newApplication->getUid());
            } else {
                $newApplication->setApplicationTitle($news->getTitle() . ' - ' . $newApplication->getName() . ' ' . $newApplication->getSurname() . '-' . $newApplication->getUid());
            }

            $this->applicationRepository->update($newApplication);
            $persistenceManager->persistAll();

            if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
                CacheUtility::flushCacheForNews($news->getUid());
            }


            $demand = $this->createDemandObjectFromSettings($this->settings);
            $demand->setActionAndClass(__METHOD__, __CLASS__);

            $assignedValues = [
                'newsItem'       => $news,
                'demand'         => $demand,
                'newApplication' => $newApplication,
                'settings'       => $this->settings,
            ];

            if ($news->getRecurrence() > 0) {
                $assignedValues['recurrence'] = $recurringEvent;
            }

            $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CREATEAPPLICATION_ACTION, $assignedValues);
            $this->view->assignMultiple($assignedValues);

            $sent = $this->mailService->sendMail($newApplication, $this->settings, $news);
            $this->flashMessage(
                $sent['message'],
                $sent['status'],
                $sent['type']
            );
            if(isset($sent['forward']) && $sent['forward'] === true) {
                $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
            }
        }


    }

    /**
     * getNewsOrPreviewNews
     *
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @return \GeorgRinger\News\Domain\Model\News
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    protected function getNewsOrPreviewNews(\GeorgRinger\News\Domain\Model\News $news = null)
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

        if (is_a(
            $news,
                'GeorgRinger\\News\\Domain\\Model\\News'
        ) && $this->settings['detail']['checkPidOfNewsRecord']
        ) {
            $news = $this->checkPidOfNewsRecord($news);
        }

        if (is_null($news) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }

        return $news;
    }



    /**
     * action confirmApplication.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function confirmApplicationAction(\FalkRoeder\DatedNews\Domain\Model\Application $newApplication)
    {
        $assignedValues = [];
        $event = null;

        if (is_null($newApplication)) {
            $this->flashMessage('applicationNotFound', 'applicationNotFoundStatus', 'ERROR');
        } else {
            //vor confirmation link validity check
            $date = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->setTime(date('H'), date('i'), date('s'));
            $hoursSinceBookingRequestSent = ($date->getTimestamp() - $newApplication->getCrdate()) / 3600;

            if ($newApplication->isConfirmed() === true) {
                //was allready confirmed
                $this->flashMessage(
                    'applicationAllreadyConfirmed',
                    'applicationAllreadyConfirmedStatus',
                    'INFO'
                );
            } elseif ($this->settings['dated_news']['validDaysConfirmationLink'] * 24 < $hoursSinceBookingRequestSent) {
                //confirmation link not valid anymore
                $this->flashMessage(
                    'applicationConfirmationLinkUnvalid',
                    'applicationConfirmationLinkUnvalidStatus',
                    'ERROR'
                );
            }

            if (
                $newApplication->isConfirmed() !== true &&
                !($this->settings['dated_news']['validDaysConfirmationLink'] * 24 < $hoursSinceBookingRequestSent)
            ) {
                //confirm



                $newApplication->setConfirmed(true);
                $newApplication->setHidden(false);
                $this->applicationRepository->update($newApplication);

                $events = $newApplication->getEvents();
                if ($events->count() === 0) {
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

                $sent = $this->mailService->sendMail($newApplication, $this->settings, $news, true);
                $this->flashMessage(
                    $sent['message'],
                    $sent['status'],
                    $sent['type']
                );
                if(isset($sent['forward']) && $sent['forward'] === true) {
                    $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
                }

                $assignedValues = [
                    'newApplication' => $newApplication,
                    'newsItem'       => $news,
                ];
                if ($news->getRecurrence() > 0 && !is_null($event)) {
                    $assignedValues['recurrence'] = $event;
                }
            }
        }
        if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
            CacheUtility::flushCacheForNews($news->getUid());

        }

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CONFIRMAPPLICATION_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);
    }



    /**
     * reloads Details of news via ajax.
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
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
                    $func = 'get' . ucfirst(trim($field));
                    if (method_exists($item, $func) === true) {
                        if ($field === 'slotsFree') {
                            $resultArray[$uid][$field] = $item->getSlotsFree();
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
     * adds needed flashmessages
     * for informations to user.
     *
     * @param \string $messageKey
     * @param \string $statusKey
     * @param \string $level
     *
     * @return void
     */
    public function flashMessage($messageKey, $statusKey, $level)
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
            default:
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO;
        }

        $message = (\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($messageKey, 'dated_news') !== null) ? \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($messageKey, 'dated_news') : $messageKey;

        $this->addFlashMessage(
            $message,
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($statusKey, 'dated_news'),
            $level,
            true
        );
    }


   

    
}
