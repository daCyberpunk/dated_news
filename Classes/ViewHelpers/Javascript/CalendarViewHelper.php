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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * CalendarViewHelper.
 *
 * @author Falk Röder
 */
class CalendarViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
        $this->registerArgument('settings', 'mixed', 'settings');
        $this->registerArgument('id', 'integer', 'Uid of Content Element');
        $this->registerArgument('compress', 'boolean', 'Compress argument - see PageRenderer documentation', false, true);
        $this->registerArgument('newsUids', 'string', 'uids of news records which should be demanded on ajax request', true);
    }

    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     *
     * @return string the needed html markup inklusive javascript
     *
     * @internal param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
     */
    public function render()
    {
        $settings = $this->arguments['settings'];
        $tsSettings = $settings['dated_news'];
        $uid = $this->arguments['id'];
        $newsUids = $this->arguments['newsUids'];

        $timeZone = new \DateTimeZone("Europe/Berlin");
        $dt = new \DateTime();
        $dt->setTimezone($timeZone);

        //build all options
        $headerFooter = $this->buildHeaderFooterOption(
            $tsSettings['titlePosition'],
            $tsSettings['switchableViewsPosition'],
            $tsSettings['nextPosition'],
            $tsSettings['prevPosition'],
            $tsSettings['todayPosition'],
            $settings['switchableViews']
            );
        $eventRenderer = '';
        $hasQtips = '';
        if ($settings['qtips'] == '1') {
            $hasQtips = 'has-qtips';
            $eventRenderer = $this->buildEventRendererOption($tsSettings['tooltipPreStyle'], '.calendar_'.$uid);
        }
        $timeFormat = $this->buildTimeFormatOption($tsSettings['twentyfourhour']);
        $buttonText = $this->getButtonText();
        $defaultView = 'defaultView: "'.$settings['defaultView'].'",';
        $lang = 'locale: "'.$GLOBALS['TSFE']->lang.'",';

        $flexformConfig = preg_replace("/\r|\n/", '', $settings['additionalConfig']);
        if (trim($flexformConfig) !== '' && substr($flexformConfig, -1) !== ',') {
            $flexformConfig = $flexformConfig.',';
        }

        if (trim($settings['aspectRatio']) !== '' && floatval($settings['aspectRatio']) != 0) {
            $aspectRatioHeight = 'aspectRatio: "'.floatval($settings['aspectRatio']).'",';
        } else {
            $aspectRatioHeight = 'height: "auto",';
        }

        $allDaySlot = 'allDaySlot:0,';
        if ($settings['allDaySlot']) {
            $allDaySlot = 'allDaySlot:'.$settings['allDaySlot'].',';
        }
        $minTime = '';
        if ($settings['minTime']) {
            $minTime = 'minTime: "'.date('H:i:s', $settings['minTime']).'",';
        }
        $maxTime = '';
        if ($settings['maxTime']) {
            $maxTime = 'maxTime: "'.date('H:i:s', $settings['maxTime']).'",';
        }
        $this->addJQueryUIThemeCSS($tsSettings['uiThemeCustom'], $tsSettings['uiTheme']);

        //complete javascript code
        $js = <<<EOT
            if(!newsCalendarTags){
                var newsCalendarTags = [];
            };
            if(!eventscal){
				var eventscal= [];
			}
			if(!eventscal.hasOwnProperty("newsCalendarEvent_$calendarUid")){
				eventscal["newsCalendarEvent_$uid"] = [];
			}
			
			(function($) {
			       var events = [
			       eventsCache = []
			       ];
			       var fillEventscal = function(events){
			       if(events != undefined) {
			            for (var i = 0; i < events.length; i++) {
                            if(!eventscal["newsCalendarEvent_$uid"].hasOwnProperty('Event_' + events[i]['id'] )){
                                eventscal["newsCalendarEvent_$uid"]['Event_' + events[i]['id']] = [];
                                eventscal["newsCalendarEvent_$uid"]['Event_' + events[i]['id']]['events'] = [];
                                eventscal["newsCalendarEvent_$uid"]['Event_' + events[i]['id']]['events'][0] = events[i];
                            }
                       }
			       }
                       
			       };
                    var newsUids = "$newsUids";
					var newsCalendar_$uid = $('#calendar.calendar_$uid').fullCalendar({
						$headerFooter[0]
						$headerFooter[1]
						$defaultView
						$minTime
						$maxTime
						$allDaySlot
						$lang
						$eventRenderer
						$buttonText
						$flexformConfig
				        $aspectRatioHeight
				        theme : 'true',
						buttonIcons: true, 
						weekNumbers: false,
			        	timezone : 'UTC',
			        	viewRender: function(){
                            if($('.fc-slats').length > 0) {
                                var bottomContainerPos = $('#calendar')[0].getBoundingClientRect().bottom;
                                var bottomTablePos = $('.fc-slats')[0].getBoundingClientRect().bottom;
                                var bottomDifference = bottomContainerPos - bottomTablePos ;
                                var currentHeight = $( ".fc-slats > table" ).css( "height");
                                var newHeight = parseInt(currentHeight) + bottomDifference;
                                $( ".fc-slats > table" ).css( "height", newHeight );
                            }
                        },
                        events: function (startdate, enddate, timezone, callback) {
                        
                            /*loading events via ajax as JSON string and store it in an array
                            * so next time the allready seen month doesnt need to be reloaded again
                            */
                            if (events.eventsCache && events.eventsCache[startdate.format() + "-" + enddate.format()]){
                                newsCalendarTags = events.eventsCache[startdate.format() + "-" + enddate.format()]['tags'];
                                callback(events.eventsCache[startdate.format() + "-" + enddate.format()]['events']);
                                if(DatedNewsFilterAdded.hasOwnProperty('newsCalendarEvent_' + $uid)){
                                        filterCalendarEvents(DatedNewsFilterAdded['newsCalendarEvent_' + $uid], $('#calendar.calendar_' + $uid), 'newsCalendarEvent_' + $uid);
                                    }
                                return;
                            }
                            $.get("?type=6660667", { "tx_news_pi1[action]": "ajaxEvent", "tx_news_pi1[start]": startdate.format(), "tx_news_pi1[end]": enddate.format(), "tx_news_pi1[newsUids]": newsUids}, function(data){
                                if (!events.eventsCache) {events.eventsCache = {};}
                                if(typeof data === 'string'){
                                    data = JSON.parse(data);
                                }
                                events.eventsCache[startdate.format() + "-" + enddate.format()] = data;
                                newsCalendarTags = data['tags'];
                                fillEventscal(data['events']);
                                callback(data['events']);
                                if(DatedNewsFilterAdded.hasOwnProperty('newsCalendarEvent_' + $uid)){
                                    filterCalendarEvents(DatedNewsFilterAdded['newsCalendarEvent_' + $uid], $('#calendar.calendar_' + $uid), 'newsCalendarEvent_' + $uid);
                                }
                            });
                        },  
			        	$timeFormat
			    	});
			})(jQuery);
			/*jQuery.noConflict(true);*/
			
EOT;

        if ($this->isCached()) {
            $this->pageRenderer->addFooterData('<script name="newsCalendar_'.$uid.'" type="text/javascript">'.$js.'</script>');
        } else {
            // additionalFooterData not possible in USER_INT
            $GLOBALS['TSFE']->additionalFooterData[md5('dated_newsCalendar_'.$uid)] = GeneralUtility::wrapJS($js);
        }

        $this->templateVariableContainer->add('datedNewsCalendarHtml', '<div id="calendar" class="fc-calendar-container '.$hasQtips.' calendar_'.$uid.'"></div>');

        return '<div id="calendar" data-qtipminwidth="'.$tsSettings['viewportMinWidthForTooltip'].'" class="fc-calendar-container '.$hasQtips.' calendar_'.$uid.'"></div>';
    }

    /**
     * @return string
     */
    public function getButtonText()
    {
        $extensionName = 'dated_news';
        $key = 'fullcalendar.';
        $today = LocalizationUtility::translate($key.'today', $extensionName);
        $month = LocalizationUtility::translate($key.'month', $extensionName);
        $week = LocalizationUtility::translate($key.'week', $extensionName);
        $agendaWeek = LocalizationUtility::translate($key.'agendaWeek', $extensionName);
        $day = LocalizationUtility::translate($key.'day', $extensionName);
        $agendaDay = LocalizationUtility::translate($key.'agendaDay', $extensionName);
        $listYear = LocalizationUtility::translate($key.'listYear', $extensionName);
        $listMonth = LocalizationUtility::translate($key.'listMonth', $extensionName);
        $listWeek = LocalizationUtility::translate($key.'listWeek', $extensionName);
        $listDay = LocalizationUtility::translate($key.'listDay', $extensionName);

        return "buttonText: {today:'".$today."',month:'".$month."',week:'".$week."',agendaWeek:'".$agendaWeek."',day:'".$day."',agendaDay:'".$agendaDay."',listYear:'".$listYear."',listMonth:'".$listMonth."',listWeek:'".$listWeek."',listDay:'".$listDay."'},";
    }

    /**
     * @param string $uiThemeCustom
     * @param $uiTheme
     */
    public function addJQueryUIThemeCSS($uiThemeCustom, $uiTheme)
    {
        if ($uiTheme === 'custom') {
            $uiTheme = $uiThemeCustom;
        }
        if ($uiTheme != null) {
            $this->pageRenderer->addCssFile('typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.min.css');
            $this->pageRenderer->addCssFile('typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.theme.min.css');
//            $GLOBALS['TSFE']->additionalHeaderData['dated_news1'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.min.css" media="all">';
//            $GLOBALS['TSFE']->additionalHeaderData['dated_news2'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.theme.min.css" media="all">';
        }
    }

    /**
     * @param $twentyfourhour
     *
     * @return string
     */
    public function buildTimeFormatOption($twentyfourhour)
    {
        if ($twentyfourhour === '0') {
            $tformat = "timeFormat: 'h:mm'";
        } else {
            $tformat = "timeFormat: 'H:mm'";
        }

        return $tformat;
    }

    /**
     * @param $tooltipPreStyle
     * @param $calendarClass
     *
     * @return string
     */
    public function buildEventRendererOption($tooltipPreStyle, $calendarClass)
    {
        $eventRenderer = <<<EOT
            eventRender: function(event, element) {
			    element.qtip({
				    style: { 
				        classes: 'qtip-rounded qtip-shadow $tooltipPreStyle' 
				    },
					hide: {
					    delay: 200,
						fixed: true, 
						effect: function() { $(this).fadeOut(250); }
					},
					position: {
					    viewport: $("$calendarClass"),
						adjust: {
						    method: 'flip'
						}
					},
					content: function(ev, api){
					    return event.qtip;
					}
				});
			},
EOT;

        return $eventRenderer;
    }

    /**
     * @param $titlePosition
     * @param $switchableViewsPosition
     * @param $nextPosition
     * @param $prevPosition
     * @param $todayPosition
     * @param $switchableViews
     *
     * @return array
     */
    public function buildHeaderFooterOption($titlePosition, $switchableViewsPosition, $nextPosition, $prevPosition, $todayPosition, $switchableViews)
    {
        //generate js option for buttons positions
        $positions = [
            'header' => [
                'left'   => '',
                'center' => '',
                'right'  => '',
            ],
            'footer' => [
                'left'   => '',
                'center' => '',
                'right'  => '',
            ],
        ];

        $titlePositionArr = explode('_', $titlePosition);
        $positions[$titlePositionArr[0]][$titlePositionArr[1]] .= 'title';
        $prevPositionArr = explode('_', $prevPosition);
        $positions[$prevPositionArr[0]][$prevPositionArr[1]] .= ', prev';
        $nextPositionArr = explode('_', $nextPosition);
        $positions[$nextPositionArr[0]][$nextPositionArr[1]] .= ', next';
        $todayPositionArr = explode('_', $todayPosition);
        $positions[$todayPositionArr[0]][$todayPositionArr[1]] .= ', today';
        $switchableViewsPositionArr = explode('_', $switchableViewsPosition);
        $positions[$switchableViewsPositionArr[0]][$switchableViewsPositionArr[1]] .= ', '.$switchableViews;
        $header = 'header: {left: "'.$positions['header']['left'].'", center: "'.$positions['header']['center'].'", right: "'.$positions['header']['right'].'"},';
        $footer = 'footer: {left: "'.$positions['footer']['left'].'", center: "'.$positions['footer']['center'].'", right: "'.$positions['footer']['right'].'"},';

        return [$header, $footer];
    }
}
