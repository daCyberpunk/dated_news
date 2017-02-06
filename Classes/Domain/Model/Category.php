<?php
namespace FalkRoeder\DatedNews\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Falk Röder <mail@falk-roeder.de>
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
 * Category
 */
class Category extends \GeorgRinger\News\Domain\Model\Category
{
    
    /**
     * textcolor
     *
     * @var string
     */
    protected $textcolor = '';
    
    /**
     * backgroundcolor
     *
     * @var string
     */
    protected $backgroundcolor = '';
    

    
    /**
     * Returns the textcolor
     *
     * @return string $textcolor
     */
    public function getTextcolor()
    {
        return $this->textcolor;
    }
    
    /**
     * Sets the textcolor
     *
     * @param string $textcolor
     * @return void
     */
    public function setTextcolor($textcolor)
    {
        $this->textcolor = $textcolor;
    }


    
    /**
     * Returns the backgroundcolor
     *
     * @return string $backgroundcolor
     */
    public function getBackgroundcolor()
    {
        return $this->backgroundcolor;
    }
    
    /**
     * Sets the backgroundcolor
     *
     * @param string $backgroundcolor
     * @return void
     */
    public function setBackgroundcolor($backgroundcolor)
    {
        $this->backgroundcolor = $backgroundcolor;
    }



}