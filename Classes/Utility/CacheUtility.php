<?php

namespace FalkRoeder\DatedNews\Utility;

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


/**
 * CacheUtility.
 */
class CacheUtility
{

    /**
     * flushCacheForNews
     *
     * @return void
     */
    public static function flushCacheForNews($uid)
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')
            ->getCache('cache_pages')->flushByTag('tx_news_uid_' . $uid);

//        $cacheManager->flushCachesByTag('tx_news_uid_' . $uid);
    }
}


