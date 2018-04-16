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
    public function getCalendarFilterValues(\TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $newsRecords, $showCategoryFilter = false, $showTagFilter = false, $sortingFilterlist = '')
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



}


