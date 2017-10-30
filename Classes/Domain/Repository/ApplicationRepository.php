<?php

namespace FalkRoeder\DatedNews\Domain\Repository;

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
 * The repository for Applications.
 */
class ApplicationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
    ];

    public function initializeObject()
    {
        $this->defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $this->defaultQuerySettings->setRespectStoragePage(false);
//        $this->defaultQuerySettings->setIgnoreEnableFields(true);
//        $this->defaultQuerySettings->setEnableFieldsToBeIgnored(['hidden']);
    }

    /**
     * get number of applications allready sent to a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return int
     */
    public function countApplicationsForNews($newsId)
    {
        return count($this->getApplicationsForNews($newsId));
    }

    /**
     * get number of reserved Slots for a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return int
     */
    public function countReservedSlotsForNews($newsId)
    {
        $applications = $this->getApplicationsForNews($newsId);

        $reservedSlots = 0;
        foreach ($applications as $application) {
            if ($application->isConfirmed() === true) {
                $reservedSlots = $reservedSlots + $application->getReservedSlots();
            }
        }

        return $reservedSlots;
    }

    /**
     * get all applications allready sent to a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return QueryResultInterface|array
     */
    public function getApplicationsForNews($newsId)
    {
        $applications = $this->findAll();
        $applicationsForNews = [];
        foreach ($applications as $key => $application) {
            $events = $application->getEvents();
            foreach ($events as $event) {
                if ($event->getUid() === $newsId) {
                    $applicationsForNews[] = $application;
                }
            }
        }

        return $applicationsForNews;
    }


    /**
     * get number of applications allready sent to a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return int
     */
    public function countApplicationsForNewsRecurrence($newsId,$ignoreEnables = false )
    {
        return count($this->getApplicationsForNewsRecurrence($newsId, $ignoreEnables));
    }

    /**
     * get number of reserved Slots for a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return int
     */
    public function countReservedSlotsForNewsRecurrence($newsId)
    {
        $applications = $this->getApplicationsForNewsRecurrence($newsId);

        $reservedSlots = 0;
        foreach ($applications as $application) {
            if ($application->isConfirmed() === true) {
                $reservedSlots = $reservedSlots + $application->getReservedSlots();
            }
        }

        return $reservedSlots;
    }

    /**
     * get all applications allready sent to a specific news event.
     *
     * @param int $newsId id of news record
     *
     * @return QueryResultInterface|array
     */
    public function getApplicationsForNewsRecurrence($newsId, $ignoreEnables = false)
    {
        $query = $this->createQuery();
        if($ignoreEnables === true){
            $query->getQuerySettings()->setIgnoreEnableFields(true);            
        }
        
        $applications = $query->execute();
        $applicationsForNews = [];
        foreach ($applications as $key => $application) {
            $events = $application->getRecurringevents();
            foreach ($events as $event) {
                if ($event->getUid() === $newsId) {
                    $applicationsForNews[] = $application;
                }
            }
        }

        return $applicationsForNews;
    }
    
    

    /**
     * checks if form allready submited using a timestamp sent with the form.
     *
     * @param int $tstamp timestamp of form
     *
     * @return bool
     */
    public function isFirstFormSubmission($tstamp)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);
        if ($query->matching($query->equals('form_timestamp', $tstamp))->execute()->count() > 0) {
            return false;
        }

        return true;
    }
}
