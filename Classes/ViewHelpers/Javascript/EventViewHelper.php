<?php

namespace FalkRoeder\DatedNews\ViewHelpers\Javascript;

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
 *  The GNU General Public License can be found 	at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * EventViewHelper.
 *
 * @author Falk Röder
 * @inject
 */
class EventViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function injectPageRenderer(\TYPO3\CMS\Core\Page\PageRenderer $pageRenderer)
    {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     *
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * Returns TRUE if what we are outputting may be cached.
     *
     * @return bool
     */
    protected function isCached()
    {
        $userObjType = $this->configurationManager->getContentObject()->getUserObjectType();

        return $userObjType !== \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::OBJECTTYPE_USER_INT;
    }

    /**
     * Arguments initialization.
     *
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('qtip', 'string', 'rendered qtip Partial');
        $this->registerArgument('strftime', 'bool', 'if true, the strftime is used instead of date()', false, true);
        $this->registerArgument('item', 'mixed', 'newsitem');
        $this->registerArgument('recurrence', 'mixed', 'recurrence of event');
        $this->registerArgument('iterator', 'mixed', 'iterator');
        $this->registerArgument('id', 'integer', 'Uid of Content Element');
        $this->registerArgument('settings', 'array', 'plugin settings');
        $this->registerArgument('compress', 'boolean', 'Compress argument - see PageRenderer documentation', false, true);
    }

    /**
     * @throws Exception
     *
     * @return void
     */
    public function render()
    {
        $item = $this->arguments['item'];
        $recurrence = $this->arguments['recurrence'];
        $strftime = $this->arguments['strftime'];
        $qtip = ',qtip: \''.trim(preg_replace("/\r|\n/", '', $this->renderQtip($this->arguments['settings'], $item, $recurrence))).'\'';
        $calendarUid = $this->arguments['id'];
        $detailPid = $this->arguments['settings']['detailPid'];
        $timeZone = new \DateTimeZone("Europe/Berlin");

        if(NULL !== $recurrence) {
            $start = $recurrence->getEventstart()/*->setTimezone($timeZone)*/;

            $end = $recurrence->getEventend();
            $uid = $recurrence->getUid();
        } else {
            $start = $item->getEventstart();
            $end = $item->getEventend();
            $uid = $item->getUid();
        }



        
        $title = $item->getTitle();
        $fulltime = $item->getFulltime();
        $tags = $item->getTags();
        $categories = $item->getCategories();
        $filterTags = '';
        $color = trim($item->getBackgroundcolor());
        $textcolor = trim($item->getTextcolor());

        if ($color === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getBackgroundcolor());
                if ($tempColor !== '') {
                    $color = $tempColor;
                }
            }
        }
        if ($textcolor === '') {
            foreach ($categories as $category) {
                $tempColor = trim($category->getTextcolor());
                if ($tempColor !== '') {
                    $textcolor = $tempColor;
                }
            }
        }

        $i = 0;
        foreach ($tags as $key => $value) {
            $i++;
            if ($i === 1) {
                $filterTags = $value->getTitle();
            } else {
                $filterTags .= ','.$value->getTitle();
            }
        }

        foreach ($categories as $key => $value) {
            $i++;
            if ($i === 1) {
                $filterTags = $value->getTitle();
            } else {
                $filterTags .= ','.$value->getTitle();
            }
        }

        if ($start === null || $uid === null) {
            return;
        }

        date_default_timezone_set('UTC');
        if (!$start instanceof \DateTime) {
            try {
                $start = new \DateTime($start);
            } catch (\Exception $exception) {
                throw new Exception('"'.$start.'" could not be parsed by DateTime constructor.', 1438925934);
            }
        }

        if (!$end instanceof \DateTime && $end !== null) {
            try {
                $end = new \DateTime($end);
            } catch (\Exception $exception) {
                throw new Exception('"'.$end.'" could not be parsed by DateTime constructor.', 1438925934);
            }
        }
        $formattedEnd = '';
        if ($strftime) {
            $formattedStart = strftime('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
            if ($end !== null) {
                $formattedEnd = strftime('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
            }
        } else {
            $formattedStart = date('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
            if ($end !== null) {
                $formattedEnd = date('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
            }
        }

        $allDay = ',allDay: false';
        if ($fulltime === true) {
            $allDay = ',allDay: true';
        }

        $uri = '';
        if ($detailPid) {
            $uriBuilder = $this->controllerContext->getUriBuilder();
            $detailUri = $uriBuilder
                ->reset()
                ->setTargetPageUid($detailPid)
                ->setUseCacheHash(true)
                ->setArguments(['tx_news_pi1' => ['controller' => 'News', 'action' => 'detail', 'news' => $item->getUid()]])
                ->setCreateAbsoluteUri(true)
                ->buildFrontendUri();
            $uri = 'url: "'.$detailUri.'",';
        }

        $js = <<<EOT
                window.onload = function () {
                   
                };
				if(!eventscal){
					var eventscal= [];
				}
				if(!eventscal.hasOwnProperty("newsCalendarEvent_$calendarUid")){
					eventscal["newsCalendarEvent_$calendarUid"] = [];
				}
				eventscal["newsCalendarEvent_$calendarUid"]['Event_$uid'] = {
			    events: [
			        {
			    	    title: '$title',
			    	    $uri
			            start: '$formattedStart',
			            end: '$formattedEnd',
			            className: 'Event_$uid'
			            $allDay
			            $qtip 
			        }
			    ],
			    
			    color: '$color',
			    textColor: '$textcolor'
			};
				if(!newsCalendarTags){
					var newsCalendarTags = [];
				};

				var tempTags = '$filterTags';
					if(tempTags.length > 0){
						tempTags = tempTags.split(',');
						for (var key in tempTags) {
							  if (tempTags.hasOwnProperty(key)) {
							  	if(!newsCalendarTags[tempTags[key]]){
									newsCalendarTags[tempTags[key]] = [];
								}
								newsCalendarTags[tempTags[key]].push($uid);
							  }
						}
					}
				
EOT;
        if ($this->isCached()) {
            $this->pageRenderer->addJsFooterInlineCode(
                'dated_newsEvent'.$uid.$calendarUid,
                $js,
                $this->arguments['compress'],
                false
            );
        } else {
            // additionalFooterData not possible in USER_INT
            $GLOBALS['TSFE']->additionalFooterData[md5('dated_newsEvent'.$uid.$calendarUid)] = GeneralUtility::wrapJS($js);
        }
    }

    /**
     * @param $settings
     * @param $newsItem
     *
     * @return string html output of Qtip.html
     */
    public function renderQtip($settings, $newsItem, $recurrence = null)
    {

        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $qtip = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $qtip->setTemplatePathAndFilename(ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials/Calendar/Qtip.html');
        /*$qtip->setLayoutRootPaths(array(
            'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Layouts'
        ));*/
        $qtip->setPartialRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials',
        ]);
        $assignedValues = [
            'newsItem' => $newsItem,
            'settings' => $settings,
        ];
        if(NULL !== $recurrence){
            $assignedValues['recurrence'] = $recurrence;
        }
        $qtip->assignMultiple($assignedValues);

        return $qtip->render();
    }
}
