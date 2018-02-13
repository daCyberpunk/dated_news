<?php

namespace FalkRoeder\DatedNews\Utility;

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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Service\FlexFormService;

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
 * This class provides misc functions for the dated News extension.
 *
 * @author Falk Röder <mail@falk-roeder.de>
 */
class Div
{
    /**
     * configurationManager.
     *
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     * @inject
     */
    protected $configurationManager;

    /**
     * objectManager.
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * Generate and send Email.
     *
     * @param array $conf
     * @return bool
     */
    public function sendEmail($conf)
    {

        $emailBodyObject = $this->getEmailBodyObject($conf);
        $email = $this->createEmail($conf);
        $email->setCharset($GLOBALS['TSFE']->metaCharset);
        $email->setBody($emailBodyObject->render(), 'text/html');

        if (isset($conf['fileNames']) && is_array($conf[$fileNames])) {
            foreach ($fileNames as $fileName) {
                if (trim($fileName) != '') {
                    $email->attach(\Swift_Attachment::fromPath('fileadmin'.$fileName));
                }
            }
        }

        $email->send();

        return $email->isSent();
    }

    /**
     * createEmail
     *
     * @param $conf
     * @return \TYPO3\CMS\Core\Mail\MailMessage
     */
    public function createEmail($conf)
    {
        /** @var $email \TYPO3\CMS\Core\Mail\MailMessage */
        $email = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $email
            ->setTo($conf['receiver'])
            ->setCc($conf['receiverCc'])
            ->setBcc($conf['receiverBcc'])
            ->setFrom($conf['sender'])
            ->setSubject($conf['subject']);

        return $email;
    }

    /**
     * getEmailBodyObject
     *
     * @param array $conf
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    public function getEmailBodyObject($conf)
    {
        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $emailBodyObject = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $emailBodyObject->setTemplatePathAndFilename($this->getTemplatePath('Email/'.$conf('template').'.html'));
        $emailBodyObject->setLayoutRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Layouts',
        ]);
        $emailBodyObject->setPartialRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials',
        ]);
        $emailBodyObject->assignMultiple($conf['variables']);

        return $emailBodyObject;
    }


    /**
     * Generate and send ICS Invitation.
     *
     * @param $conf
     * @return bool
     *
     * @internal param string $Template file in Templates/Email/
     */
    public function sendIcsInvitation($conf)
    {

        $email = $this->createEmail($conf);
        $email->setReplyTo($conf['replyTo']);
        $email->setBody($conf['attachment']['content'], 'text/calendar');


        $headers = $email->getHeaders();
        $headers->addTextHeader('Content-class', 'urn:content-classes:calendarmessage');
        $type = $email->getHeaders()->get('Content-Type');
        $type->setValue('text/calendar; method=REQUEST');
        $type->setParameter('charset', 'UTF-8');


        $email->send();

        return $email->isSent();
    }

    /**
     * Return path and filename for a file
     *        respect *RootPaths and *RootPath.
     *
     * @todo Remove this function as soon as StandaloneView supports templaterootpaths ... , maybe TYPO3 6.3 ?
     *
     * @param string $relativePathAndFilename e.g. Email/Name.html
     *
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getTemplatePath($relativePathAndFilename)
    {
        $extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
                );
        if (!empty($extbaseFrameworkConfiguration['view']['templateRootPaths'])) {
            foreach ($extbaseFrameworkConfiguration['view']['templateRootPaths'] as $path) {
                $absolutePath = GeneralUtility::getFileAbsFileName($path);
                if (file_exists($absolutePath.$relativePathAndFilename)) {
                    $absolutePathAndFilename = $absolutePath.$relativePathAndFilename;
                }
            }
        }
        if (empty($absolutePathAndFilename)) {
            $absolutePathAndFilename = GeneralUtility::getFileAbsFileName(
                    'EXT:dated_news/Resources/Private/Templates/'.$relativePathAndFilename
                    );
        }

        return $absolutePathAndFilename;
    }

    /**
     * @param $list
     *
     * @return array
     */
    public function shuffleAssoc($list)
    {
        if (!is_array($list)) {
            return $list;
        }

        $keys = array_keys($list);
        shuffle($keys);
        $random = [];
        foreach ($keys as $key) {
            $random[$key] = $list[$key];
        }

        return $random;
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getPluginConfiguration($id){

        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
        $piFlexformSettings = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'pi_flexform', 'tt_content', 'uid = ' . $id);
        $ffs = GeneralUtility::makeInstance(FlexFormService::class);
        return $ffs->convertFlexFormContentToArray($piFlexformSettings[0]['pi_flexform']);
    }
}
