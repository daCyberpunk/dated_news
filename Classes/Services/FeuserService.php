<?php
namespace FalkRoeder\DatedNews\Services;

use FalkRoeder\DatedNews\Domain\Model\Feuser;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Service to control frontend editing access for frontend user
 */
class FeuserService implements SingletonInterface
{

    /**
     * @var \FalkRoeder\DatedNews\Domain\Repository\FeuserRepository
     * @inject
     */
    protected $feuserRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository
     */
    protected $feuserGroupRepository;

    /**
     * @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $extbaseObjectManager = null;

    /**
     * @var $persistenceManager \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     */
    protected $persistenceManager = null;

    public function __construct()
    {
        $this->extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->persistenceManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $this->feuserGroupRepository = $this->extbaseObjectManager->get('TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository');
    }

    /**
     * @return bool
     */
    public function hasLoggedInFrontendUser()
    {
        if ($GLOBALS['TSFE']->loginUser) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getFrontendUserGroups()
    {
        if ($this->hasLoggedInFrontendUser()) {
            return $GLOBALS['TSFE']->fe_user->groupData['uid'];
        }
        return [];
    }

    /**
     * @return int|null
     */
    public function getFrontendUserUid()
    {
        if ($this->hasLoggedInFrontendUser() && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            return intval($GLOBALS['TSFE']->fe_user->user['uid']);
        }
        return null;
    }

    /**
     * @return \FalkRoeder\DatedNews\Domain\Model\Feuser|null
     */
    public function getFrontendUserObject()
    {
        if ($this->hasLoggedInFrontendUser() && !empty($GLOBALS['TSFE']->fe_user->user['uid'])) {
            return $this->feuserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
        }
        return null;
    }

    /**
     * @return \FalkRoeder\DatedNews\Domain\Model\Feuser|bool
     */
    public function findUserByEmail($email)
    {
        $user = false;
        if (is_string($email) && GeneralUtility::validEmail($email)) {
            $user = $this->feuserRepository->findByEmail($email);
        }
        return $user;
    }

    /**
     * gets a new FE-User and his clear password
     *
     * @return array|bool
     */
    public function getNewFeuser($userData = [], $userGroupUids, $storagePage)
    {
        $user = $this->add($userData, $userGroupUids, $storagePage);
        if (!$user) {
            return false;
        }
        $pw = $this->generatePassword();
        $user->setPassword($pw['salted']);
        $this->feuserRepository->update($user);
        $this->persistenceManager->persistAll();

        return [
            'user' => $user,
            'password' => $pw['clear']
        ];
    }

    /**
     * Adds a new FE-User
     *
     * @return \FalkRoeder\DatedNews\Domain\Model\Feuser|bool
     */
    protected function add($userData = [], $userGroupUids, $storagePage)
    {
        if (!is_string($userData['email']) || !GeneralUtility::validEmail($userData['email'])) {
            return false;
        }

        $newFeuser = new Feuser();

        foreach ($userData as $fieldName => $value) {
            $func = 'set' . ucfirst($fieldName);
            if (method_exists($newFeuser, $func) === true) {
                $newFeuser->{$func}($value);
            } else {
                continue;
            }
        }

        $loginName = $this->createLoginName($userData['firstName'], $userData['lastName']);
        $newFeuser->setUsername($loginName);

        $userGroupUids = explode(',', $userGroupUids);
        $userGroups = new ObjectStorage();

        foreach ($userGroupUids as $uid) {
            $groupObj = $this->feuserGroupRepository->findByUid($uid);
            if ($groupObj) {
                $userGroups->attach($groupObj);
            }
        }

        $newFeuser->setUsergroup($userGroups);

        $newFeuser->setPid($storagePage);

        $this->feuserRepository->add($newFeuser);
        $this->persistenceManager->persistAll();
        return $newFeuser;
    }

    /**
     * gets a new LoginName for a new user
     *
     * @return string'
     */
    protected function createLoginName($firstName, $lastName)
    {
        $loginName = strtolower(preg_replace('/\s/', '', $firstName . $lastName));

        if ($this->feuserRepository->loginNameExist($loginName)) {
            $similarNames = $this->feuserRepository->getSimilarLoginNames($loginName);
            $numbers = [];
            foreach ($similarNames as $name) {
                array_push(
                    $numbers,
                    (int) str_replace(
                        $loginName,
                        '',
                        strtolower($name)
                    )
                );
            }

            $number = max($numbers);

            $number++;
            $loginName .= $number;
            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($loginName, 'FeuserService:186');
        }
        return $loginName;
    }

    /**
     * Generates new salted pw
     *
     * @return array
     */
    protected function generatePassword()
    {
        $pw = [];
        $pool = 'qwertzupasdfghkyxcvbnm';
        $pool .= '23456789';
        $pool .= 'WERTZUPLKJHGFDSAYXCVBNM';

        srand((double)microtime()*1000000);
        for ($index = 0; $index < 10; $index++) {
            $pw['clear'] .= substr($pool, (rand()%(strlen($pool))), 1);
        }
        $objInstanceSaltedPW = SaltFactory::getSaltingInstance();
        $pw['salted'] = $objInstanceSaltedPW->getHashedPassword($pw['clear']);
        return $pw;
    }
}
