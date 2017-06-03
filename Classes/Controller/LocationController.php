<?php

namespace FalkRoeder\DatedNews\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017
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
