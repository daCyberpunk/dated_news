<?php

namespace FalkRoeder\DatedNews\Domain\Model;

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
 * Application.
 */
class Application extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * crdate.
     *
     * @var string
     */
    protected $crdate = '';

    /**
     * title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * applicationTitle.
     *
     * @var string
     */
    protected $applicationTitle = '';

    /**
     * name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * surname.
     *
     * @var string
     */
    protected $surname = '';

    /**
     * email.
     *
     * @var string
     */
    protected $email = '';

    /**
     * reservedSlots.
     *
     * @var int
     */
    protected $reservedSlots;

    /**
     * formTimestamp.
     *
     * @var int
     */
    protected $formTimestamp;

    /**
     * company.
     *
     * @var string
     */
    protected $company = '';

    /**
     * address.
     *
     * @var string
     */
    protected $address = '';

    /**
     * address2.
     *
     * @var string
     */
    protected $address2 = '';

    /**
     * zip.
     *
     * @var string
     */
    protected $zip = '';

    /**
     * city.
     *
     * @var string
     */
    protected $city = '';

    /**
     * country.
     *
     * @var string
     */
    protected $country = '';

    /**
     * costs.
     *
     * @var string
     */
    protected $costs = '';

    /**
     * Returns the costs.
     *
     * @return string $costs
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * Sets the costs.
     *
     * @param string $costs
     *
     * @return void
     */
    public function setCosts($costs)
    {
        $this->costs = $costs;
    }

    /**
     * confirmed.
     *
     * @var bool
     */
    protected $confirmed = false;

    /**
     * Returns the confirmed.
     *
     * @return bool $confirmed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * Sets the confirmed.
     *
     * @param bool $confirmed
     *
     * @return void
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = $confirmed;
    }

    /**
     * Returns the boolean state of confirmed.
     *
     * @return bool
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * termsAccept.
     *
     * @var bool
     */
    protected $termsAccept = '';

    /**
     * Returns the termsAccept.
     *
     * @return bool $termsAccept
     */
    public function getTermsAccept()
    {
        return $this->termsAccept;
    }

    /**
     * Sets the termsAccept.
     *
     * @param bool $termsAccept
     *
     * @return void
     */
    public function setTermsAccept($termsAccept)
    {
        $this->termsAccept = $termsAccept;
    }

    /**
     * message.
     *
     * @var string
     */
    protected $message = '';

    /**
     * Returns the message.
     *
     * @return string $message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Sets the message.
     *
     * @param string $message
     *
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * taxid.
     *
     * @var string
     */
    protected $taxid = '';

    /**
     * Returns the taxid.
     *
     * @return string $taxid
     */
    public function getTaxid()
    {
        return $this->taxid;
    }

    /**
     * Sets the taxid.
     *
     * @param string $taxid
     *
     * @return void
     */
    public function setTaxid($taxid)
    {
        $this->taxid = $taxid;
    }

    /**
     * mobile.
     *
     * @var string
     */
    protected $mobile = '';

    /**
     * Returns the mobile.
     *
     * @return string $mobile
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Sets the mobile.
     *
     * @param string $mobile
     *
     * @return void
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * phone.
     *
     * @var string
     */
    protected $phone = '';

    /**
     * Returns the phone.
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone.
     *
     * @param string $phone
     *
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the reservedSlots.
     *
     * @return int $reservedSlots
     */
    public function getReservedSlots()
    {
        return $this->reservedSlots;
    }

    /**
     * Sets the reservedSlots.
     *
     * @param int $reservedSlots
     *
     * @return void
     */
    public function setReservedSlots($reservedSlots)
    {
        $this->reservedSlots = $reservedSlots;
    }

    /**
     * Returns the formTimestamp.
     *
     * @return int $formTimestamp
     */
    public function getFormTimestamp()
    {
        return $this->formTimestamp;
    }

    /**
     * Sets the formTimestamp.
     *
     * @param int $formTimestamp
     *
     * @return void
     */
    public function setFormTimestamp($formTimestamp)
    {
        $this->formTimestamp = $formTimestamp;
    }

    /**
     * events.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News>
     * @cascade remove
     */
    protected $events = null;

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
        $this->events = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the crdate.
     *
     * @return string $crdate
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Sets the crdate.
     *
     * @param string $crdate
     *
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
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
     * _languageUid.
     *
     * @var int
     */
    protected $_languageUid;

    /**
     * Get sys language.
     *
     * @return int
     */
    public function getSysLanguageUid()
    {
        return $this->_languageUid;
    }

    /**
     * Set sys language.
     *
     * @param int $sysLanguageUid
     *
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Returns the title.
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title.
     *
     * @param string $title
     *
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the applicationTitle.
     *
     * @return string $applicationTitle
     */
    public function getApplicationTitle()
    {
        return $this->applicationTitle;
    }

    /**
     * Sets the applicationTitle.
     *
     * @param string $applicationTitle
     *
     * @return void
     */
    public function setApplicationTitle($applicationTitle)
    {
        $this->applicationTitle = $applicationTitle;
    }

    /**
     * Returns the name.
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the surname.
     *
     * @return string $surname
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Sets the surname.
     *
     * @param string $surname
     *
     * @return void
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Returns the email.
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the company.
     *
     * @return string $company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Sets the company.
     *
     * @param string $company
     *
     * @return void
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Returns the address.
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address.
     *
     * @param string $address
     *
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns the address2.
     *
     * @return string $address2
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * Sets the address2.
     *
     * @param string $address2
     *
     * @return void
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
    }

    /**
     * Returns the zip.
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip.
     *
     * @param string $zip
     *
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the city.
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city.
     *
     * @param string $city
     *
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the country.
     *
     * @return string $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country.
     *
     * @param string $country
     *
     * @return void
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Adds a News.
     *
     * @param \GeorgRinger\News\Domain\Model\News $event
     *
     * @return void
     */
    public function addEvent(\GeorgRinger\News\Domain\Model\News $event)
    {
        $this->events->attach($event);
    }

    /**
     * Removes a News.
     *
     * @param \GeorgRinger\News\Domain\Model\News $eventToRemove The News to be removed
     *
     * @return void
     */
    public function removeEvent(\GeorgRinger\News\Domain\Model\News $eventToRemove)
    {
        $this->events->detach($eventToRemove);
    }

    /**
     * Returns the events.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News> $events
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Sets the events.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $events
     *
     * @internal param $ \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\GeorgRinger\News\Domain\Model\News> $events
     */
    public function setEvents(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $events)
    {
        $this->events = $events;
    }
}
