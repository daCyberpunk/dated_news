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

use FalkRoeder\DatedNews\Domain\Model\Application;

/**
 * ApplicationController.
 */
class ApplicationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * applicationRepository.
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository = null;

    /**
     * @var $persistenceManager \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    /**
     * initializeAction
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
    }

    /**
     * action.
     *
     * @return void
     */
    public function cancelAction()
    {
        $uid = $this->request->getArgument('application')['__identity'];
        $application = $this->applicationRepository->findByUid($uid);
        $application->setCancelled(true);
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();
        return true;
    }

    /**
     * action.
     *
     * @return void
     */
    public function applicateAction()
    {
        $uid = $this->request->getArgument('application')['__identity'];
        $application = $this->applicationRepository->findByUid($uid);
        $application->setConfirmed(false);
        $this->applicationRepository->update($application);
        $this->persistenceManager->persistAll();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($application,'ApplicationController:42');
        return true;
    }
}
