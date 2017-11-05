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
     * action.
     *
     * @return void
     */
    public function Action()
    {
    }
}
