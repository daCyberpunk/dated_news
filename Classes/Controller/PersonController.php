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
 * PersonController.
 */
class PersonController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * personRepository.
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\PersonRepository
     * @inject
     */
    protected $personRepository = null;

    /**
     * action list.
     *
     * @return void
     */
    public function listAction()
    {
        $persons = $this->personRepository->findAll();
        $this->view->assign('persons', $persons);
    }

    /**
     * action show.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     *
     * @return void
     */
    public function showAction(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->view->assign('person', $person);
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
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $newPerson
     *
     * @return void
     */
    public function createAction(\FalkRoeder\DatedNews\Domain\Model\Person $newPerson)
    {
        $this->personRepository->add($newPerson);
        $this->redirect('list');
    }

    /**
     * action edit.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     * @ignorevalidation $person
     *
     * @return void
     */
    public function editAction(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->view->assign('person', $person);
    }

    /**
     * action update.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     *
     * @return void
     */
    public function updateAction(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->personRepository->update($person);
        $this->redirect('list');
    }

    /**
     * action delete.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Person $person
     *
     * @return void
     */
    public function deleteAction(\FalkRoeder\DatedNews\Domain\Model\Person $person)
    {
        $this->personRepository->remove($person);
        $this->redirect('list');
    }
}
