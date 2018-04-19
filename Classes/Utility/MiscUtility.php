<?php

namespace FalkRoeder\DatedNews\Utility;

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
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * This class provides misc functions for the dated News extension.
 *
 * @author Falk Röder <mail@falk-roeder.de>
 */
class MiscUtility
{

    /**
     * @param $list
     *
     * @return array
     */
    public static function shuffleAssoc($list)
    {
        if (!is_array($list)) {
            return $list;
        }

        $keys = array_keys($list);
        shuffle($keys);
        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }

        return $random;
    }

    /**
     * changeKeyCaseRecursive
     *
     * @return void
     */
    public static function changeKeyCaseRecursive($arr, $case)
    {
        return array_map(function ($item) use ($case) {
            if (is_array($item)) {
                $item =  self::changeKeyCaseRecursive($item, $case);
            }
            return $item;
        }, array_change_key_case($arr, CASE_LOWER));
    }


}
