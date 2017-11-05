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
 * Person.
 */
class Person extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * name.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $name = '';

    /**
     * surname.
     *
     * @var string
     * @validate NotEmpty
     */
    protected $surname = '';

    /**
     * title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * email.
     *
     * @var string
     */
    protected $email = '';

    /**
     * images.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $images = null;

    /**
     * Returns the images.
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets the images.
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     *
     * @return void
     */
    public function setImages($images)
    {
        $this->images = $images;
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
