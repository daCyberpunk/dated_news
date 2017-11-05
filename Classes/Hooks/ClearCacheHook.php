<?php
namespace FalkRoeder\DatedNews\Hooks;

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

use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ClearCacheHook
 */
class ClearCacheHook
{
    /**
     * @param array $params
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function clearJsCache(array $params, DataHandler &$pObj)
    {
        if (!isset($params['cacheCmd'])) {
            return;
        }
        switch ($params['cacheCmd']) {
            case 'all':
                GeneralUtility::rmdir(
                    PATH_site . 'typo3temp/assets/datednews',
                    true
                );
                break;
            default:
        }
    }
}
