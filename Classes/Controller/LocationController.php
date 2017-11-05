<?php

namespace FalkRoeder\DatedNews\Controller;

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
 * LocationController.
 */
class LocationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * locationRepository.
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\LocationRepository
     * @inject
     */
    protected $locationRepository = null;

    /**
     * action list.
     *
     * @return void
     */
    public function listAction()
    {
        $locations = $this->locationRepository->findAll();
        $this->view->assign('locations', $locations);
    }

    /**
     * action show.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     *
     * @return void
     */
    public function showAction(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->view->assign('location', $location);
    }

    /**
     * action new.
     *
     * @return void
     */
    public function newAction()
    {
    }

    /**
     * action create.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $newLocation
     *
     * @return void
     */
    public function createAction(\FalkRoeder\DatedNews\Domain\Model\Location $newLocation)
    {
        $this->locationRepository->add($newLocation);
        $this->redirect('list');
    }

    /**
     * action edit.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     * @ignorevalidation $location
     *
     * @return void
     */
    public function editAction(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->view->assign('location', $location);
    }

    /**
     * action update.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     *
     * @return void
     */
    public function updateAction(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->locationRepository->update($location);
        $this->redirect('list');
    }

    /**
     * action delete.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Location $location
     *
     * @return void
     */
    public function deleteAction(\FalkRoeder\DatedNews\Domain\Model\Location $location)
    {
        $this->locationRepository->remove($location);
        $this->redirect('list');
    }
}
