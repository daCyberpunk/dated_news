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

/**
 * CalendarViewHelper
 * 
 * @package TYPO3
 * @subpackage dated_news
 * @author Falk Röder
 */
class CalendarViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	* Arguments initialization
	*
	* @return void
	*/
	public function initializeArguments() {
		$this->registerArgument('settings', 'mixed', 'settings');
		$this->registerArgument('id', 'integer', 'Uid of Content Element');
	}

	/**
	* @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
	* @return string the needed html markup inklusive javascript
	*/
	public function render() {
 		$settings = $this->arguments['settings'];
        $tsSettings = $settings['dated_news'];
        $uid = $this->arguments['id'];

        //build all options
        $headerFooter = $this->buildHeaderFooterOption(
            $tsSettings['titlePosition'],
            $tsSettings['switchableViewsPosition'],
            $tsSettings['nextPosition'],
            $tsSettings['prevPosition'],
            $tsSettings['todayPosition'],
            $settings['switchableViews']
            );
        $eventRenderer = $this->buildEventRendererOption($tsSettings['tooltipPreStyle'], '.calendar_' . $uid);
        $timeFormat = $this->buildTimeFormatOption($tsSettings['twentyfourhour']);
        $buttonText = $this->getButtonText();
		$defaultView = 'defaultView: "'. $settings['defaultView'].'",';
        $lang = 'locale: "'.$GLOBALS['TSFE']->lang .'",';

        $flexformConfig = preg_replace( "/\r|\n/", "", $settings['additionalConfig'] );
        if(trim($flexformConfig) !== '' && substr($flexformConfig, -1) !== ',') {
            $flexformConfig = $flexformConfig .',';
        }

        if(trim($settings['aspectRatio']) !== '' && floatval($settings['aspectRatio']) != 0){
            $aspectRatioHeight = 'aspectRatio: "' . floatval($settings['aspectRatio']) . '",';
        } else {
            $aspectRatioHeight = 'height: "auto",';
        }

        $allDaySlot = 'allDaySlot:0,';
        if($settings['allDaySlot']){
            $allDaySlot = 'allDaySlot:' . $settings['allDaySlot'] .',';
        }
        $minTime ='';
        if($settings['minTime']){
            $minTime = 'minTime: "' . date('H:i:s',$settings['minTime']) . '",';
        }
        $maxTime ='';
        if($settings['maxTime']) {
            $maxTime = 'maxTime: "' . date('H:i:s', $settings['maxTime']) . '",';
        }
        $this->addJQueryUIThemeCSS($tsSettings['uiThemeCustom'], $tsSettings['uiTheme']);


        //complete javascript code
		$js = <<<EOT
			(function($) {
			       
					newsCalendar_$uid = $('#calendar.calendar_$uid').fullCalendar({
						$headerFooter[0]
						$headerFooter[1]
						$defaultView
						$minTime
						$maxTime
						$allDaySlot
						$lang
						$eventRenderer
						$buttonText
						$columnFormat
						$flexformConfig
				        $aspectRatioHeight
				        theme : 'true',
						buttonIcons: true, 
						weekNumbers: false,
			        	timezone : 'local',
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
			        	$timeFormat
			    	});
					addAllEvents(newsCalendar_$uid,"newsCalendarEvent_$uid");
					
					
					
			})(jQuery);
			/*jQuery.noConflict(true);*/
			
EOT;

		$this->templateVariableContainer->add('datedNewsCalendarJS', $js);
		$this->templateVariableContainer->add('datedNewsCalendarHtml', '<div id="calendar" class="fc-calendar-container calendar_'.$uid.'"></div>');
	}

    public function getButtonText(){
        $extensionName = 'dated_news';
        $key = 'fullcalendar.';
        $today = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'today', $extensionName);
        $month = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'month', $extensionName);
        $week = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'week', $extensionName);
        $agendaWeek = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'agendaWeek', $extensionName);
        $day = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'day', $extensionName);
        $agendaDay = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'agendaDay', $extensionName);
        $listYear = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'listYear', $extensionName);
        $listMonth = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'listMonth', $extensionName);
        $listWeek = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'listWeek', $extensionName);
        $listDay = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key .'listDay', $extensionName);
        
        return "buttonText: {today:'".$today."',month:'".$month."',week:'".$week."',agendaWeek:'".$agendaWeek."',day:'".$day."',agendaDay:'".$agendaDay."',listYear:'".$listYear."',listMonth:'".$listMonth."',listWeek:'".$listWeek."',listDay:'".$listDay."'},";

    }

    public function addJQueryUIThemeCSS($uiThemeCustom = '', $uiTheme){
        if ($uiTheme === 'custom') {
            $uiTheme = $uiThemeCustom;
        }
        if ($uiTheme != NULL) {
            $GLOBALS['TSFE']->additionalHeaderData['dated_news1'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.min.css" media="all">';
            $GLOBALS['TSFE']->additionalHeaderData['dated_news2'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.theme.min.css" media="all">';
        }
    }

    public function buildTimeFormatOption($twentyfourhour){
        if ($twentyfourhour === '0') {
            $tformat = "timeFormat: 'h:mm'";
        } else {
            $tformat = "timeFormat: 'H:mm'";
        }
        return $tformat;
    }
    public function buildEventRendererOption($tooltipPreStyle, $calendarClass){
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

    public function buildHeaderFooterOption($titlePosition,$switchableViewsPosition,$nextPosition,$prevPosition,$todayPosition,$switchableViews){
        //generate js option for buttons positions
        $positions = ['header' => ['left' => '','center' => '','right' => ''],'footer' => ['left' => '','center' => '','right' => '']];

        $titlePositionArr = explode('_', $titlePosition);
        $positions[$titlePositionArr[0]][$titlePositionArr[1]] .= 'title';
        $prevPositionArr = explode('_', $prevPosition);
        $positions[$prevPositionArr[0]][$prevPositionArr[1]] .= ', prev';
        $nextPositionArr = explode('_', $nextPosition);
        $positions[$nextPositionArr[0]][$nextPositionArr[1]] .= ', next';
        $todayPositionArr = explode('_', $todayPosition);
        $positions[$todayPositionArr[0]][$todayPositionArr[1]] .= ', today';
        $switchableViewsPositionArr = explode('_', $switchableViewsPosition);
        $positions[$switchableViewsPositionArr[0]][$switchableViewsPositionArr[1]] .= ', ' . $switchableViews;
        $header = 'header: {left: "'.$positions['header']['left'].'", center: "'.$positions['header']['center'].'", right: "'.$positions['header']['right'].'"},';
        $footer = 'footer: {left: "'.$positions['footer']['left'].'", center: "'.$positions['footer']['center'].'", right: "'.$positions['footer']['right'].'"},';

        return array($header, $footer);
    }


}