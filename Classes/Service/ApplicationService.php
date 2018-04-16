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


/**
 * ApplicationService.
 */
class ApplicationService
{
    /**
     * getEventTotalCosts
     *
     * @param $news
     * @param $earlyBirdDate
     * @param $reservedSlots
     * @return float|int
     */
    public function getEventTotalCosts($news, $earlyBirdDate, $reservedSlots)
    {
        if ($news->getEarlyBirdPrice() != '' && $earlyBirdDate !== null) {
            $earlyBirdDate->setTime(0, 0, 0);

            $today = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
            $today->setTime(0, 0, 0);

            if ($earlyBirdDate >= $today) {
                $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getEarlyBirdPrice()));
            } else {
                $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getPrice()));
            }
        } else {
            $costs = $reservedSlots * floatval(str_replace(',', '.', $news->getPrice()));
        }
        return $costs;
    }
}


