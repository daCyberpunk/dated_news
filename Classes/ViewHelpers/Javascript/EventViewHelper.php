<?php
namespace FR\NewsCalendar\Viewhelpers\Javascript;

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
 * EventViewHelper
 * 
 * @package TYPO3
 * @subpackage news_calendar
 * @author Falk Röder
 */
class EventViewHelper extends \Tx_Fluid_Core_ViewHelper_AbstractViewHelper {



	/**
	 * Creates Javascript Output for given events
	 * @param string $title title of event
	 * @param mixed $url url to detailview of event
	 * @param mixed $start startdate of event
	 * @param mixed $end enddate of event
	 * @param bool $strftime if true, the strftime is used instead of date()
	 * @param bool $fulltime if true, the event time will be ignored
	 * @param string $description description of event
	 * @param string $color backgroundcolor in calender of event optional
	 * @param string $textcolor textcolor in calender of event optional
	 * @param int $uid uid of the event
	 * @return string the needed html markup inclusive javascript
	 * @api
	 */
	public function render($url, $uid = NULL, $title = NULL, $start = NULL, $end = NULL, $description = NULL, $color = NULL, $textcolor = NULL, $strftime = TRUE, $fulltime = FALSE ) {

		if ($start === NULL || $uid === NULL) {
				return '';
		}

		date_default_timezone_set('UTC');
		if (!$start instanceof \DateTime) {
			try {
				$start = new \DateTime($start);
			} catch (\Exception $exception) {
				throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('"' . $start . '" could not be parsed by DateTime constructor.', 1438925934);
			}
		}
		if (!$end instanceof \DateTime) {
			try {
				$end = new \DateTime($end);
			} catch (\Exception $exception) {
				throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('"' . $end . '" could not be parsed by DateTime constructor.', 1438925934);
			}
		}
		if ($strftime) {
			$formattedStart = strftime('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
			$formattedEnd = strftime('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
		} else {
			$formattedStart = date('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
			$formattedEnd = date('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
		}

		if ($fulltime === TRUE) {
			$allDay = ',allDay: true';
		}
		if ($description != NULL) {
			$description = "description: '".trim(preg_replace('/\s+/', ' ', $description))."',";
		}

		$string = <<<EOT
			<script type='text/javascript'>
				if(!newsCalendarEvent){
					var newsCalendarEvent = [];
				}
				newsCalendarEvent["Event_$uid"] = {
			    events: [
			        {
			    	    title: '$title',
			            start: '$formattedStart',
			            end: '$formattedEnd',
			            className: 'Event_$uid',
			            $description
			            /*url: '$url'*/
			            uri: '$url'
			            $allDay
			        }

			    ],
			    color: '$color',
			    textColor: '$textcolor'
			}
			</script>
EOT;

		return $string; 		
	}
}