<?php

namespace FalkRoeder\DatedNews\Service;

/***
 *
 * This file is part of the "" Extension for TYPO3 CMS.
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * PluginService.
 */
class PluginService
{
    /**
     * @param int $id
     *
     * @return array
     */
    public function getPluginConfiguration($id)
    {
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
        $piFlexformSettings = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('pi_flexform', 'tt_content', 'uid = ' . $id);
        $ffs = GeneralUtility::makeInstance(FlexFormService::class);
        return $ffs->convertFlexFormContentToArray($piFlexformSettings[0]['pi_flexform']);
    }
}


