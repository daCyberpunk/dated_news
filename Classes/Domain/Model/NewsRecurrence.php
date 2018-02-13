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
 * NewsRecurrence.
 */
class NewsRecurrence extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
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
     * eventlocation.
     *
     * @var string
     */
    protected $eventlocation = '';

    /**
     * bodytext.
     *
     * @var string
     */
    protected $bodytext = '';

    /**
     * teaser.
     *
     * @var string
     */
    protected $teaser = '';

    /**
     * modified.
     *
     * @var bool
     */
    protected $modified = false;

    /**
     * hidden.
     *
     * @var bool
     */
    protected $hidden = false;
    
    /**
     * pid.
     *
     * @var int
     */
    protected $pid ;

    /**
     * slots.
     *
     * @var int
     */
    protected $slots;


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
     * earlyBirdDate.
     *
     * @var \DateTime
     */
    protected $earlyBirdDate = '';
    
    /**
     * slotoptions.
     *
     * @var array
     */
    protected $slotoptions = [];

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
     * Empty the Locations.
     *
     * @return void
     */
    public function emptyLocations()
    {
        $loc = $this->locations->toArray();
        foreach ($loc as $object) {
            $this->removeLocation($object);
        }
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
     * Empty the Persons.
     *
     * @return void
     */
    public function emptyPersons()
    {
        $persons = $this->persons->toArray();
        foreach ($persons as $object) {
            $this->removePerson($object);
        }
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
        return (int) $this->getSlots() - $this->getReservedSlotsCount();
    }
    
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application>
     */
    protected $application;

    /**
     * parentEvent.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News>
     * @cascade remove
     */
    protected $parentEvent = null;

    /**
     * __construct.
     */
    public function __construct()
    {
        //Do not remove the next line: It would break the functionality
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead.
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->parentEvent = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->locations = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->persons = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
     * Returns the bodytext.
     *
     * @return string $bodytext
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Sets the bodytext.
     *
     * @param string $bodytext
     *
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * Returns the teaser.
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Sets the teaser.
     *
     * @param string $teaser
     *
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Returns the modified.
     *
     * @return bool $modified
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Sets the modified.
     *
     * @param bool $modified
     *
     * @return void
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
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
     * Sets the slotoptions.
     *
     * @param bool $slotoptions
     *
     * @return void
     */
    public function setSlotoptions($slotoptions)
    {
        $this->slotoptions = $slotoptions;
    }

    /**
     * Returns the boolean state of modified.
     *
     * @return bool
     */
    public function isModified()
    {
        return (bool)$this->modified;
    }

    /**
     * Returns the hidden.
     *
     * @return bool $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Sets the hidden.
     *
     * @param bool $hidden
     *
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Returns the boolean state of hidden.
     *
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
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
     * getReservedSlotsCount
     *
     * @return int
     */
    public function getReservedSlotsCount()
    {
        return count($this->getApplication()->toArray());

    }


    
    /**
     * Adds a News.
     *
     * @param \GeorgRinger\News\Domain\Model\News $parentEvent
     *
     * @return void
     */
    public function addParentEvent(\GeorgRinger\News\Domain\Model\News $parentEvent)
    {
        $this->parentEvent->attach($parentEvent);
    }

    /**
     * Removes a News.
     *
     * @param \GeorgRinger\News\Domain\Model\News $parentEventToRemove The News to be removed
     *
     * @return void
     */
    public function removeParentEvent(\GeorgRinger\News\Domain\Model\News $parentEventToRemove)
    {
        $this->parentEvent->detach($parentEventToRemove);
    }

    /**
     * Returns the parentEvent.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News> $parentEvent
     */
    public function getParentEvent()
    {
        return $this->parentEvent;
    }

    /**
     * Sets the parentEvent.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News> $parentEvent
     *
     * @return void
     */
    public function setParentEvent(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $parentEvent)
    {
        $this->parentEvent = $parentEvent;
    }
}
