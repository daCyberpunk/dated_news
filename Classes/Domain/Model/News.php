<?php
namespace FalkRoeder\DatedNews\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Falk Röder <mail@falk-roeder.de>
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
 * News
 */
class News extends \GeorgRinger\News\Domain\Model\News
{

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application>
     * 
     */
    protected $application;
    
    /**
     * showincalendar
     *
     * @var boolean
     */
    protected $showincalendar = FALSE;

    /**
     * enableApplication
     *
     * @var boolean
     */
    protected $enableApplication = FALSE;
    
    /**
     * fulltime
     *
     * @var boolean
     */
    protected $fulltime = FALSE;
    
    /**
     * textcolor
     *
     * @var string
     */
    protected $textcolor = '';
    
    /**
     * backgroundcolor
     *
     * @var string
     */
    protected $backgroundcolor = '';
    
    /**
     * eventstart
     *
     * @var \DateTime
     */
    protected $eventstart = NULL;
    
    /**
     * eventend
     *
     * @var \DateTime
     */
    protected $eventend = NULL;

    /**
     * eventtype
     *
     * @var string
     */
    protected $eventtype = '';

    /**
     * eventlocation
     *
     * @var string
     */
    protected $eventlocation = '';

    /**
     * slots
     * 
     * @var int
     */
    protected $slots;

    /**
     * slotsFree
     *
     * @var int
     */
    protected $slotsFree;

    /**
     * price
     *
     * @var string
     */
    protected $price = '';

    /**
     * earlyBirdPrice
     *
     * @var string
     */
    protected $earlyBirdPrice = '';

    /**
     * targetgroup
     *
     * @var string
     */
    protected $targetgroup = '';
    
    /**
     * locations
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location>
     * @lazy
     */
    protected $locations = null;

    /**
     * persons
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person>
     * @lazy
     */
    protected $persons = null;

    

    /**
     * Returns the price
     *
     * @return string $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the price
     *
     * @param string $price
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
    
    /**
     * Returns the targetgroup
     *
     * @return string $targetgroup
     */
    public function getTargetgroup()
    {
        return $this->targetgroup;
    }

    /**
     * Sets the targetgroup
     *
     * @param string $targetgroup
     * @return void
     */
    public function setTargetgroup($targetgroup)
    {
        $this->targetgroup = $targetgroup;
    }

    /**
     * Returns the earlyBirdPrice
     *
     * @return string $earlyBirdPrice
     */
    public function getEarlyBirdPrice()
    {
        return $this->earlyBirdPrice;
    }

    /**
     * Sets the earlyBirdPrice
     *
     * @param string $earlyBirdPrice
     * @return void
     */
    public function setEarlyBirdPrice($earlyBirdPrice)
    {
        $this->earlyBirdPrice = $earlyBirdPrice;
    }

    /**
     * Adds a Location
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     * @return void
     */
    public function addLocation(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->locations->attach($location);
    }

    /**
     * Removes a Location
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $locationToRemove The Location to be removed
     * @return void
     */
    public function removeLocation(\FalkRoeder\DatedNews\Domain\Model\Location $locationToRemove)
    {
        $this->locations->detach($locationToRemove);
    }

    /**
     * Returns the locations
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location> $locations
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Sets the locations
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Location> $locations
     * @return void
     */
    public function setLocations(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $locations)
    {
        $this->locations = $locations;
    }

    /**
     * Adds a Person
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     * @return void
     */
    public function addPerson(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->persons->attach($person);
    }

    /**
     * Removes a Person
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $personToRemove The Person to be removed
     * @return void
     */
    public function removePerson(\FalkRoeder\DatedNews\Domain\Model\Person $personToRemove)
    {
        $this->persons->detach($personToRemove);
    }

    /**
     * Returns the persons
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person> $persons
     */
    public function getPersons()
    {
        return $this->persons;
    }

    /**
     * Sets the persons
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Person> $persons
     * @return void
     */
    public function setPersons(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $persons)
    {
        $this->persons = $persons;
    }
    
    /**
     * Returns the showincalendar
     *
     * @return boolean $showincalendar
     */
    public function getShowincalendar()
    {
        return $this->showincalendar;
    }
    
    /**
     * Sets the showincalendar
     *
     * @param boolean $showincalendar
     * @return void
     */
    public function setShowincalendar($showincalendar)
    {
        $this->showincalendar = $showincalendar;
    }
    
    /**
     * Returns the boolean state of showincalendar
     *
     * @return boolean
     */
    public function isShowincalendar()
    {
        return $this->showincalendar;
    }
    
    
    /**
     * Returns the enableApplication
     *
     * @return boolean $enableApplication
     */
    public function getEnableApplication()
    {
        return $this->enableApplication;
    }
    
    /**
     * Sets the enableApplication
     *
     * @param boolean $enableApplication
     * @return void
     */
    public function setEnableApplication($enableApplication)
    {
        $this->enableApplication = $enableApplication;
    }
    
    /**
     * Returns the boolean state of enableApplication
     *
     * @return boolean
     */
    public function isEnableApplication()
    {
        return $this->enableApplication;
    }
    
    /**
     * Returns the fulltime
     *
     * @return boolean $fulltime
     */
    public function getFulltime()
    {
        return $this->fulltime;
    }
    
    /**
     * Sets the fulltime
     *
     * @param boolean $fulltime
     * @return void
     */
    public function setFulltime($fulltime)
    {
        $this->fulltime = $fulltime;
    }
    
    /**
     * Returns the boolean state of fulltime
     *
     * @return boolean
     */
    public function isFulltime()
    {
        return $this->fulltime;
    }
    
    /**
     * Returns the eventstart
     *
     * @return \DateTime $eventstart
     */
    public function getEventstart()
    {
        return $this->eventstart;
    }
    
    /**
     * Sets the eventstart
     *
     * @param \DateTime $eventstart
     * @return void
     */
    public function setEventstart(\DateTime $eventstart)
    {
        $this->eventstart = $eventstart;
    }
    
    /**
     * Returns the eventend
     *
     * @return \DateTime $eventend
     */
    public function getEventend()
    {
        return $this->eventend;
    }
    
    /**
     * Sets the eventend
     *
     * @param \DateTime $eventend
     * @return void
     */
    public function setEventend(\DateTime $eventend)
    {
        $this->eventend = $eventend;
    }

    /**
     * Returns the eventtype
     *
     * @return \DateTime $eventtype
     */
    public function getEventtype()
    {
        return $this->eventtype;
    }

    /**
     * Sets the eventtype
     *
     * @param \DateTime $eventtype
     * @return void
     */
    public function setEventtype(\DateTime $eventtype)
    {
        $this->eventtype = $eventtype;
    }
    
    /**
     * Returns the eventlocation
     *
     * @return string $eventlocation
     */
    public function getEventlocation()
    {
        return $this->eventlocation;
    }
    
    /**
     * Sets the eventlocation
     *
     * @param string $eventlocation
     * @return void
     */
    public function setEventlocation($eventlocation)
    {
        $this->eventlocation = $eventlocation;
    }
    
    /**
     * Returns the textcolor
     *
     * @return string $textcolor
     */
    public function getTextcolor()
    {
        return $this->textcolor;
    }
    
    /**
     * Sets the textcolor
     *
     * @param string $textcolor
     * @return void
     */
    public function setTextcolor($textcolor)
    {
        $this->textcolor = $textcolor;
    }

    /**
     * Returns the slots
     *
     * @return int $slots
     */
    public function getSlots()
    {
        return $this->slots;
    }

    /**
     * Sets the slots
     *
     * @param int $slots
     * @return void
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;
    }

    /**
     * Sets the slotsFree
     *
     * @param int $slotsFree
     * @return void
     */
    public function setSlotsFree($slotsFree)
    {
        $this->slotsFree = $slotsFree;
    }

    /**
     * Returns the slotsFree
     *
     * @return int $slotsFree
     */
    public function getSlotsFree()
    {
        return $this->slotsFree;
    }
    
    /**
     * Returns the backgroundcolor
     *
     * @return string $backgroundcolor
     */
    public function getBackgroundcolor()
    {
        return $this->backgroundcolor;
    }
    
    /**
     * Sets the backgroundcolor
     *
     * @param string $backgroundcolor
     * @return void
     */
    public function setBackgroundcolor($backgroundcolor)
    {
        $this->backgroundcolor = $backgroundcolor;
    }

    /**
     * Adds a Application
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $application
     * @return void
     */
    public function addApplication(\FalkRoeder\DatedNews\Domain\Model\Application $application) {
        $this->application->attach($application);
    }

    /**
     * Removes a Application
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove The Application to be removed
     * @return void
     */
    public function removeApplication(\FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove) {
        $this->application->detach($applicationToRemove);
    }

    /**
     * Returns the application
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $application
     */
    public function getApplication() {
        return $this->application;
    }

    /**
     * Sets the application
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $application
     * @return void
     */
    public function setApplication(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $application) {
        $this->application = $application;
    }

}