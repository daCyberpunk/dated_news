<?php

namespace FalkRoeder\DatedNews\Utility;

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
     * @param \string Template file in Templates/Email/
     * @param \array  $receiver    Combination of Email => Name
     * @param \array  $receiverCc  Combination of Email => Name
     * @param \array  $receiverBcc Combination of Email => Name
     * @param \array  $sender      Combination of Email => Name
     * @param \string $subject     Mail subject
     * @param \array  $variables   Variables for assignMultiple
     * @param $fileNames
     *
     * @return \bool Mail was sent?
     */
    public function sendEmail($template, $receiver, $receiverCc, $receiverBcc, $sender, $subject, $variables, $fileNames)
    {

        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $emailBodyObject = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $emailBodyObject->setTemplatePathAndFilename($this->getTemplatePath('Email/'.$template.'.html'));
        $emailBodyObject->setLayoutRootPaths([
                'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Layouts',
        ]);
        $emailBodyObject->setPartialRootPaths([
                'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials',
        ]);
        $emailBodyObject->assignMultiple($variables);

        $email = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $email
                ->setTo($receiver)
                ->setCc($receiverCc)
                ->setBcc($receiverBcc)
                ->setFrom($sender)
                ->setSubject($subject)
                ->setCharset($GLOBALS['TSFE']->metaCharset)
                ->setBody($emailBodyObject->render(), 'text/html');

        if ($fileNames && is_array($fileNames)) {
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
     * Generate and send ICS Invitation.
     *
     * @param $template
     * @param \array  $receiver      Combination of Email => Name
     * @param \array  $receiverCc    Combination of Email => Name
     * @param \array  $receiverBcc   Combination of Email => Name
     * @param \array  $replyTo       Combination of Email => Name
     * @param \array  $sender        Combination of Email => Name
     * @param \string $subject       Mail subject
     * @param \array  $variables     Variables for assignMultiple
     * @param array   $icsAttachment
     *
     * @return bool Mail was sent?
     *
     * @internal param string $Template file in Templates/Email/
     */
    public function sendIcsInvitation($template, $receiver, $receiverCc, $receiverBcc, $sender, $subject, $variables, $icsAttachment, $replyTo)
    {

        /** @var $emailBodyObject \TYPO3\CMS\Fluid\View\StandaloneView */
        $emailBodyObject = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
        $emailBodyObject->setTemplatePathAndFilename($this->getTemplatePath('Email/'.$template.'.html'));
        $emailBodyObject->setLayoutRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Layouts',
        ]);
        $emailBodyObject->setPartialRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news').'Resources/Private/Partials',
        ]);
        $emailBodyObject->assignMultiple($variables);

        $email = $this->objectManager->get('TYPO3\\CMS\\Core\\Mail\\MailMessage');
        $email
            ->setTo($receiver)
            ->setCc($receiverCc)
            ->setBcc($receiverBcc)
            ->setReplyTo($replyTo)
            ->setFrom($sender)
            ->setSubject($subject)
            ->setBody($icsAttachment['content'], 'text/calendar');

        $headers = $email->getHeaders();
        $headers->addTextHeader('Content-class', 'urn:content-classes:calendarmessage');
        $type = $email->getHeaders()->get('Content-Type');
        $type->setValue('text/calendar; method=REQUEST');
        $type->setParameter('charset', 'UTF-8');

        //might not be needed bc ICS invitation will be send
        /*if(!empty($icsAttachment)){
            $icsFile = \Swift_Attachment::newInstance()
                ->setFilename($icsAttachment['name'] . ".ics")
                ->setContentType('text/calendar;charset=UTF-8;name="' . $icsAttachment['name'] . '.ics"; method=REQUEST')
                ->setBody($icsAttachment['content'])
                ->setDisposition('attachment;filename=' . $icsAttachment['name'] . '.ics');
            $email->attach($icsFile);
        }*/

        $email->send();

        return $email->isSent();
    }

    /**
     * Return path and filename for a file
     * 		respect *RootPaths and *RootPath.
     *
     *@todo Remove this function as soon as StandaloneView supports templaterootpaths ... , maybe TYPO3 6.3 ?
     *
     * @param string $relativePathAndFilename e.g. Email/Name.html
     *
     * @return string
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
        $settings = $ffs->convertFlexFormContentToArray($piFlexformSettings[0]['pi_flexform']);

        return $settings;
    }
}
