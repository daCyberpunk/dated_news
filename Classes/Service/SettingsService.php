<?php

namespace FalkRoeder\DatedNews\Service;

/***
 *
 * This file is part of the "dreipc_ca" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018
 *
 * Author Falk Röder <mail@falk-roeder.de>
 *
 ***/

use FalkRoeder\DatedNews\Utility\MiscUtility;
use TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * SettingsService.
 */
class SettingsService extends BackendConfigurationManager
{
    /**
     * @var mixed
     */
    protected $settings = null;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Core\TypoScript\TypoScriptService
     */
    protected $typoScriptService;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $this->objectManager->get(BackendConfigurationManager::class);
        $this->typoScriptService = $this->objectManager->get(TypoScriptService::class);
    }

    /**
     * Returns all settings.
     *
     * @return array
     */
    public function getSettings($changeCase = true)
    {
        if ($this->settings === null) {
            $settings = $this->configurationManager->getTypoScriptSetup();
            $settings = $settings['plugin.'];
            $this->settings = $this->typoScriptService->convertTypoScriptArrayToPlainArray(
                $changeCase ? MiscUtility::changeKeyCaseRecursive($settings, CASE_LOWER) : $settings
            );
        }
        return $this->settings;
    }

    /**
     * templateIncluded
     *
     * @return bool
     */
    public function templateIncluded()
    {
        $settings = $this->configurationManager->getTypoScriptSetup();
        return isset($settings['plugin.']['tx_dated_news.']) && !empty($settings['plugin.']['tx_dated_news.']);
    }

    /**
     * Returns the settings at path $path, which is separated by ".",
     * e.g. "pages.uid".
     * "pages.uid" would return $this->settings['pages']['uid'].
     *
     * If the path is invalid or no entry is found, false is returned.
     *
     * @param string $path
     * @return mixed
     */
    public function getByPath($path, $changeCase = true)
    {   $path =  $changeCase ? strtolower($path) : $path;
        return \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getPropertyPath($this->getSettings($changeCase), $path);
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath($value)
    {
        $a = ArrayUtility::filterByValueRecursive($value, $this->getSettings());
        $flatten = ArrayUtility::flatten($a);

        return key($flatten);
    }

    /**
     * stepBackInPath
     *
     * @return string
     */
    public function stepBackInPath($path, $steps = 0)
    {
        $steps = substr_count($path, '.')+1 - $steps;
        $keyArray = explode('.', $path);
        $i = 0;
        $newPath = '';
        while ($i < $steps) {
            if ($i > 0) {
                $newPath .= '.';
            }
            $newPath .= $keyArray[$i];
            $i++;
        }
        return $newPath;
    }

    /**
     * keyAboveValueBySteps
     *
     * @return void
     */
    public function keyAboveValueBySteps($value, $steps)
    {
        $path = $this->stepBackInPath($this->getPath(strtolower($value)), $steps);

        $path = explode('.', $path);
        return array_values(array_slice($path, -1))[0];
    }

    /**
     * getSibling
     *
     * @return void
     */
    public function getSibling($value, $siblingKey)
    {
        $path = $this->stepBackInPath($this->getPath(strtolower($value)), 1);
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->getByPath('config.abbreviations.className'),'SettingsService:142');

        return $this->getByPath($path . '.' . strtolower($siblingKey));
    }
}
