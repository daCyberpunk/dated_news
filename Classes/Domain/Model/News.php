<?php
namespace FR\NewsCalendar\Domain\Model;


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
 * News
 */
class News extends \GeorgRinger\News\Domain\Model\News {

	/**
	 * showincalendar
	 *
	 * @var boolean
	 */
	protected $showincalendar = FALSE;

	/**
	 * Returns the showincalendar
	 *
	 * @return boolean $showincalendar
	 */
	public function getShowincalendar() {
		return $this->showincalendar;
	}

	/**
	 * Sets the showincalendar
	 *
	 * @param boolean $showincalendar
	 * @return void
	 */
	public function setShowincalendar($showincalendar) {
		$this->showincalendar = $showincalendar;
	}

	/**
	 * Returns the boolean state of showincalendar
	 *
	 * @return boolean
	 */
	public function isShowincalendar() {
		return $this->showincalendar;
	}

	/**
	 * fulltime
	 *
	 * @var boolean
	 */
	protected $fulltime = FALSE;

	/**
	 * Returns the fulltime
	 *
	 * @return boolean $fulltime
	 */
	public function getFulltime() {
		return $this->fulltime;
	}

	/**
	 * Sets the fulltime
	 *
	 * @param boolean $fulltime
	 * @return void
	 */
	public function setFulltime($fulltime) {
		$this->fulltime = $fulltime;
	}

	/**
	 * Returns the boolean state of fulltime
	 *
	 * @return boolean
	 */
	public function isFulltime() {
		return $this->fulltime;
	}

	/**
	 * eventstart
	 *
	 * @var \DateTime
	 */
	protected $eventstart = NULL;

	/**
	 * eventend
	 *
	 * @var \DateTime
	 */
	protected $eventend = NULL;

	/**
	 * eventlocation
	 *
	 * @var string
	 */
	protected $eventlocation = '';

	/**
	 * Returns the eventstart
	 *
	 * @return \DateTime $eventstart
	 */
	public function getEventstart() {
		return $this->eventstart;
	}

	/**
	 * Sets the eventstart
	 *
	 * @param \DateTime $eventstart
	 * @return void
	 */
	public function setEventstart(\DateTime $eventstart) {
		$this->eventstart = $eventstart;
	}

	/**
	 * Returns the eventend
	 *
	 * @return \DateTime $eventend
	 */
	public function getEventend() {
		return $this->eventend;
	}

	/**
	 * Sets the eventend
	 *
	 * @param \DateTime $eventend
	 * @return void
	 */
	public function setEventend(\DateTime $eventend) {
		$this->eventend = $eventend;
	}

	/**
	 * Returns the eventlocation
	 *
	 * @return string $eventlocation
	 */
	public function getEventlocation() {
		return $this->eventlocation;
	}

	/**
	 * Sets the eventlocation
	 *
	 * @param string $eventlocation
	 * @return void
	 */
	public function setEventlocation($eventlocation) {
		$this->eventlocation = $eventlocation;
	}

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
	public function getTextcolor() {
		return $this->textcolor;
	}

	/**
	 * Sets the textcolor
	 *
	 * @param string $textcolor
	 * @return void
	 */
	public function setTextcolor($textcolor) {
		$this->textcolor = $textcolor;
	}

	/**
	 * Returns the backgroundcolor
	 *
	 * @return string $backgroundcolor
	 */
	public function getBackgroundcolor() {
		return $this->backgroundcolor;
	}

	/**
	 * Sets the backgroundcolor
	 *
	 * @param string $backgroundcolor
	 * @return void
	 */
	public function setBackgroundcolor($backgroundcolor) {
		$this->backgroundcolor = $backgroundcolor;
	}


}