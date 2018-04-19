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

use FalkRoeder\DatedNews\Utility\MiscUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;


/**
 * CalendarService.
 */
class CalendarService
{
    /**
     * takes categories of all
     * news records shown in calendar and put them into a array
     * also adds the colors which might be specified in
     * category to the array to enable filtering of calendar items
     * with colored buttons by category.
     * for filtering the calendar
     * todo: move to calendarService
     *
     * @param $newsRecords
     *
     * @return array
     */
    protected function getCategoriesOfNews($newsRecords)
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
     * for filtering the calendar
     * todo: move to calendarService
     * @param $newsRecords
     *
     * @return array
     */
    protected function getTagsOfNews($newsRecords)
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
     * getFilterValues
     *
     * gets all filtervalues based on tags and categories and sort them
     *
     * @param \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $newsRecords
     * @param boolean $showCategoryFilter
     * @param boolean $showTagFilter
     *
     * @return array
     */
    public function getCalendarFilterValues( $newsRecords, $showCategoryFilter = false, $showTagFilter = false, $sortingFilterlist = '')
    {
        if(!$newsRecords || $newsRecords->count() < 1) {
            return [];
        }

        $filterValues = [];
        if ($showCategoryFilter) {
            $filterValues = array_merge($filterValues, $this->getCategoriesOfNews($newsRecords));
        }
        if ($showTagFilter) {
            $filterValues = array_merge($filterValues, $this->getTagsOfNews($newsRecords));
        }

        if (!empty($filterValues)) {
            if ($sortingFilterlist === 'shuffle') {
                $assignedValues['filterValues'] = MiscUtility::shuffleAssoc($filterValues);
            } else {
                ksort($filterValues, SORT_LOCALE_STRING);
                $assignedValues['filterValues'] = $filterValues;
            }
        }

        return $filterValues;
    }


    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    public function __construct()
    {
        $this->extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->pageRenderer = $this->extbaseObjectManager->get('TYPO3\CMS\Core\Page\PageRenderer');

    }

    /**
     * renderCalendar
     *
     * @return void
     */
    public function renderCalendar($newsRecords, $settings, $overwriteDemand = null, $demand = null, $cObjUid, $renderSelf = false)
    {

        foreach ($newsRecords as $news) {
            $news->setTitle(addslashes($news->getTitle()));
            $news->setTeaser(addslashes($news->getTeaser()));
            $news->setDescription(addslashes($news->getDescription()));
            $news->setBodytext(addslashes($news->getBodytext()));
        }


        $this->addCalendarJSLibs($settings['dated_news']['includeJQuery'], $settings['dated_news']['jsFiles']);
        $this->addCalendarCss($settings['dated_news']['cssFile']);

        //collect news Uids for ajax request as we do not have the demandobject in our ajaxcall later
        $newsUids = '';
        foreach ($newsRecords as $news) {
            $newsUids .= ',' . $news->getUid();
        }

        $filterValues = $this->getCalendarFilterValues(
            $newsRecords,
            (bool)$settings['showCategoryFilter'],
            (bool)$settings['showTagFilter'],
            $settings['sortingFilterlist']
        );
        $assignedValues = [
            'news'            => $newsRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand'          => $demand,
            'newsUids'        => $newsUids,
            'filterValues'    => $filterValues,
            'cObjUid'         => $cObjUid
        ];

        if(!$renderSelf) {
            return $assignedValues;
        }
        $assignedValues['settings'] = $settings;

        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->getRequest()->setControllerExtensionName('dated_news');
        $templatePathAndFile = 'EXT:dated_news/Resources/Private/Templates/News/Calendar.html';
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($templatePathAndFile));
        $standaloneView->setLayoutRootPaths([0 => 'EXT:dated_news/Resources/Private/Layouts/']);

//        $standaloneView->getRenderingContext()->setControllerName('News');
//        $standaloneView->getRenderingContext()->setControllerAction('calendar');



        $standaloneView->assignMultiple($assignedValues);

        return $standaloneView;
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




}


