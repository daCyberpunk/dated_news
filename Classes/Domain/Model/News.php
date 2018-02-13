<?php

namespace FalkRoeder\DatedNews\Domain\Model;

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

/**
 * News.
 */
class News extends \GeorgRinger\News\Domain\Model\News
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application>
     */
    protected $application;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\NewsRecurrence>
     */
    protected $newsrecurrence;

    /**
     * showincalendar.
     *
     * @var bool
     */
    protected $showincalendar = false;

    /**
     * enableApplication.
     *
     * @var bool
     */
    protected $enableApplication = false;

    /**
     * fulltime.
     *
     * @var bool
     */
    protected $fulltime = false;

    /**
     * textcolor.
     *
     * @var string
     */
    protected $textcolor = '';

    /**
     * backgroundcolor.
     *
     * @var string
     */
    protected $backgroundcolor = '';

    /**
     * eventstart.
     *
     * @var \DateTime
     */
    protected $eventstart = null;

    /**
     * eventend.
     *
     * @var \DateTime
     */
    protected $eventend = null;

    /**
     * eventtype.
     *
     * @var string
     */
    protected $eventtype = '';

    /**
     * eventlocation.
     *
     * @var string
     */
    protected $eventlocation = '';

    /**
     * slots.
     *
     * @var int
     */
    protected $slots;


    /**
     * price.
     *
     * @var string
     */
    protected $price = '';

    /**
     * earlyBirdPrice.
     *
     * @var string
     */
    protected $earlyBirdPrice = '';

    /**
     * earlyBirdDate.
     *
     * @var \DateTime
     */
    protected $earlyBirdDate = '';

    /**
     * targetgroup.
     *
     * @var string
     */
    protected $targetgroup = '';

    /**
     * recurrence.
     *
     * @var int
     */
    protected $recurrence = 0;

    /**
     * recurrenceType.
     *
     * @var int
     */
    protected $recurrenceType = 0;

    /**
     * recurrenceUntil.
     *
     * @var \DateTime
     */
    protected $recurrenceUntil = null;

    /**
     * recurrenceCount.
     *
     * @var int
     */
    protected $recurrenceCount = 0;

    /**
     * udType.
     *
     * @var int
     */
    protected $udType = 0;

    /**
     * udDailyEverycount.
     *
     * @var int
     */
    protected $udDailyEverycount = 0;

    /**
     * udWeeklyEverycount.
     *
     * @var int
     */
    protected $udWeeklyEverycount = 0;

    /**
     * udWeeklyWeekdays.
     *
     * @var int
     */
    protected $udWeeklyWeekdays = 0;

    /**
     * udMonthlyBase.
     *
     * @var int
     */
    protected $udMonthlyBase = 0;

    /**
     * udMonthlyPerday.
     *
     * @var int
     */
    protected $udMonthlyPerday = 0;

    /**
     * udMonthlyPerdayWeekdays.
     *
     * @var int
     */
    protected $udMonthlyPerdayWeekdays = 0;

    /**
     * udMonthlyPerdateDay.
     *
     * @var int
     */
    protected $udMonthlyPerdateDay = 0;

    /**
     * udMonthlyPerdateLastday.
     *
     * @var int
     */
    protected $udMonthlyPerdateLastday = 0;

    /**
     * udMonthlyEverycount.
     *
     * @var int
     */
    protected $udMonthlyEverycount = 0;

    /**
     * udYearlyEverycount.
     *
     * @var int
     */
    protected $udYearlyEverycount = 0;

    /**
     * udYearlyPerday.
     *
     * @var int
     */
    protected $udYearlyPerday = 0;

    /**
     * udYearlyPerdayWeekdays.
     *
     * @var int
     */
    protected $udYearlyPerdayWeekdays = 0;

    /**
     * udYearlyPerdayMonth.
     *
     * @var int
     */
    protected $udYearlyPerdayMonth = 0;

    /**
     * pid.
     *
     * @var int
     */
    protected $pid;

    /**
     * locations.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location>
     * @lazy
     */
    protected $locations = null;

    /**
     * persons.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person>
     * @lazy
     */
    protected $persons = null;

    /**
     * Returns the price.
     *
     * @return string $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the price.
     *
     * @param string $price
     *
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Returns the targetgroup.
     *
     * @return string $targetgroup
     */
    public function getTargetgroup()
    {
        return $this->targetgroup;
    }

    /**
     * Sets the targetgroup.
     *
     * @param string $targetgroup
     *
     * @return void
     */
    public function setTargetgroup($targetgroup)
    {
        $this->targetgroup = $targetgroup;
    }

    /**
     * Returns the earlyBirdPrice.
     *
     * @return string $earlyBirdPrice
     */
    public function getEarlyBirdPrice()
    {
        return $this->earlyBirdPrice;
    }

    /**
     * Sets the earlyBirdPrice.
     *
     * @param string $earlyBirdPrice
     *
     * @return void
     */
    public function setEarlyBirdPrice($earlyBirdPrice)
    {
        $this->earlyBirdPrice = $earlyBirdPrice;
    }

    /**
     * Returns the earlyBirdDate.
     *
     * @return string $earlyBirdDate
     */
    public function getEarlyBirdDate()
    {
        return $this->earlyBirdDate;
    }

    /**
     * Sets the earlyBirdDate.
     *
     * @param string $earlyBirdDate
     *
     * @return void
     */
    public function setEarlyBirdDate($earlyBirdDate)
    {
        $this->earlyBirdDate = $earlyBirdDate;
    }

    /**
     * Adds a Location.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     *
     * @return void
     */
    public function addLocation(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->locations->attach($location);
    }

    /**
     * Removes a Location.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $locationToRemove The Location to be removed
     *
     * @return void
     */
    public function removeLocation(\FalkRoeder\DatedNews\Domain\Model\Location $locationToRemove)
    {
        $this->locations->detach($locationToRemove);
    }

    /**
     * Returns the locations.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location> $locations
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Sets the locations.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $locations
     *
     * @internal param $ \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location> $locations
     */
    public function setLocations(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $locations)
    {
        $this->locations = $locations;
    }

    /**
     * Adds a Person.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     *
     * @return void
     */
    public function addPerson(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->persons->attach($person);
    }

    /**
     * Removes a Person.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $personToRemove The Person to be removed
     *
     * @return void
     */
    public function removePerson(\FalkRoeder\DatedNews\Domain\Model\Person $personToRemove)
    {
        $this->persons->detach($personToRemove);
    }

    /**
     * Returns the persons.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person> $persons
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Sets the persons.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $persons
     *
     * @internal param $ \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person> $persons
     */
    public function setPersons(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $persons)
    {
        $this->persons = $persons;
    }

    /**
     * Returns the showincalendar.
     *
     * @return bool $showincalendar
     */
    public function getShowincalendar()
    {
        return $this->showincalendar;
    }

    /**
     * Sets the showincalendar.
     *
     * @param bool $showincalendar
     *
     * @return void
     */
    public function setShowincalendar($showincalendar)
    {
        $this->showincalendar = $showincalendar;
    }

    /**
     * Returns the boolean state of showincalendar.
     *
     * @return bool
     */
    public function isShowincalendar()
    {
        return $this->showincalendar;
    }

    /**
     * Returns the enableApplication.
     *
     * @return bool $enableApplication
     */
    public function getEnableApplication()
    {
        return $this->enableApplication;
    }

    /**
     * Sets the enableApplication.
     *
     * @param bool $enableApplication
     *
     * @return void
     */
    public function setEnableApplication($enableApplication)
    {
        $this->enableApplication = $enableApplication;
    }

    /**
     * Returns the boolean state of enableApplication.
     *
     * @return bool
     */
    public function isEnableApplication()
    {
        return $this->enableApplication;
    }

    /**
     * Returns the fulltime.
     *
     * @return bool $fulltime
     */
    public function getFulltime()
    {
        return $this->fulltime;
    }

    /**
     * Sets the fulltime.
     *
     * @param bool $fulltime
     *
     * @return void
     */
    public function setFulltime($fulltime)
    {
        $this->fulltime = $fulltime;
    }

    /**
     * Returns the boolean state of fulltime.
     *
     * @return bool
     */
    public function isFulltime()
    {
        return $this->fulltime;
    }

    /**
     * Returns the eventstart.
     *
     * @return \DateTime $eventstart
     */
    public function getEventstart()
    {
        return $this->eventstart;
    }

    /**
     * Sets the eventstart.
     *
     * @param \DateTime $eventstart
     *
     * @return void
     */
    public function setEventstart(\DateTime $eventstart)
    {
        $this->eventstart = $eventstart;
    }

    /**
     * Returns the eventend.
     *
     * @return \DateTime $eventend
     */
    public function getEventend()
    {
        return $this->eventend;
    }

    /**
     * Sets the eventend.
     *
     * @param \DateTime $eventend
     *
     * @return void
     */
    public function setEventend(\DateTime $eventend)
    {
        $this->eventend = $eventend;
    }

    /**
     * Returns the eventtype.
     *
     * @return \DateTime $eventtype
     */
    public function getEventtype()
    {
        return $this->eventtype;
    }

    /**
     * Sets the eventtype.
     *
     * @param \DateTime $eventtype
     *
     * @return void
     */
    public function setEventtype(\DateTime $eventtype)
    {
        $this->eventtype = $eventtype;
    }

    /**
     * is an event.
     *
     * @return bool
     */
    public function isEvent()
    {
        return $this->eventtype === '' ? false : true;
    }



    /**
     * Returns the eventlocation.
     *
     * @return string $eventlocation
     */
    public function getEventlocation()
    {
        return $this->eventlocation;
    }

    /**
     * Sets the eventlocation.
     *
     * @param string $eventlocation
     *
     * @return void
     */
    public function setEventlocation($eventlocation)
    {
        $this->eventlocation = $eventlocation;
    }

    /**
     * Returns the textcolor.
     *
     * @return string $textcolor
     */
    public function getTextcolor()
    {
        return $this->textcolor;
    }

    /**
     * Sets the textcolor.
     *
     * @param string $textcolor
     *
     * @return void
     */
    public function setTextcolor($textcolor)
    {
        $this->textcolor = $textcolor;
    }

    /**
     * Returns the slots.
     *
     * @return int $slots
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Sets the slots.
     *
     * @param int $slots
     *
     * @return void
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }


    /**
     * Returns the slotsFree.
     *
     * @return int $slotsFree
     */
    public function getSlotsFree()
    {
        if ($this->getRecurrence() > 0) {
            $recurrences = $this->getNewsRecurrence()->toArray();
            $sumFreeSlots = 0;
            foreach ($recurrences as $recurrence) {
                $slotsfree = $recurrence->getSlotsFree();
                $sumFreeSlots = $sumFreeSlots + $slotsfree;
            }

            return $sumFreeSlots;
        } else {
            return (int) $this->getSlots() - $this->getReservedSlotsCount();
        }
    }

    /**
     * getReservedSlotsCount
     *
     * @return int
     */
    public function getReservedSlotsCount()
    {
        return count($this->getApplication()->toArray());
    }

    /**
     * Returns the slotoptions.
     *
     * @return array $slotoptions
     */
    public function getSlotoptions()
    {
        $options = [];
        $i = 1;
        while ($i <= $this->getSlotsFree()) {
            $slotoption = new \stdClass();
            $slotoption->key = $i;
            $slotoption->value = $i;
            $options[] = $slotoption;
            $i++;
        }
        return $options;
    }


    /**
     * Returns the backgroundcolor.
     *
     * @return string $backgroundcolor
     */
    public function getBackgroundcolor()
    {
        return $this->backgroundcolor;
    }

    /**
     * Sets the backgroundcolor.
     *
     * @param string $backgroundcolor
     *
     * @return void
     */
    public function setBackgroundcolor($backgroundcolor)
    {
        $this->backgroundcolor = $backgroundcolor;
    }

    /**
     * Adds a Application.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $application
     *
     * @return void
     */
    public function addApplication(\FalkRoeder\DatedNews\Domain\Model\Application $application)
    {
        $this->application->attach($application);
    }

    /**
     * Removes a Application.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove The Application to be removed
     *
     * @return void
     */
    public function removeApplication(\FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove)
    {
        $this->application->detach($applicationToRemove);
    }

    /**
     * Returns the application.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Sets the application.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $application
     *
     * @internal param $ \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $application
     */
    public function setApplication(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $application)
    {
        $this->application = $application;
    }

    /**
     * Adds a NewsRecurrence.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\NewsRecurrence $newsrecurrence
     *
     * @return void
     */
    public function addNewsRecurrence(\FalkRoeder\DatedNews\Domain\Model\NewsRecurrence $newsrecurrence)
    {
        $this->newsrecurrence->attach($newsrecurrence);
    }

    /**
     * Removes a NewsRecurrence.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\NewsRecurrence $newsrecurrenceToRemove The Application to be removed
     *
     * @return void
     */
    public function removeNewsRecurrence(\FalkRoeder\DatedNews\Domain\Model\NewsRecurrence $newsrecurrenceToRemove)
    {
        $this->newsrecurrence->detach($newsrecurrenceToRemove);
    }

    /**
     * Returns the newsrecurrence.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\NewsRecurrence> $newsrecurrence
     */
    public function getNewsRecurrence()
    {
        return $this->newsrecurrence;
    }

    /**
     * Has newsrecurrence.
     *
     * @return bool
     */
    public function hasNewsRecurrences()
    {
        return count($this->getNewsRecurrence()->toArray()) > 0 ? true : false;
    }

    /**
     * Sets the newsrecurrence.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $newsrecurrence
     *
     * @internal param $ \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\NewsRecurrence> $newsrecurrence
     */
    public function setNewsRecurrence(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $newsrecurrence)
    {
        $this->newsrecurrence = $newsrecurrence;
    }

    /**
     * Returns the recurrence.
     *
     * @return int $recurrence
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * Sets the recurrence.
     *
     * @param int $recurrence
     *
     * @return void
     */
    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;
    }

    /**
     * Returns the recurrenceType.
     *
     * @return int $recurrenceType
     */
    public function getRecurrenceType()
    {
        return $this->recurrenceType;
    }

    /**
     * Sets the recurrenceType.
     *
     * @param int $recurrenceType
     *
     * @return void
     */
    public function setRecurrenceType($recurrenceType)
    {
        $this->recurrenceType = $recurrenceType;
    }

    /**
     * Returns the recurrenceUntil.
     *
     * @return \DateTime $recurrenceUntil
     */
    public function getRecurrenceUntil()
    {
        return $this->recurrenceUntil;
    }

    /**
     * Sets the recurrenceUntil.
     *
     * @param \DateTime $recurrenceUntil
     *
     * @return void
     */
    public function setRecurrenceUntil(\DateTime $recurrenceUntil)
    {
        $this->recurrenceUntil = $recurrenceUntil;
    }

    /**
     * Returns the recurrenceCount.
     *
     * @return int $recurrenceCount
     */
    public function getRecurrenceCount()
    {
        return $this->recurrenceCount;
    }

    /**
     * Sets the recurrenceCount.
     *
     * @param int $recurrenceCount
     *
     * @return void
     */
    public function setRecurrenceCount($recurrenceCount)
    {
        $this->recurrenceCount = $recurrenceCount;
    }

    /**
     * Returns the udType.
     *
     * @return int $udType
     */
    public function getUdType()
    {
        return $this->udType;
    }

    /**
     * Sets the udType.
     *
     * @param int $udType
     *
     * @return void
     */
    public function setUdType($udType)
    {
        $this->udType = $udType;
    }

    /**
     * Returns the udDailyEverycount.
     *
     * @return int $udDailyEverycount
     */
    public function getUdDailyEverycount()
    {
        return $this->udDailyEverycount;
    }

    /**
     * Sets the udDailyEverycount.
     *
     * @param int $udDailyEverycount
     *
     * @return void
     */
    public function setUdDailyEverycount($udDailyEverycount)
    {
        $this->udDailyEverycount = $udDailyEverycount;
    }

    /**
     * Returns the udWeeklyEverycount.
     *
     * @return int $udWeeklyEverycount
     */
    public function getUdWeeklyEverycount()
    {
        return $this->udWeeklyEverycount;
    }

    /**
     * Sets the udWeeklyEverycount.
     *
     * @param int $udWeeklyEverycount
     *
     * @return void
     */
    public function setUdWeeklyEverycount($udWeeklyEverycount)
    {
        $this->udWeeklyEverycount = $udWeeklyEverycount;
    }

    /**
     * Returns the udWeeklyWeekdays.
     *
     * @return int $udWeeklyWeekdays
     */
    public function getUdWeeklyWeekdays()
    {
        return $this->udWeeklyWeekdays;
    }

    /**
     * Sets the udWeeklyWeekdays.
     *
     * @param int $udWeeklyWeekdays
     *
     * @return void
     */
    public function setUdWeeklyWeekdays($udWeeklyWeekdays)
    {
        $this->udWeeklyWeekdays = $udWeeklyWeekdays;
    }

    /**
     * Returns the udMonthlyBase.
     *
     * @return int $udMonthlyBase
     */
    public function getUdMonthlyBase()
    {
        return $this->udMonthlyBase;
    }

    /**
     * Sets the udMonthlyBase.
     *
     * @param int $udMonthlyBase
     *
     * @return void
     */
    public function setUdMonthlyBase($udMonthlyBase)
    {
        $this->udMonthlyBase = $udMonthlyBase;
    }

    /**
     * Returns the udMonthlyPerday.
     *
     * @return int $udMonthlyPerday
     */
    public function getUdMonthlyPerday()
    {
        return $this->udMonthlyPerday;
    }

    /**
     * Sets the udMonthlyPerday.
     *
     * @param int $udMonthlyPerday
     *
     * @return void
     */
    public function setUdMonthlyPerday($udMonthlyPerday)
    {
        $this->udMonthlyPerday = $udMonthlyPerday;
    }

    /**
     * Returns the udMonthlyPerdayWeekdays.
     *
     * @return int $udMonthlyPerdayWeekdays
     */
    public function getUdMonthlyPerdayWeekdays()
    {
        return $this->udMonthlyPerdayWeekdays;
    }

    /**
     * Sets the udMonthlyPerdayWeekdays.
     *
     * @param int $udMonthlyPerdayWeekdays
     *
     * @return void
     */
    public function setUdMonthlyPerdayWeekdays($udMonthlyPerdayWeekdays)
    {
        $this->udMonthlyPerdayWeekdays = $udMonthlyPerdayWeekdays;
    }

    /**
     * Returns the udMonthlyPerdateDay.
     *
     * @return int $udMonthlyPerdateDay
     */
    public function getUdMonthlyPerdateDay()
    {
        return $this->udMonthlyPerdateDay;
    }

    /**
     * Sets the udMonthlyPerdateDay.
     *
     * @param int $udMonthlyPerdateDay
     *
     * @return void
     */
    public function setUdMonthlyPerdateDay($udMonthlyPerdateDay)
    {
        $this->udMonthlyPerdateDay = $udMonthlyPerdateDay;
    }

    /**
     * Returns the udMonthlyPerdateLastday.
     *
     * @return int $udMonthlyPerdateLastday
     */
    public function getUdMonthlyPerdateLastday()
    {
        return $this->udMonthlyPerdateLastday;
    }

    /**
     * Sets the udMonthlyPerdateLastday.
     *
     * @param int $udMonthlyPerdateLastday
     *
     * @return void
     */
    public function setUdMonthlyPerdateLastday($udMonthlyPerdateLastday)
    {
        $this->udMonthlyPerdateLastday = $udMonthlyPerdateLastday;
    }

    /**
     * Returns the udMonthlyEverycount.
     *
     * @return int $udMonthlyEverycount
     */
    public function getUdMonthlyEverycount()
    {
        return $this->udMonthlyEverycount;
    }

    /**
     * Sets the udMonthlyEverycount.
     *
     * @param int $udMonthlyEverycount
     *
     * @return void
     */
    public function setUdMonthlyEverycount($udMonthlyEverycount)
    {
        $this->udMonthlyEverycount = $udMonthlyEverycount;
    }

    /**
     * Returns the udYearlyEverycount.
     *
     * @return int $udYearlyEverycount
     */
    public function getUdYearlyEverycount()
    {
        return $this->udYearlyEverycount;
    }

    /**
     * Sets the udYearlyEverycount.
     *
     * @param int $udYearlyEverycount
     *
     * @return void
     */
    public function setUdYearlyEverycount($udYearlyEverycount)
    {
        $this->udYearlyEverycount = $udYearlyEverycount;
    }

    /**
     * Returns the udYearlyPerday.
     *
     * @return int $udYearlyPerday
     */
    public function getUdYearlyPerday()
    {
        return $this->udYearlyPerday;
    }

    /**
     * Sets the udYearlyPerday.
     *
     * @param int $udYearlyPerday
     *
     * @return void
     */
    public function setUdYearlyPerday($udYearlyPerday)
    {
        $this->udYearlyPerday = $udYearlyPerday;
    }

    /**
     * Returns the udYearlyPerdayWeekdays.
     *
     * @return int $udYearlyPerdayWeekdays
     */
    public function getUdYearlyPerdayWeekdays()
    {
        return $this->udYearlyPerdayWeekdays;
    }

    /**
     * Sets the udYearlyPerdayWeekdays.
     *
     * @param int $udYearlyPerdayWeekdays
     *
     * @return void
     */
    public function setUdYearlyPerdayWeekdays($udYearlyPerdayWeekdays)
    {
        $this->udYearlyPerdayWeekdays = $udYearlyPerdayWeekdays;
    }

    /**
     * Returns the udYearlyPerdayMonth.
     *
     * @return int $udYearlyPerdayMonth
     */
    public function getUdYearlyPerdayMonth()
    {
        return $this->udYearlyPerdayMonth;
    }

    /**
     * Sets the udYearlyPerdayMonth.
     *
     * @param int $udYearlyPerdayMonth
     *
     * @return void
     */
    public function setUdYearlyPerdayMonth($udYearlyPerdayMonth)
    {
        $this->udYearlyPerdayMonth = $udYearlyPerdayMonth;
    }

    /**
     * Returns the pid.
     *
     * @return int $pid
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Sets the pid.
     *
     * @param int $pid
     *
     * @return void
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }
}
