<?php
namespace FalkRoeder\DatedNews\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Repository;

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
 * The repository for FE Users
 */
class FeuserRepository extends Repository
{
    public function loginNameExist($loginName)
    {
        $users = $this->getUserWithSimilarLoginNames($loginName);
        return $users->count() > 0 ? true : false;
    }

    public function getUserWithSimilarLoginNames($loginName)
    {
        $settings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $settings->setIgnoreEnableFields(true);
        $settings->setEnableFieldsToBeIgnored(['hidden', 'deleted', 'disabled']);
        $settings->setRespectStoragePage(false);

        $query = $this->createQuery();
        $query->setQuerySettings($settings);

        $query->matching(
            $query->like('username', '%' . $loginName . '%', true)
        );

        return $query->execute();
    }

    public function getSimilarLoginNames($loginName)
    {
        $users = $this->getUserWithSimilarLoginNames($loginName);
        $loginNames = [];
        foreach ($users as $user) {
            array_push($loginNames, $user->getUsername());
        }

        return $loginNames;
    }

    public function findByEmail($email)
    {
        $settings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');
        $settings->setIgnoreEnableFields(true);
        $settings->setEnableFieldsToBeIgnored(['hidden', 'deleted', 'disabled']);
        $settings->setRespectStoragePage(false);

        $query = $this->createQuery();
        $query->setQuerySettings($settings);

        $query->matching(
            $query->equals('email', $email)
        );
        $result = $query->execute();
        if ($result->count() !== 1) {
            return false;
        } else {
            return $result->getFirst();
        }
    }
}
