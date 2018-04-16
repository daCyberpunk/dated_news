<?php

namespace FalkRoeder\DatedNews\Service;

/***
 *
 * This file is part of the "" Extension for TYPO3 CMS.
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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * EventService.
 */
class EventService
{

    /**
     * @var \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository
     */
    protected $newsRecurrenceRepository = null;

    /**
     * Misc Functions.
     *
     * @var \FalkRoeder\DatedNews\Service\LinkToNewsItem
     */
    protected $linkToNewsItem;

     /**
     * ObjectManager
     *
     * @var ObjectManager
     */
    protected $objectManager;


    /**
     * constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->newsRecurrenceRepository = GeneralUtility::makeInstance(\FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository::class);
        $this->linkToNewsItem = GeneralUtility::makeInstance(\FalkRoeder\DatedNews\Service\LinkToNewsItem::class);
    }


    /**
     * getRecurrences in actual calendar view
     * @return array $eventsArray
     */
    public function getRecurrences($eventsArray, $calendarstart, $calendarend, $settings)
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
                    $eventsArray['tags'] = $this->getTagList($eventsArray['tags'], $parent, $evt);
                    array_push($eventsArray['events'], $this->createSingleEvent($parent, $settings, $evt));
                }
            } else {
                unset($recurrences[$key]);
            }
        }
        return $eventsArray;
    }

    /**
     * getSingleEventsFromNewsRecords for actual calendar view
     *
     * newsRecords filter if not an event, has recurrences or showincalendar === False
     *
     * @param $newsRecords
     * @return array
     */
    public function getSingleEventsFromNewsRecords($newsRecords, $eventsArray, $calendarstart, $calendarend, $settings)
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
                    $eventsArray['tags'] = $this->getTagList($eventsArray['tags'], $news);
                    array_push($eventsArray['events'], $this->createSingleEvent($news, $settings));
                }
            }
        }

        return $eventsArray;
    }

    /**
     * creates a single event array from given news / recurrence
     * todo: move to calendarService
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
     * creates list of tags from given news
     * todo: move to calendarService
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
     * @param $settings
     * @param $newsItem
     * todo: move to calendarService
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
     * getCalendarItemColors
     * todo: move to calendarService
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
}


