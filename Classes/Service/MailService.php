<?php

namespace FalkRoeder\DatedNews\Service;

/***
 *
 * This file is part of the "" Extension for TYPO3 CMS.
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

use FalkRoeder\DatedNews\Utility\MiscUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * MailService.
 */
class MailService
{

    /**
     * objectManager.
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     * @inject
     */
    protected $objectManager;

    /**
     * @var \FalkRoeder\DatedNews\Service\FeuserService
     */
    protected $feuserService;


    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    protected $feuser;



    /**
     * constructor
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class);
        $this->feuserService = $this->objectManager->get('FalkRoeder\DatedNews\Service\FeuserService');
        $this->feuser = $this->feuserService->getFrontendUserObject();
    }
    

    /**
     * Generate and send Email.
     *
     * @param array $conf
     * @return bool
     */
    public function send($conf)
    {
        $emailBodyObject = $this->getEmailBodyObject($conf);
        $email = $this->createEmail($conf);
        $email->setCharset($GLOBALS['TSFE']->metaCharset);
        $email->setBody($emailBodyObject->render(), 'text/html');

        if (isset($conf['fileNames']) && is_array($conf[$fileNames])) {
            foreach ($fileNames as $fileName) {
                if (trim($fileName) != '') {
                    $email->attach(\Swift_Attachment::fromPath('fileadmin' . $fileName));
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
            ->setTo($conf['recipients'])
            ->setCc($conf['recipientsCc'])
            ->setBcc($conf['recipientsBcc'])
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
        $emailBodyObject->setTemplatePathAndFilename($this->getTemplatePath('Email/' . $conf['template'] . '.html'));
        $emailBodyObject->setLayoutRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Layouts',
        ]);
        $emailBodyObject->setPartialRootPaths([
            'default' => ExtensionManagementUtility::extPath('dated_news') . 'Resources/Private/Partials',
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
                if (file_exists($absolutePath . $relativePathAndFilename)) {
                    $absolutePathAndFilename = $absolutePath . $relativePathAndFilename;
                }
            }
        }
        if (empty($absolutePathAndFilename)) {
            $absolutePathAndFilename = GeneralUtility::getFileAbsFileName(
                'EXT:dated_news/Resources/Private/Templates/' . $relativePathAndFilename
            );
        }
        return $absolutePathAndFilename;
    }

    /**
     * sendMail to applyer, admins
     * and authors and the ICS invitation
     * if booking is confirmed.
     *
     * @param \GeorgRinger\News\Domain\Model\News            $news           news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @param $settings
     * @param bool $confirmation
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function sendMail(\FalkRoeder\DatedNews\Domain\Model\Application $newApplication, $settings, \GeorgRinger\News\Domain\Model\News $news = null, $confirmation = false)
    {

        // from
        if (!empty($settings['senderMail'])) {
            $sender = [$settings['senderMail'] => $settings['senderName']];
        } else {
            return $message = [
                'message' => 'LocalizationUtility::translate(\'applicationSendMessageGeneralError\', \'dated_news\', [\'subject\' => \'(no sendermail configured.)\'])',
                'status' => 'applicationSendStatusGeneralErrorStatus',
                'type' => 'ERROR'
            ];
        }



        //validate Mailadress of applyer
        $applyer = [];
        if (
            $this->feuser &&
            GeneralUtility::validEmail($this->feuser->getEmail())
        ) {
            $applyer = [
                $this->feuser->getEmail() => $this->feuser->getLastName() . ', ' . $this->feuser->getFirstName(),
            ];
        } elseif (GeneralUtility::validEmail($newApplication->getEmail())) {
            $applyer = [
                $newApplication->getEmail() => $newApplication->getName() . ', ' . $newApplication->getSurname(),
            ];
        } else {
            return $message = [
                'message' => 'applicationSendMessageNoApplyerEmail',
                'status' => 'applicationSendMessageNoApplyerEmailStatus',
                'type' => 'ERROR',
                'forward' => true
            ];
        }

        //generell event infos
        if ($news->getRecurrence() > 0) {
            $events = $newApplication->getRecurringevents();
            $events->rewind();
            $event = $events->current();
            $eventstart = $event->getEventstart();
            $eventend = $event->getEventend();
            $newsLocation = $news->getLocations();
        } else {
            $eventstart = $news->getEventstart();
            $eventend = $news->getEventend();
            $newsLocation = $news->getLocations();
            $event = null;
        }
        $subject = $this->getEmailSubject($news, $settings, $eventstart, $newsLocation);
        //general mailConf
        $sendMailConf = [
            'recipientsCc' => [],
            'recipientsBcc' => [],
            'sender' => $sender,
            'subject' => LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', ['subject' => $subject]),
            'variables' => ['newApplication' => $newApplication, 'news' => $news, 'recurrence' => $event, 'settings' => $settings],
        ];

        // send email Customer
        if($confirmation) {
            $sendMailConf = [
                'template' => 'MailConfirmationApplyer',
                'recipients' => $applyer
            ];
        } else {
            $filenames = $this->getFileNamesToSend($settings);
            $sendMailConf['template'] = 'MailApplicationApplyer';
            $sendMailConf['recipients'] = $applyer;
            $sendMailConf['fileNames'] = $filenames;

        }
        if (!$this->send($sendMailConf)) {
            return $message = [
                'message' => 'applicationSendMessageApplyerError',
                'status' => 'applicationSendStatusApplyerErrorStatus',
                'type' => 'ERROR'
            ];
        } else {
            if ($confirmation === false) {
                return $message = [
                    'message' => 'applicationSendMessage',
                    'status' => 'applicationSendStatus',
                    'type' => 'OK'
                ];
            }
        }

        //Send to admins etc only when booking / application confirmed
        if ($confirmation === true) {
            $recipients = $this->getAdminAndAuthorRecipients($news, $settings);

            if (!count($recipients)) {
                return $message = [
                    'message' => 'applicationSendMessageNoRecipients',
                    'status' => 'applicationSendMessageNoRecipientsStatus',
                    'type' => 'ERROR',
                    'forward' => true
                ];
            }

            // send email to authors and Plugins mail addresses
            $sendMailConf['template'] = 'MailApplicationNotification';
            $sendMailConf['recipients'] = $recipients;
            if ($this->send($sendMailConf)) {
                return $message = [
                    'message' => 'applicationConfirmed',
                    'status' => 'applicationConfirmedStatus',
                    'type' => 'OK'
                ];
            } else {
                return $message = [
                    'message' => 'applicationSendMessageGeneralError',
                    'status' => 'applicationSendStatusGeneralErrorStatus',
                    'type' => 'ERROR'
                ];
            }
        }

        //create ICS File and send invitation
        if ($confirmation === true && $settings['ics']) {
            $newsTitle = $news->getTitle();
            $icsLocation = '';
            $i = 0;
            if (isset($newsLocation) && count($newsLocation) < 2) {
                foreach ($newsLocation as $location) {
                    $icsLocation .= $location->getName() . ', ' . $location->getAddress() . ', ' . $location->getZip() . ' ' . $location->getCity() . ', ' . $location->getCountry();
                }
            } else {
                foreach ($newsLocation as $location) {
                    $i++;
                    if ($i === 1) {
                        $icsLocation .= $location->getName();
                    } else {
                        $icsLocation .= ', ' . $location->getName();
                    }
                }
            }

            $properties = [
                'dtstart'   => $eventstart->getTimestamp(),
                'dtend'     => $eventend->getTimestamp(),
                'location'  => $icsLocation,
                'summary'   => $newsTitle,
                'organizer' => $settings['senderMail'],
                'attendee'  => $applyerMail,

            ];

            //add description
            $description = $this->getIcsDescription($news, $settings, $event);
            if ($description !== false) {
                $properties['description'] = $description;
            }

            $ics = new \FalkRoeder\DatedNews\Service\IcsService($properties);
            $icsAttachment = [
                'content' => $ics->to_string(),
                'name'    => str_replace(' ', '_', $newsTitle),

            ];
            $sendMailConf['template'] = 'MailConfirmationApplyer';
            $sendMailConf['recipients'] = $applyer;
            $sendMailConf['subject'] = LocalizationUtility::translate('tx_datednews_domain_model_application.invitation_subject', 'dated_news', ['subject' => $subject]);
            $sendMailConf['attachment'] = $icsAttachment;
            $sendMailConf['replyTo'] = [substr_replace($settings['senderMail'], 'noreply', 0, strpos($settings['senderMail'], '@')) => $settings['senderName']];


            if (!$this->sendIcsInvitation($sendMailConf)) {
                return $message = [
                    'message' => 'applicationSendMessageApplyerError',
                    'status' => 'applicationSendStatusApplyerErrorStatus',
                    'type' => 'ERROR'
                ];
            }
        }
    }
    
    /**
     * getFileNamesToSend
     *
     * @return array
     */
    public function getFileNamesToSend()
    {
        $cObj = $this->configurationManager->getContentObject();
        $uid = $cObj->data['uid'];
        $fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
        $fileObjects = $fileRepository->findByRelation('tt_content', 'tx_datednews', $uid);
        $filenames = [];
        if (is_array($fileObjects)) {
            foreach ($fileObjects as $file) {
                $filenames[] = $file->getOriginalFile()->getIdentifier();
            }
        }

        //FAL files does not work with gridelements, so add possibility to add file paths to TS. see https://forge.typo3.org/issues/71436
        $filesFormTS = explode(',', $settings['dated_news']['filesForMailToApplyer']);
        foreach ($filesFormTS as $fileName) {
            $filenames[] = trim($fileName);
        }
        return $filenames;
    }

    /**
     * getEmailSubject
     *
     * @return string
     */
    public function getEmailSubject($news, $settings, $eventstart, $newsLocation)
    {
        $subjectFields = explode(',', $settings['dated_news']['emailSubjectFields']);
        $subject = '';
        $fieldIterator = 0;
        foreach ($subjectFields as $field) {
            switch (trim($field)) {
                case 'title':
                    if ($fieldIterator > 0) {
                        $subject .= ', ';
                    }
                    $subject .= $news->getTitle();
                    break;
                case 'eventstart':
                    $subject .= LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject_eventstart', 'dated_news');
                    $subject .= $eventstart->format($settings['dated_news']['emailSubjectDateFormat']);
                    break;
                case 'locationname':
                    $locationIterator = 0;
                    if (isset($newsLocation)) {
                        $subject .= LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject_locationname', 'dated_news');
                        foreach ($newsLocation as $location) {
                            $locationIterator++;
                            $subject = $locationIterator === 1 ? $subject . $location->getName() : $subject . ', ' . $location->getName();
                        }
                    }
                    break;
                default:
            }
        }
    }

    /**
     * getEmailRecipients
     *
     * @param $news
     * @return array
     */
    public function getAdminAndAuthorRecipients($news, $settings)
    {
        /** @var $to array Array to collect all the receipients */
        $to = [];

        //news Author
        if ($settings['notificateAuthor']) {
            $authorEmail = $news->getAuthorEmail();
            if (!empty($authorEmail)) {
                $to[] = [
                    'email' => $authorEmail,
                    'name'  => $news->getAuthor(),
                ];
            }
        }

        //Plugins notification mail addresses
        if (!empty($settings['notificationMail'])) {
            $tsmails = explode(',', $settings['notificationMail']);
            foreach ($tsmails as $tsmail) {
                $to[] = [
                    'email' => trim($tsmail),
                    'name'  => '',
                ];
            }
        }

        $recipients = [];
        if (is_array($to)) {
            foreach ($to as $pair) {
                if (GeneralUtility::validEmail($pair['email'])) {
                    if (trim($pair['name'])) {
                        $recipients[$pair['email']] = $pair['name'];
                    } else {
                        $recipients[] = $pair['email'];
                    }
                }
            }
        }
        return $recipients;
    }

    /**
     * getIcsDescription.
     *
     * creates the ICS description for the
     * invitation send to Customer
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param $event
     * @param array $settings
     *
     * @return bool|string
     */
    public function getIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $settings, $event = null)
    {
        switch ($settings['icsDescriptionField']) {
            case 'Teaser':
                $result = $this->getEventTeaser($news, $event);
                break;
            case 'Description':
                $description = $news->getDescription();
                if (($description == strip_tags($description))) {
                    $result = $description;
                }
                break;
            case 'Url':
                $uri = $this->linkToNewsItem->getLink($news, $settings);
                $result = LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', ['url' => $uri]);
                break;
            case 'Custom':
                $result = $this->getCustomIcsDescription($news, $settings, $event);
                break;
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * getCustomIcsDescription
     *
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @param $settings
     * @param $event
     * @return string | bool
     */
    public function getCustomIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $settings, $event = null)
    {
        $result = false;
        if (trim($settings['icsDescriptionCustomField']) !== '') {
            if ($news->getRecurrence() > 0 && !is_null($event)) {
                $object = $event;
            } else {
                $object = $news;
            }
            $func = 'get' . ucfirst(trim($settings['icsDescriptionCustomField']));
            if (method_exists($object, $func) === true) {
                $description = $object->{$func}();
                if (trim($description) === '' || $description === strip_tags($description)) {
                    $result = $description;
                }
            }
        }

        return $result;
    }

    /**
     * getEventTeaser
     *
     * @param \GeorgRinger\News\Domain\Model\News $news
     * @param $event
     * @return string | bool
     */
    public function getEventTeaser(\GeorgRinger\News\Domain\Model\News $news, $event = null)
    {
        $result = false;
        if ($news->getRecurrence() > 0 && !is_null($event)) {
            $teaser = $event->getTeaser();
        } else {
            $teaser = $news->getTeaser();
        }
        if ($teaser == strip_tags($teaser)) {
            $result = $teaser;
        }
        return $result;
    }
}


