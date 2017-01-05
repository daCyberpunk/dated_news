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
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * EventViewHelper
 * 
 * @package TYPO3
 * @subpackage dated_news
 * @author Falk Röder
 * @inject
 */
class EventViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	* Arguments initialization
	*
	* @return void
	*/
	public function initializeArguments() {
		$this->registerArgument('url', 'mixed', 'url to detailview of event');
		$this->registerArgument('strftime', 'bool', 'if true, the strftime is used instead of date()', FALSE, TRUE);
		$this->registerArgument('description', 'string', 'description of event');
		$this->registerArgument('item', 'mixed', 'newsitem');
		$this->registerArgument('iterator', 'mixed', 'iterator');

	}

	/**
	* @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $objects
	* @return string the needed html markup inklusive javascript
	*/
	public function render() {
 		$item = $this->arguments['item'];
 		$strftime = $this->arguments['strftime'];


 		$title = $item->getTitle();
 		$start = $item->getEventstart();
 		$end = $item->getEventend();
 		$fulltime = $item->getFulltime();
 		$color = $item->getBackgroundcolor;
 		$textcolor = $item->getTextcolor;
 		$uid = $item->getUid();
 		$tags = $item->getTags();

 		$i = 0;
 		foreach($tags as $key => $value) {
 			$i++;
 			if ($i === 1) {
 				$filterTags = $value->getTitle();
 			} else {
 				$filterTags .= ','.$value->getTitle();
 			}
		}

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

		if (!$end instanceof \DateTime && $end !== NULL) {
			try {
				$end = new \DateTime($end);
			} catch (\Exception $exception) {
				throw new \TYPO3\CMS\Fluid\Core\ViewHelper\Exception('"' . $end . '" could not be parsed by DateTime constructor.', 1438925934);
			}
		}

		if ($strftime) {
			$formattedStart = strftime('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
			if($end !== NULL){
				$formattedEnd = strftime('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
			}

		} else {
			$formattedStart = date('%Y-%m-%dT%H:%M:%S+00:00', $start->format('U'));
			if($end !== NULL){
				$formattedEnd = date('%Y-%m-%dT%H:%M:%S+00:00', $end->format('U'));
			}



		}

		if ($fulltime === TRUE) {
			$allDay = ',allDay: true';
		}

		$qtip = ',qtip: \'' . trim(preg_replace( "/\r|\n/", "", $this->renderChildren())) . '\'';
		
		$string = <<<EOT
				if(!newsCalendarEvent){
					var newsCalendarEvent = [];
				}
				newsCalendarEvent["Event_$uid"] = {
			    events: [
			        {
			    	    title: '$title',
			            start: '$formattedStart',
			            end: '$formattedEnd',
			            className: 'Event_$uid'
			            $allDay
			            $qtip 
			        }
			    ],
			    color: '$color',
			    textColor: '$textcolor'
			}
				if(!newsCalendarTags){
					var newsCalendarTags = [];
				}

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

		return $string; 		
	}
}