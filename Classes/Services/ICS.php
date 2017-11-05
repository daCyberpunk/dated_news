<?php

namespace FalkRoeder\DatedNews\Services;

/***
 *
 * This file is part of the "Dated News" Extension for TYPO3 CMS.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2017
 *
 * Author Falk Röder <mail@falk-roeder.de>
 *
 ***/

/**
 * ICS.php
 * =======
 * Use this class to create an .ics file.
 *
 * Usage
 * -----
 * Basic usage - generate ics file contents (see below for available properties):
 *   $ics = new ICS($props);
 *   $ics_file_contents = $ics->to_string();
 *
 * Setting properties after instantiation
 *   $ics = new ICS();
 *   $ics->set('summary', 'My awesome event');
 *
 * You can also set multiple properties at the same time by using an array:
 *   $ics->set(array(
 *     'dtstart' => 'now + 30 minutes',
 *     'dtend' => 'now + 1 hour'
 *   ));
 *
 * Available properties
 * --------------------
 * description
 *   String description of the event.
 * dtend
 *   A date/time stamp designating the end of the event. You can use either a
 *   DateTime object or a PHP datetime format string (e.g. "now + 1 hour").
 * dtstart
 *   A date/time stamp designating the start of the event. You can use either a
 *   DateTime object or a PHP datetime format string (e.g. "now + 1 hour").
 * location
 *   String address or description of the location of the event.
 * summary
 *   String short summary of the event - usually used as the title.
 * url
 *   A url to attach to the the event. Make sure to add the protocol (http://
 *   or https://).
 */
class ICS
{
    const DT_FORMAT = 'Ymd\THis\Z';

    protected $properties = [];

    private $available_properties = [
        'description',
        'dtend',
        'dtstart',
        'location',
        'summary',
        'url',
//        'organizer',
//        'attendee'
    ];

    /**
     * ICS constructor.
     *
     * @param $props
     */
    public function __construct($props)
    {
        $this->set($props);
    }

    /**
     * @param $key
     * @param bool $val
     */
    public function set($key, $val = false)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            if (in_array($key, $this->available_properties)) {
                $this->properties[$key] = $this->sanitize_val($val, $key);
            }
        }
    }

    /**
     * @return string
     */
    public function to_string()
    {
        $rows = $this->build_props();

        return implode("\r\n", $rows);
    }

    /**
     * @return array
     */
    private function build_props()
    {
        // Build ICS properties - add header
        $ics_props = [
            'BEGIN:VCALENDAR',
            'PRODID:-//GourmetPortal//NONSGML rr//EN',
            'VERSION:2.0',
//            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
//            'ORGANIZER;RSVP=TRUE;PARTSTAT=ACCEPTED;ROLE=CHAIR:mailto:mail@falk-roeder.de',
//            'ATTENDEE;RSVP=TRUE;CN=Falk Roeder;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT:mailto:mail@falk-roeder.de',
            'CALSCALE:GREGORIAN',
            'TRANSP:OPAQUE',
        ];

        // Build ICS properties - add header
        $props = [];
        foreach ($this->properties as $k => $v) {
            $props[strtoupper($k.($k === 'url' ? ';VALUE=URI' : ''))] = $v;
        }

        // Set some default values
        $props['DTSTAMP'] = $this->format_timestamp('now');
        $props['UID'] = uniqid();

        // Append properties
        foreach ($props as $k => $v) {
            $ics_props[] = "$k:$v";
        }

        // Build ICS properties - add footer
        $ics_props[] = 'END:VEVENT';
        $ics_props[] = 'END:VCALENDAR';

        return $ics_props;
    }

    /**
     * @param $val
     * @param bool $key
     *
     * @return mixed|string
     */
    private function sanitize_val($val, $key = false)
    {
        switch ($key) {
            case 'dtend':
            case 'dtstamp':
            case 'dtstart':
                $val = $this->format_timestamp($val);
                break;
            case 'organizer':
                $val = 'ORGANIZER;RSVP=TRUE;PARTSTAT=ACCEPTED;ROLE=CHAIR:mailto:'.$val.'';
                break;
            case 'attendee':
                $val = 'ATTENDEE;RSVP=TRUE;CN=Falk Roeder;PARTSTAT=NEEDS-ACTION;ROLE=REQ-PARTICIPANT:mailto:'.$val.'';
                break;
            default:
                $val = $this->escape_string($val);
        }

        return $val;
    }

    /**
     * @param $timestamp
     *
     * @return string
     */
    private function format_timestamp($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimestamp((int) $timestamp);

        return $dt->format(self::DT_FORMAT);
    }

    /**
     * @param $str
     *
     * @return mixed
     */
    private function escape_string($str)
    {
        return preg_replace('/([\,;])/', '\\\$1', $str);
    }
}
