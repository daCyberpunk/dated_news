<?php

namespace FR\NewsCalendar\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Falk Röder <mail@falk-roeder.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \FR\NewsCalendar\Domain\Model\News.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Falk Röder <mail@falk-roeder.de>
 */
class NewsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \FR\NewsCalendar\Domain\Model\News
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \FR\NewsCalendar\Domain\Model\News();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getEventstartReturnsInitialValueForDateTime() {
		$this->assertEquals(
			NULL,
			$this->subject->getEventstart()
		);
	}

	/**
	 * @test
	 */
	public function setEventstartForDateTimeSetsEventstart() {
		$dateTimeFixture = new \DateTime();
		$this->subject->setEventstart($dateTimeFixture);

		$this->assertAttributeEquals(
			$dateTimeFixture,
			'eventstart',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getEventendReturnsInitialValueForDateTime() {
		$this->assertEquals(
			NULL,
			$this->subject->getEventend()
		);
	}

	/**
	 * @test
	 */
	public function setEventendForDateTimeSetsEventend() {
		$dateTimeFixture = new \DateTime();
		$this->subject->setEventend($dateTimeFixture);

		$this->assertAttributeEquals(
			$dateTimeFixture,
			'eventend',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getEventlocationReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getEventlocation()
		);
	}

	/**
	 * @test
	 */
	public function setEventlocationForStringSetsEventlocation() {
		$this->subject->setEventlocation('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'eventlocation',
			$this->subject
		);
	}
}
