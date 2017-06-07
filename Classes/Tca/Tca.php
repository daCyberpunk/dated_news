<?php

namespace FalkRoeder\DatedNews\Tca;

/***************************************************************
 *  Copyright notice
 *  (c) 2013 Jo Hasenau <info@cybercraft.de>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class PreHeaderRenderHook.
 */
class Tca
{
    /**
     * @param array $arg
     */
    public function injectJs()
    {
        $js = file_get_contents(ExtensionManagementUtility::extPath('dated_news').'Resources/Public/Backend/JavaScript/datednews_reccurence_conf.js');

        return '<script>'.$js.'</script>';
    }
}
