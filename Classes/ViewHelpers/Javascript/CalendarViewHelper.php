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
	}

	/**
	* @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
	* @return string the needed html markup inklusive javascript
	*/
	public function render() {
 		$settings = $this->arguments['settings']['dated_news'];
 		$uiThemeCustom = $settings['uiThemeCustom'];
 		$uiTheme = $settings['uiTheme'];
 		$tooltipPreStyle = $settings['tooltipPreStyle'];

		$titlePosition = $settings['titlePosition'];
		$switchableViewsPosition = $settings['switchableViewsPosition'];
		$nextPosition = $settings['nextPosition'];
		$todayPosition = $settings['todayPosition'];
		$prevPosition = $settings['prevPosition'];

		$switchableViews = $settings['switchableViews'];
		$defaultView = 'defaultView: "'. $settings['defaultView'].'",';
 		$twentyfourhour = $settings['twentyfourhour'];
 		$allDaySlot = 'allDaySlot:' . $settings['allDaySlot'] .',';
 		$minTime = 'minTime: "' . $settings['minTime']. '",';
 		$maxTime = 'maxTime: "' . $settings['maxTime']. '",';

		if ($uiTheme === 'custom') {
			$uiTheme = $uiThemeCustom;
		}
		if ($uiTheme != NULL) {
			$GLOBALS['TSFE']->additionalHeaderData['dated_news1'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.min.css" media="all">';
			$GLOBALS['TSFE']->additionalHeaderData['dated_news2'] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/dated_news/Resources/Public/CSS/jqueryThemes/'.$uiTheme.'/jquery-ui.theme.min.css" media="all">';
		}

		$lang = $GLOBALS['TSFE']->lang;

		if ($twentyfourhour === '0') {
			$tformat = "timeFormat: 'h:mm'";
		} else {
			$tformat = "timeFormat: 'H:mm'";
		}

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

		$container = '<div id="calendar" class="fc-calendar-container"></div>';
		
		$js = <<<EOT
			(function($) {
					newsCalendar = $('#calendar').fullCalendar({
						$header
						$footer
						$defaultView
						$minTime
						$maxTime
						$allDaySlot
						eventRender: function(event, element) {
					        element.qtip({
				        		style: { 
				        			classes: 'qtip-rounded qtip-shadow qtip-cluetip $tooltipPreStyle' 
				        		},
					        	hide: {
							        delay: 200,
							        fixed: true, 
							        effect: function() { $(this).fadeOut(250); }
							    },
							    position: {
							        viewport: $(window),
							        adjust: {
							            method: 'flip'
							        }
							    },
					            content: function(ev, api){
									return event.qtip;
					            }
					        });
					    },
				        locale: '$lang',
				        height: 'auto',
				        theme : 'true',
						buttonIcons: true, // show the prev/next text
						weekNumbers: false,
			        	timezone : 'local',
			        	$tformat

			    	})
					
					function addAllEvents(){
						for (var key in newsCalendarEvent) {
						  if (newsCalendarEvent.hasOwnProperty(key)) {
							newsCalendar.fullCalendar( 'addEventSource', newsCalendarEvent[key] )	
						  }
						}
					}
					addAllEvents();
					function removeAllEvents(){
						for (var key in newsCalendarEvent) {
						  if (newsCalendarEvent.hasOwnProperty(key)) {
							newsCalendar.fullCalendar( 'removeEventSource', newsCalendarEvent[key] )	
						  }
						}
					}

					$('.dated-news-filter').on('click', function(){
						removeAllEvents();
						$(this).hasClass('dn-checked') ? $(this).removeClass('dn-checked') : $(this).addClass('dn-checked');
						var dnchecked = $('.dated-news-filter.dn-checked');
						// wenn nix gechecked dann alle adden
						if (!dnchecked.length) {
							addAllEvents();
						} else {
							var added =[];
							dnchecked.each(function(){
								var filter = $(this).data('dn-filter');
								for (var key in newsCalendarTags[filter]) {
							  		if (newsCalendarTags[filter].hasOwnProperty(key)) {
							  			//make sure event wasn't added before
							  			if (!added['Event_'+newsCalendarTags[filter][key]]) {
											newsCalendar.fullCalendar( 'addEventSource', newsCalendarEvent['Event_'+newsCalendarTags[filter][key]] )	
								  			added['Event_'+newsCalendarTags[filter][key]] = 1;
							  			}
							  		}
								}		
							}) 
						}

							
						
					})
			})(jQuery);
			jQuery.noConflict(true);
			
EOT;

		$this->templateVariableContainer->add('datedNewsCalendarJS', $js);
		$this->templateVariableContainer->add('datedNewsCalendarHtml', $container);
	}
}