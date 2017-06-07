<?php

namespace FalkRoeder\DatedNews\Domain\Model;

/***
 *
 * This file is part of the "Dated News" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
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
     * Returns the boolean state of modified.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->modified;
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
