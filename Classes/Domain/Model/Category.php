<?php

namespace FalkRoeder\DatedNews\Domain\Model;

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
 * Category.
 */
class Category extends \GeorgRinger\News\Domain\Model\Category
{
    /**
     * textcolor.
     *
     * @var string
     */
    protected $textcolor = '';

    /**
     * backgroundcolor.
     *
     * @var string
     */
    protected $backgroundcolor = '';

    /**
     * Returns the textcolor.
     *
     * @return string $textcolor
     */
    public function getTextcolor()
    {
        return $this->textcolor;
    }

    /**
     * Sets the textcolor.
     *
     * @param string $textcolor
     *
     * @return void
     */
    public function setTextcolor($textcolor)
    {
        $this->textcolor = $textcolor;
    }

    /**
     * Returns the backgroundcolor.
     *
     * @return string $backgroundcolor
     */
    public function getBackgroundcolor()
    {
        return $this->backgroundcolor;
    }

    /**
     * Sets the backgroundcolor.
     *
     * @param string $backgroundcolor
     *
     * @return void
     */
    public function setBackgroundcolor($backgroundcolor)
    {
        $this->backgroundcolor = $backgroundcolor;
    }
}
