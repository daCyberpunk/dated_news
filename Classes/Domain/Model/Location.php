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
 * Location.
 */
class Location extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $name = '';

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
     * address.
     *
     * @var string
     */
    protected $address = '';

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
     * phone.
     *
     * @var string
     */
    protected $phone = '';

    /**
     * email.
     *
     * @var string
     */
    protected $email = '';

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
}
