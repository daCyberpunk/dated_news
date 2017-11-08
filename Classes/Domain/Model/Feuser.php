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
 * The FE User
 */
class Feuser extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser {

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application>
     */
    protected $applications;

    /**
     * @var int
     */
    protected $pid;

	/**
	 * __construct
	 */
	public function __construct() {
		$this->initStorageObjects();
	}
  
	/**
	 * Initializes all ObjectStorage properties
	 * Do not modify this method!
	 * It will be rewritten on each save in the extension builder
	 * You may modify the constructor of this class instead
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		$this->applications = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
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
	 * Adds a Application
	 *
	 * @param \FalkRoeder\DatedNews\Domain\Model\Application $application
	 * @return void
	 */
	public function addApplication(\FalkRoeder\DatedNews\Domain\Model\Application $application) {
		$this->applications->attach($application);
	}

	/**
	 * Removes a Application
	 *
	 * @param \FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove The Application to be removed
	 * @return void
	 */
	public function removeApplication(\FalkRoeder\DatedNews\Domain\Model\Application $applicationToRemove) {
		$this->applications->detach($applicationToRemove);
	}

	/**
	 * Returns the applications
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $applications
	 */
	public function getApplications() {
		return $this->applications;
	}

	/**
	 * Sets the applications
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\FalkRoeder\DatedNews\Domain\Model\Application> $applications
	 * @return void
	 */
	public function setApplications(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $applications) {
		$this->applications = $applications;
	}
}