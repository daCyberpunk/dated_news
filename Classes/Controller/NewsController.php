<?php
namespace FalkRoeder\DatedNews\Controller;

use GeorgRinger\News\Utility\Cache;
use GeorgRinger\News\Utility\Page;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Falk Röder <mail@falk-roeder.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class FalkRoeder\DatedNews\Controller\NewsController
 */
class NewsController extends \GeorgRinger\News\Controller\NewsController
{

    const SIGNAL_NEWS_CALENDAR_ACTION = 'calendarAction';


    /**
     * Calendar view
     *
     * @param array $overwriteDemand
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
        
        $assignedValues = array(
            'news' => $newsRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand
        );
        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CALENDAR_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);
        Cache::addPageCacheTagsByDemandObject($demand);
    }


  
}