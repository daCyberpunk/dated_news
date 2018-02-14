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
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Services\LinkToNewsItem
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
        if($cObj && isset($cObj->data['uid'])){
            $pluginConfiguration = $this->div->getPluginConfiguration($cObj->data['uid']);
            $this->settings['switchableControllerActions'] = $pluginConfiguration['switchableControllerActions'];
        }
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
     * creates list of tags from given news
     *
     *
     * @param $tagArray
     * @param $news
     * @param null $recurrence
     * @return string
     */
    public function getTagList($tagArray, $news, $recurrence = null)
    {
        $tags = $news->getTags()->toArray();
        $categories = $news->getCategories()->toArray();
        $tagsAndCats = array_merge($tags, $categories);
        $uid = $recurrence ? 'r' . $recurrence->getUid() : 'n' . $news->getUid();

        foreach ($tagsAndCats as $value) {
            $tagTitle = $value->getTitle();
            if (array_key_exists($tagTitle, $tagArray)) {
                if (!in_array($uid, $tagArray[$tagTitle])) {
                    array_push($tagArray[$tagTitle], $uid);
                }
            } else {
                $tagArray[$tagTitle] = [];
                array_push($tagArray[$tagTitle], $uid);
            }
        }

        return $tagArray;
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
     * Get events via AJAX .
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
        $settings = $this->div->getPluginConfiguration($this->request->getArgument('cUid'));
        $settings = array_merge($this->settings, $settings['settings']);

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

        $result = $this->addNewsForCalendar(
            $newsRecords->toArray(),
            $result,
            $calendarstart,
            $calendarend,
            $settings
        );

        return json_encode(
            $this->addRecurrencesForCalendar(
                $result,
                $calendarstart,
                $calendarend,
                $settings
            )
        );
    }

    /**
     * addRecurrencesForCalendar
     *
     * @return array
     */
    public function addRecurrencesForCalendar($result, $calendarstart, $calendarend, $settings)
    {
        $recurrences = $this->newsRecurrenceRepository->getBetweenDates([$calendarstart, $calendarend]);
        foreach ($recurrences as $key => $evt) {
            $parents = $evt->getParentEvent()->toArray();
            if (isset($parents[0])) {
                $parent = $parents[0];
                if (
                    $parent->getHidden()||
                    !$parent->isShowincalendar()
                ) {
                    unset($recurrences[$key]);
                } else {
                    $result['tags'] = $this->getTagList($result['tags'], $parent, $evt);
                    array_push($result['events'], $this->createSingleEvent($parent, $settings, $evt));
                }
            } else {
                unset($recurrences[$key]);
            }
        }
        return $result;
    }

    /**
     * filterNewsForCalendar
     *
     * newsRecords filter if not an event, has recurrences or showincalendar === False
     *
     * @param $newsRecords
     * @return array
     */
    public function addNewsForCalendar($newsRecords, $result, $calendarstart, $calendarend, $settings)
    {
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

                if (
                    $newsEnd < $calendarstart ||
                    $newsStart > $calendarend
                ) {
                    unset($newsRecords[$key]);
                } else {
                    $result['tags'] = $this->getTagList($result['tags'], $news);
                    array_push($result['events'], $this->createSingleEvent($news, $settings));
                }
            }
        }

        return $result;
    }

    /**
     * creates a single event array from given news / recurrence
     *
     *
     * @param $news
     * @param $settings
     * @param null $recurrence
     * @return array
     */
    public function createSingleEvent($news, $settings, $recurrence = null)
    {
        $calendarDateformat = 'Y-m-d H:i:s';
        $start = $recurrence ? $recurrence->getEventstart() : $news->getEventstart();
        $end = $recurrence ?  $recurrence->getEventend() : $news->getEventend();
        $allDay = $news->getFulltime();
        $qtip = ' \'' . trim(preg_replace("/\r|\n/", '', $this->renderQtip($settings, $news, $recurrence))) . '\'';

        $diff = date_diff($end, $start);
        if ($diff->d > 0 && $allDay === true) {
            $end->modify('+1 day');
        }

        if (!$start instanceof \DateTime) {
            try {
                $start = new \DateTime($start);
            } catch (\Exception $exception) {
                throw new Exception('"' . $start . '" could not be parsed by DateTime constructor.', 1438925934);
            }
        }
        if (!$end instanceof \DateTime && $end !== null) {
            try {
                $end = new \DateTime($end);
            } catch (\Exception $exception) {
                throw new Exception('"' . $end . '" could not be parsed by DateTime constructor.', 1438925934);
            }
        }

        $uri = $this->linkToNewsItem->getLink($news, $settings);
        $uid = $recurrence ? 'r' . $recurrence->getUid() : 'n' . $news->getUid();
        $colors = $this->getCalendarItemColors($news);

        return [
            'title' => $news->getTitle(),
            'id' => $uid,
            'end' => $end->format($calendarDateformat),
            'start' => $start->format($calendarDateformat),
            'url' => $uri,
            'allDay' => $allDay,
            'className' => 'Event_' . $uid,
            'qtip' => $qtip,
            'color' => $colors['color'],
            'textColor' => $colors['textColor']
        ];
    }

    /**
     * getCalendarItemColors
     *
     * @return array
     */
    public function getCalendarItemColors($news)
    {
        $categories = $news->getCategories();
        $color = trim($news->getBackgroundcolor());
        $textColor = trim($news->getTextcolor());
        if ($color === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getBackgroundcolor());
                $color = $tempColor === '' ? $color : $tempColor;
            }
        }
        if ($textColor === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getTextcolor());
                $textColor = $tempColor === '' ? $textColor : $tempColor;
            }
        }

        return [
            'color' => $color,
            'textColor' => $textColor
        ];
    }

    /**
     * @param $settings
     * @param $newsItem
     *
     * @param null $recurrence
     * @return string html output of Qtip.html
     */
    public function renderQtip($settings, $newsItem, $recurrence = null)
    {
        $configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManagerInterface');
        $tsSettings             = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);

        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $qtip = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $partialRootPaths = $tsSettings['plugin.']['tx_news.']['view.']['partialRootPaths.'];
        $partial = false;

        foreach ($partialRootPaths as $key => $path) {
            $partialRootPaths[$key] = $path . 'Calendar';
        }

        foreach (array_reverse($partialRootPaths) as $key => $path) {
            if (!$partial && file_exists(GeneralUtility::getFileAbsFileName($path . '/Qtip.html'))) {
                $partial = GeneralUtility::getFileAbsFileName($path . '/Qtip.html');
            }
        }

        $qtip->setTemplatePathAndFilename($partial);
        $qtip->setPartialRootPaths($partialRootPaths);
        $assignedValues = [
            'newsItem' => $newsItem,
            'settings' => $settings,
        ];
        if (null !== $recurrence) {
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
        if ($this->applicationRepository->isFirstFormSubmission($formTimestamp)) {
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
                    $this->getEventTotalCosts($news, $earlyBirdDate, $reservedSlots)
                );

                $newApplication->addRecurringevent($recurringEvent);
            } else {
                $earlyBirdDate = null;
                if ($news->getEarlyBirdDate() != '' && $news->getEarlyBirdDate() != '0') {
                    $earlyBirdDate = clone $news->getEarlyBirdDate();
                }
                $newApplication->setCosts(
                    $this->getEventTotalCosts($news, $earlyBirdDate, $newApplication->getReservedSlots())
                );

                $newApplication->addEvent($news);
            }

            $this->applicationRepository->add($newApplication);

            $persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
            $persistenceManager->persistAll();
            $newApplication->setApplicationTitle($news->getTitle() . ' - ' . $newApplication->getName() . ' ' . $newApplication->getSurname() . '-' . $newApplication->getUid());
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

            if ($news->getRecurrence() > 0) {
                $assignedValues['recurrence'] = $recurringEvent;
            }

            $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CREATEAPPLICATION_ACTION, $assignedValues);
            $this->view->assignMultiple($assignedValues);

            $this->sendMail($newApplication, $this->settings, $news);
        } else {
            $this->flashMessageService('applicationSendMessageAllreadySent', 'applicationSendMessageAllreadySentStatus', 'ERROR');
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
     * getEventTotalCosts
     *
     * @param $news
     * @param $earlyBirdDate
     * @param $reservedSlots
     * @return float|int
     */
    public function getEventTotalCosts($news, $earlyBirdDate, $reservedSlots)
    {
        if ($news->getEarlyBirdPrice() != '' && $earlyBirdDate !== null) {
            $earlyBirdDate->setTime(0, 0, 0);

            $today = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
            $today->setTime(0, 0, 0);

            if ($earlyBirdDate >= $today) {
                $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getEarlyBirdPrice()));
            } else {
                $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getPrice()));
            }
        } else {
            $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getPrice()));
        }
        return $costs;
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
            $this->flashMessageService('applicationNotFound', 'applicationNotFoundStatus', 'ERROR');
        } else {
            //vor confirmation link validity check
            $date = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'))->setTime(date('H'), date('i'), date('s'));
            $hoursSinceBookingRequestSent = ($date->getTimestamp() - $newApplication->getCrdate()) / 3600;

            if ($newApplication->isConfirmed() === true) {
                //was allready confirmed
                $this->flashMessageService('applicationAllreadyConfirmed', 'applicationAllreadyConfirmedStatus', 'INFO');
            } elseif ($this->settings['dated_news']['validDaysConfirmationLink'] * 24 < $hoursSinceBookingRequestSent) {
                //confirmation link not valid anymore
                $this->flashMessageService('applicationConfirmationLinkUnvalid', 'applicationConfirmationLinkUnvalidStatus', 'ERROR');
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

                $this->sendMail($newApplication, $this->settings, $news, true);
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
            GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_pages')->flushByTag('tx_news_uid_' . $news->getUid());
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
    public function sendMail(\FalkRoeder\DatedNews\Domain\Model\Application $newApplication, $settings, \GeorgRinger\News\Domain\Model\News $news = null, $confirmation = false)
    {

        //generell event infos
        if ($news->getRecurrence() > 0) {
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
            $event = null;
        }

        // from
        if (!empty($this->settings['senderMail'])) {
            $sender = [$this->settings['senderMail'] => $this->settings['senderName']];
        } else {
            $this->flashMessageService(
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('applicationSendMessageGeneralError', 'dated_news', ['subject' => '(no sendermail configured.)']),
                'applicationSendStatusGeneralErrorStatus',
                'ERROR'
            );
            return;
        }

        //validate Mailadress of applyer
        $applyerMail = $newApplication->getEmail();
        $applyer = [];
        if (is_string($applyerMail) && GeneralUtility::validEmail($applyerMail)) {
            $applyer = [
                $newApplication->getEmail() => $newApplication->getName() . ', ' . $newApplication->getSurname(),
            ];
        } else {
            $this->flashMessageService('applicationSendMessageNoApplyerEmail', 'applicationSendMessageNoApplyerEmailStatus', 'ERROR');
            $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
        }

        //get filenames of flexform/TS files to send to applyer
        $filenames = [];
        if ($confirmation === false) {
            $this->getFileNamesToSend();
        }

        $subject = $this->getEmailSubject($news, $eventstart, $newsLocation);

        // send email Customer
        $sendMailConf = [
            'template' => ($confirmation === true) ? 'MailConfirmationApplyer' : 'MailApplicationApplyer',
            'recipients' => $applyer,
            'recipientsCc' => [],
            'recipientsBcc' => [],
            'sender' => $sender,
            'subject' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', ['subject' => $subject]),
            'variables' => ['newApplication' => $newApplication, 'news' => $news, 'recurrence' => $event, 'settings' => $settings],
            'fileNames' => $filenames
        ];

        if (!$this->div->sendEmail($sendMailConf)) {
            $this->flashMessageService('applicationSendMessageApplyerError', 'applicationSendStatusApplyerErrorStatus', 'ERROR');
        } else {
            if ($confirmation === false) {
                $this->flashMessageService('applicationSendMessage', 'applicationSendStatus', 'OK');
            }
        }

        //Send to admins etc only when booking / application confirmed
        if ($confirmation === true) {
            $recipients = $this->getEmailRecipients($news);

            if (!count($recipients)) {
                $this->flashMessageService('applicationSendMessageNoRecipients', 'applicationSendMessageNoRecipientsStatus', 'ERROR');
                $this->forward('eventDetail', null, null, ['news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication]);
            }

            // send email to authors and Plugins mail addresses
            $sendMailConf = [
                'template' => 'MailApplicationNotification',
                'recipients' => $recipients,
                'recipientsCc' => [],
                'recipientsBcc' => [],
                'sender' => $sender,
                'subject' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', ['subject' => $subject]),
                'variables' => ['newApplication' => $newApplication, 'news' => $news, 'recurrence' => $event, 'settings' => $this->settings],
                'fileNames' => []
            ];

            if ($this->div->sendEmail($sendMailConf)) {
                $this->flashMessageService('applicationConfirmed', 'applicationConfirmedStatus', 'OK');
            } else {
                $this->flashMessageService('applicationSendMessageGeneralError', 'applicationSendStatusGeneralErrorStatus', 'ERROR');
            }
        }
        if ($confirmation === true && $this->settings['ics']) {
            //create ICS File and send invitation
            $newsTitle = $news->getTitle();
            $icsLocation = '';
            $i = 0;
            if (isset($newsLocation) && count($newsLocation) < 2) {
                foreach ($newsLocation as $location) {
                    $icsLocation .= $location->getName() . ', ' . $location->getAddress() . ', ' . $location->getZip() . ' ' . $location->getCity() . ', ' . $location->getCountry();
                }
            } else {
                foreach ($newsLocation as $location) {
                    $i++;
                    if ($i === 1) {
                        $icsLocation .= $location->getName();
                    } else {
                        $icsLocation .= ', ' . $location->getName();
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
            $description = $this->getIcsDescription($news, $settings, $event);
            if ($description !== false) {
                $properties['description'] = $description;
            }

            $ics = new \FalkRoeder\DatedNews\Services\ICS($properties);
            $icsAttachment = [
                'content' => $ics->to_string(),
                'name'    => str_replace(' ', '_', $newsTitle),

            ];

            $sendMailConf = [
                'template' => 'MailConfirmationApplyer',
                'recipients' => $applyer,
                'recipientsCc' => [],
                'recipientsBcc' => [],
                'sender' => [$this->settings['senderMail'] => $this->settings['senderName']],
                'subject' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.invitation_subject', 'dated_news', ['subject' => $subject]),
                'variables' => ['newApplication' => $newApplication, 'news' => $news, 'recurrence' => $event, 'settings' => $settings],
                'attachment' => $icsAttachment,
                'replyTo' => [substr_replace($this->settings['senderMail'], 'noreply', 0, strpos($this->settings['senderMail'], '@')) => $this->settings['senderName']]
            ];
            if (!$this->div->sendIcsInvitation($sendMailConf)) {
                $this->flashMessageService('applicationSendMessageApplyerError', 'applicationSendStatusApplyerErrorStatus', 'ERROR');
            } else {
                if ($confirmation === false) {
                    $this->flashMessageService('applicationSendMessage', 'applicationSendStatus', 'OK');
                }
            }
        }
    }

    /**
     * getEmailRecipients
     *
     * @param $news
     * @return array
     */
    public function getEmailRecipients($news)
    {
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
        return $recipients;
    }

    /**
     * getFileNamesToSend
     *
     * @return array
     */
    public function getFileNamesToSend()
    {
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
        return $filenames;
    }

    /**
     * getEmailSubject
     *
     * @return string
     */
    public function getEmailSubject($news, $eventstart, $newsLocation)
    {
        $subjectFields = explode(',', $this->settings['dated_news']['emailSubjectFields']);
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
                            $subject = $locationIterator === 1 ? $subject . $location->getName() : $subject . ', ' . $location->getName();
                        }
                    }
                    break;
                default:
            }
        }
    }

    /**
     * getIcsDescription.
     *
     * creates the ICS description for the
     * invitation send to Customer
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param $event
     * @param array $settings
     *
     * @return bool|string
     */
    public function getIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $settings, $event = null)
    {
        switch ($settings['icsDescriptionField']) {
            case 'Teaser':
                $result = $this->getEventTeaser($news, $event);
                break;
            case 'Description':
                $description = $news->getDescription();
                if (($description == strip_tags($description))) {
                    $result = $description;
                }
                break;
            case 'Url':
                $uri = $this->linkToNewsItem->getLink($news, $settings);
                $result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', ['url' => $uri]);
                break;
            case 'Custom':
                $result = $this->getCustomIcsDescription($news, $settings, $event);
                break;
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * getCustomIcsDescription
     *
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @param $settings
     * @param $event
     * @return string | bool
     */
    public function getCustomIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $settings, $event = null)
    {
        $result = false;
        if (trim($settings['icsDescriptionCustomField']) !== '') {
            if ($news->getRecurrence() > 0 && !is_null($event)) {
                $object = $event;
            } else {
                $object = $news;
            }
            $func = 'get' . ucfirst(trim($settings['icsDescriptionCustomField']));
            if (method_exists($object, $func) === true) {
                $description = $object->{$func}();
                if (trim($description) === '' || $description === strip_tags($description)) {
                    $result = $description;
                }
            }
        }

        return $result;
    }

    /**
     * getEventTeaser
     *
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @param $event
     * @return string | bool
     */
    public function getEventTeaser(\GeorgRinger\News\Domain\Model\News $news, $event = null)
    {
        $result = false;
        if ($news->getRecurrence() > 0 && !is_null($event)) {
            $teaser = $event->getTeaser();
        } else {
            $teaser = $news->getTeaser();
        }
        if ($teaser == strip_tags($teaser)) {
            $result = $teaser;
        }
        return $result;
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
