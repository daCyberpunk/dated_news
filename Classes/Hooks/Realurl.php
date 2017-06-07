<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace FalkRoeder\DatedNews\Hooks;

/**
 * RealURL auto-configuration and segment decoder.
 *
 * @category    Hooks
 */
class Realurl
{
    /**
     * This methods will "eat" every remaining segment in the URL to make it part
     * of the requested document.
     *
     * @param $params
     * @param $parent
     *
     * @return string
     *
     * @internal param array $parameters
     */
    public function decodeSpURL_getSequence($params, $parent)
    {
        $value = $params['value'];
//        $GLOBALS['BE_USER']->writelog(4, 0, 0, 'scheduler', print_r($params) , array());

        if ($params['decodeAlias']) {
            return $this->alias2id($params['value']);
        } else {
            return $this->id2alias($params['value']);
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function id2alias($value)
    {
        if ($value == '') {
            $value = 'event';
        }
        if ($value > 0) {
            $titleHash = $this->getApplicationMd5($value);
            $hashArray = str_split($titleHash, 8);

            $value = $hashArray[0].$value.$hashArray[1];
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function alias2id($value)
    {
        $value = substr($value, 8);
        $value = substr($value, -8);

        return $value;
    }

    /**
     * @return array
     */
    protected function getMd5HashParts()
    {
        $hash = md5('booking');

        return str_split($hash, 8);
    }

    /**
     * @param $uid
     *
     * @return string
     */
    protected function getApplicationMd5($uid)
    {
        $db = $this->getDatabaseConnection();
        $mysqli = new \mysqli($db['host'], $db['username'], $db['password'], $db['database']);
        $sql = 'SELECT title FROM tx_datednews_domain_model_application WHERE uid='.$uid;
        $result = $mysqli->query($sql) or die('db-query failed '. 1487571690984);

        if ($result) {
            // Cycle through results
            while ($row = $result->fetch_object()) {
                return md5($row->title);
            }
        }
    }

    /**
     * Returns the database connection.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['DB'];
    }
}
