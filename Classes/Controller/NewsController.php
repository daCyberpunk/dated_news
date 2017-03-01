<?php
namespace FalkRoeder\DatedNews\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Falk Röder <mail@falk-roeder.de>
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

use GeorgRinger\News\Utility\Cache;
use GeorgRinger\News\Utility\Page;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FalkRoeder\DatedNews\Controller\NewsController
 */
class NewsController extends \GeorgRinger\News\Controller\NewsController
{

    const SIGNAL_NEWS_CALENDAR_ACTION = 'calendarAction';
    const SIGNAL_NEWS_EVENTDETAIL_ACTION = 'eventDetailAction';
    const SIGNAL_NEWS_CREATEAPPLICATION_ACTION = 'createApplicationAction';
    const SIGNAL_NEWS_CONFIRMAPPLICATION_ACTION = 'confirmApplicationAction';

    /**
     * applicationRepository
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository = NULL;

    /**
     * Misc Functions
     *
     * @var \FalkRoeder\DatedNews\Utility\Div
     * @inject
     */
    protected $div;

    /**
     * @var \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param \TYPO3\CMS\Core\Page\PageRenderer $pageRenderer
     */
    public function injectPageRenderer(\TYPO3\CMS\Core\Page\PageRenderer $pageRenderer) {
        $this->pageRenderer = $pageRenderer;
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     */
    public function initializeCreateApplicationAction(){
        foreach ($this->arguments as $argument) {
            $argumentName = $argument->getName();
            if ($this->request->hasArgument($argumentName)) {
                if($argumentName === 'newApplication' && $this->request->getArgument($argumentName) === '') {
                    $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
                } else {
                    $argument->setValue($this->request->getArgument($argumentName));
                }
            } else {
                $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
            }
        }
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException
     */
    public function initializeConfirmApplicationAction(){
        foreach ($this->arguments as $argument) {
            $argumentName = $argument->getName();
            if ($this->request->hasArgument($argumentName)) {
                $argument->setValue($this->request->getArgument($argumentName));
            } else {
                $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
            }
        }
    }

    /**
     * Calendar view
     *
     * @param array $overwriteDemand
     * @return void
     */
    public function calendarAction(array $overwriteDemand = null)
    {

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);
        if ($this->settings['disableOverrideDemand'] != 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }
        $newsRecords = $this->newsRepository->findDemanded($demand);
        // Escaping quotes, doublequotes and backslashes for use in Javascript
        foreach ($newsRecords as $news) {
            $news->setTitle(addslashes($news->getTitle()));
            $news->setTeaser(addslashes($news->getTeaser()));
            $news->setDescription(addslashes($news->getDescription()));
            $news->setBodytext(addslashes($news->getBodytext()));
        }
        $this->addCalendarJSLibs($this->settings['dated_news']['includeJQuery'], $this->settings['dated_news']['jsFile'], $this->settings['qtips']);
        $this->addCalendarCss($this->settings['dated_news']['cssFile']);

        $assignedValues = [
            'news' => $newsRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
        ];

        $filterValues = [];
        if($this->settings['showCategoryFilter'] === '1'){
            $filterValues = array_merge($filterValues,$this->getCategoriesOfNews($newsRecords));
        }
        if($this->settings['showTagFilter'] === '1'){
            $filterValues = array_merge($filterValues,$this->getTagsOfNews($newsRecords));
        }
        if(!empty($filterValues)){
            if ($this->settings['sortingFilterlist'] === 'shuffle'){
                $assignedValues['filterValues'] = $this->div->shuffle_assoc($filterValues);
            } else {
                ksort($filterValues, SORT_LOCALE_STRING);
                $assignedValues['filterValues'] = $filterValues;
            }
        }

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CALENDAR_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);
        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * Single view of a news record
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param int $currentPage current page for optional pagination
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function eventDetailAction(\GeorgRinger\News\Domain\Model\News $news = null, $currentPage = 1, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication = null)
    {
        if (is_null($news)) {
            $previewNewsId = ((int)$this->settings['singleNews'] > 0) ? $this->settings['singleNews'] : 0;
            if ($this->request->hasArgument('news_preview')) {
                $previewNewsId = (int)$this->request->getArgument('news_preview');
            }

            if ($previewNewsId > 0) {
                if ($this->isPreviewOfHiddenRecordsEnabled()) {
                    $GLOBALS['TSFE']->showHiddenRecords = true;
                    $news = $this->newsRepository->findByUid($previewNewsId, false);
                } else {
                    $news = $this->newsRepository->findByUid($previewNewsId);
                }
            }
        }

        if (is_a($news,
                'GeorgRinger\\News\\Domain\\Model\\News') && $this->settings['detail']['checkPidOfNewsRecord']
        ) {
            $news = $this->checkPidOfNewsRecord($news);
        }

        if (is_null($news) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $this->addCalendarCss($this->settings['dated_news']['cssFile']);

        $applicationsCount = $this->applicationRepository->countReservedSlotsForNews($news->getUid());

        $news->setSlotsFree((int)$news->getSlots() - $applicationsCount);
        $slotoptions = [];
        $i = 1;
        while ($i <= $news->getSlotsFree()) {
            $slotoption = new \stdClass();
            $slotoption->key = $i;
            $slotoption->value = $i;
            $slotoptions[] = $slotoption;
            $i++;
        }

        $assignedValues = [
            'newsItem' => $news,
            'currentPage' => (int)$currentPage,
            'demand' => $demand,
            'newApplication' => $newApplication,
            'slotoptions' => $slotoptions,
            'formTimestamp' => time() // for form reload and doubled submit prevention
        ];

        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_EVENTDETAIL_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);

        Page::setRegisterProperties($this->settings['detail']['registerProperties'], $news);
        if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
            Cache::addCacheTagsByNewsRecords([$news]);
        }
    }

    /**
     * action createApplication
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @return void
     */
    public function createApplicationAction(\GeorgRinger\News\Domain\Model\News $news = null, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication = null) {

        if (is_null($news)) {
            $previewNewsId = ((int)$this->settings['singleNews'] > 0) ? $this->settings['singleNews'] : 0;
            if ($this->request->hasArgument('news_preview')) {
                $previewNewsId = (int)$this->request->getArgument('news_preview');
            }

            if ($previewNewsId > 0) {
                if ($this->isPreviewOfHiddenRecordsEnabled()) {
                    $GLOBALS['TSFE']->showHiddenRecords = true;
                    $news = $this->newsRepository->findByUid($previewNewsId, false);
                } else {
                    $news = $this->newsRepository->findByUid($previewNewsId);
                }
            }
        }

        if (is_a($news,
                'GeorgRinger\\News\\Domain\\Model\\News') && $this->settings['detail']['checkPidOfNewsRecord']
        ) {
            $news = $this->checkPidOfNewsRecord($news);
        }

        if (is_null($news) && isset($this->settings['detail']['errorHandling'])) {
            $this->handleNoNewsFoundError($this->settings['detail']['errorHandling']);
        }

        // prevents form submitted more than once
        if($this->applicationRepository->isFirstFormSubmission($newApplication->getFormTimestamp())){

            $newApplication->setPid($news->getPid());
            $newApplication->setHidden(TRUE);

            //set creationdate
            $date = new \DateTime();
            $newApplication->setCrdate($date->getTimestamp());

            //set total depending on either customer is an early bird or not and on earyBirdPrice is set
            if($news->getEarlyBirdPrice() != '' && $this->settings['earlyBirdDays'] != '' && $this->settings['earlyBirdDays'] != '0'){
                $earlybirdDate = clone $news->getEventstart();
                if($this->settings['earlyBirdDays'] != '') {
                    $earlybirdDate->setTime(0,0,0)->sub(new \DateInterval('P'.$this->settings['earlyBirdDays'].'D'));
                }

                $today = new \DateTime();
                $today->setTime(0,0,0);

                if($earlybirdDate >= $today) {
                    $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',','.',$news->getEarlyBirdPrice())));
                } else {
                    $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',','.',$news->getPrice())));
                }
            } else {
                $newApplication->setCosts($newApplication->getReservedSlots() * floatval(str_replace(',','.',$news->getPrice())));
            }

            $newApplication->addEvent($news);
            $this->applicationRepository->add($newApplication);

            $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
            $persistenceManager->persistAll();
            $newApplication->setTitle($news->getTitle()." - ".$newApplication->getName() . ' ' . $newApplication->getSurname() . '-' . $newApplication->getUid());
            $this->applicationRepository->update($newApplication);
            $persistenceManager->persistAll();

            // clearing cache of detailpage and currentpage because otherwise free slots are not updated in view
            // and the form is always sending the same if another booking is made
            $this->cacheService->clearPageCache(
                [
                    $this->settings['detailPid'], 
                    intval($GLOBALS['TSFE']->id)
                ]
            );

            $demand = $this->createDemandObjectFromSettings($this->settings);
            $demand->setActionAndClass(__METHOD__, __CLASS__);

            $assignedValues = [
                'newsItem' => $news,
                'demand' => $demand,
                'newApplication' => $newApplication,
                'settings' => $this->settings
            ];

            $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CREATEAPPLICATION_ACTION, $assignedValues);
            $this->view->assignMultiple($assignedValues);

            Page::setRegisterProperties($this->settings['detail']['registerProperties'], $news);
            if (!is_null($news) && is_a($news, 'GeorgRinger\\News\\Domain\\Model\\News')) {
                Cache::addCacheTagsByNewsRecords([$news]);
            }
            $this->sendMail($news, $newApplication, $this->settings);
        } else {
            $this->flashMessageService('applicationSendMessageAllreadySent','applicationSendMessageAllreadySentStatus','ERROR' );
        }
    }

    /**
     * action confirmApplication
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @return void
     */
    public function confirmApplicationAction(\FalkRoeder\DatedNews\Domain\Model\Application $newApplication)
    {
        $assignedValues = [];
        if(is_null($newApplication)){
            $this->flashMessageService('applicationNotFound','applicationNotFoundStatus','ERROR' );
        } else {
            //vor confirmation link validity check
            $date = new \DateTime();
            $hoursSinceBookingRequestSent = ($date->getTimestamp() - $newApplication->getCrdate()) / 3600;
            
            if($newApplication->isConfirmed() === TRUE){
                //was allready confirmed
                $this->flashMessageService('applicationAllreadyConfirmed','applicationAllreadyConfirmedStatus','INFO' );
            } else if($this->settings['dated_news']['validDaysConfirmationLink'] * 24 < $hoursSinceBookingRequestSent){
                //confirmation link not valid anymore
                $this->flashMessageService('applicationConfirmationLinkUnvalid','applicationConfirmationLinkUnvalidStatus','ERROR' );
            } else {
                //confirm
                $newApplication->setConfirmed(TRUE);
                $newApplication->setHidden(FALSE);
                $this->applicationRepository->update($newApplication);
                $persistenceManager = GeneralUtility::makeInstance("TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager");
                $persistenceManager->persistAll();
                $events = $newApplication->getEvents();
                $events->rewind();
                $this->sendMail($events->current(), $newApplication, $this->settings, TRUE);
                $assignedValues = [
                    'newApplication' => $newApplication,
                    'newsItem' => $events->current()
                ];
            }
        }

        // clearing cache of detailpage and currentpage because otherwise free slots are not updated in view
        // and the form is always sending the same if another booking is made
        $this->cacheService->clearPageCache(
            [
                $this->settings['detailPid'],
                intval($GLOBALS['TSFE']->id)
            ]
        );
        
        $assignedValues = $this->emitActionSignal('NewsController', self::SIGNAL_NEWS_CONFIRMAPPLICATION_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);

    }

    /**
     * sendMail to applyer, admins
     * and authors and the ICS invitation
     * if booking is confirmed
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param \FalkRoeder\DatedNews\Domain\Model\Application $newApplication
     * @param $settings
     * @param bool $confirmation
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function sendMail(\GeorgRinger\News\Domain\Model\News $news = NULL, \FalkRoeder\DatedNews\Domain\Model\Application $newApplication, $settings, $confirmation = FALSE){

        // from
        $sender = [];
        if (!empty($this->settings['senderMail'])) {
            $sender = (array($this->settings['senderMail'] => $this->settings['senderName']));
        }

        //validate Mailadress of applyer
        $applyerMail = $newApplication->getEmail();

        $applyer = [];
        if (is_string($applyerMail) && GeneralUtility::validEmail($applyerMail)) {
            $applyer = [
                $newApplication->getEmail() => $newApplication->getName() . ', ' . $newApplication->getSurname()
            ];
        } else {
            $this->flashMessageService('applicationSendMessageNoApplyerEmail','applicationSendMessageNoApplyerEmailStatus','ERROR' );
            $this->forward('eventDetail', NULL, NULL, array('news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication));
        }

        //get filenames of flexform files to send to applyer
        if($confirmation === FALSE){
            $cObj = $this->configurationManager->getContentObject();
            $uid = $cObj->data['uid'];
            $fileRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
            $fileObjects = $fileRepository->findByRelation('tt_content', 'tx_datednews', $uid);
            $filenames = [];
            if(is_array($fileObjects)){
                foreach ($fileObjects as $file){
                    $filenames[] = $file->getOriginalFile()->getIdentifier();
                }
            }

            //FAL files does not work with gridelements, so add possibility to add file paths to TS. see https://forge.typo3.org/issues/71436
            $filesFormTS = explode(',', $this->settings['dated_news']['filesForMailToApplyer']);
            foreach ($filesFormTS as $fileName) {
                $filenames[] = trim($fileName);
            }

        } else {
            $filenames = [];
        }

        $recipientsCc = [];
        $recipientsBcc = [];

        // send email Customer
        $customerMailTemplate = $confirmation === TRUE ? 'MailConfirmationApplyer' : 'MailApplicationApplyer';
        if (!$this->div->sendEmail(
            $customerMailTemplate,
            $applyer,
            $recipientsCc,
            $recipientsBcc,
            $sender,
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', array('news' => $news->getTitle())),
            array('newApplication' => $newApplication, 'news' => $news, 'settings' => $settings),
            $filenames
        )) {
            $this->flashMessageService('applicationSendMessageApplyerError','applicationSendStatusApplyerErrorStatus','ERROR' );
        } else {
            if($confirmation === FALSE) {
                $this->flashMessageService('applicationSendMessage','applicationSendStatus','OK' );
            }
        }

        
        
        //Send to admins etc only when booking / application confirmed
        if($confirmation === TRUE){
            /** @var $to array Array to collect all the receipients */
            $to = [];

            //news Author
            if($this->settings['notificateAuthor']){
                $authorEmail = $news->getAuthorEmail();
                if (!empty($authorEmail)) {
                    $to [] = [
                        'email' => $authorEmail,
                        'name' => $news->getAuthor()
                    ];
                }
            }

            //Plugins notification mail addresses
            if (!empty($this->settings['notificationMail'])) {
                $tsmails = explode(',',$this->settings['notificationMail']);
                foreach($tsmails as $tsmail) {
                    $to [] = [
                        'email' => trim($tsmail),
                        'name' => ''
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

            if (!count($recipients)) {
                $this->flashMessageService('applicationSendMessageNoRecipients','applicationSendMessageNoRecipientsStatus','ERROR' );
                $this->forward('eventDetail', NULL, NULL, array('news' => $news, 'currentPage' => 1, 'newApplication' => $newApplication));
            }

            // send email to authors and Plugins mail addresses
            if ($this->div->sendEmail(
                'MailApplicationNotification',
                $recipients,
                $recipientsCc,
                $recipientsBcc,
                $applyer,
                \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.notificationemail_subject', 'dated_news', array('news' => $news->getTitle())),
                array('newApplication' => $newApplication, 'news' => $news, 'settings' => $this->settings),
                array()
            )) {
                $this->flashMessageService('applicationConfirmed','applicationConfirmedStatus','OK' );
            } else {
                $this->flashMessageService('applicationSendMessageGeneralError','applicationSendStatusGeneralErrorStatus','ERROR' );
            }
            
            if($this->settings['ics']){
                //create ICS File and send invitation
                $newsTitle = $news->getTitle();
                $icsLocation = '';
                $newsLocation = $news->getLocations();
                $i= 0;
                if(isset($newsLocation) && count($newsLocation) < 2) {
                    foreach ($newsLocation as $location){
                        $icsLocation .= $location->getName() . ', ' . $location->getAddress() . ', ' .$location->getZip() . ' ' . $location->getCity() . ', ' . $location->getCountry();
                    }
                } else {
                    foreach ($newsLocation as $location){
                        $i++;
                        if($i ===1){
                            $icsLocation .= $location->getName();
                        } else {
                            $icsLocation .= ', ' . $location->getName();
                        }

                    }
                }
                $properties = [
                    'dtstart' => $news->getEventstart()->getTimestamp(),
                    'dtend' => $news->getEventend()->getTimestamp(),
                    'location' => $icsLocation,
                    'summary' => $newsTitle,
                    'organizer' => $this->settings['senderMail'],
                    'attendee' => $applyerMail

                ];

                //add description
                $description = $this->getIcsDescription($news, $settings);
                if($description !== FALSE){
                    $properties['description'] = $description;
                }

                $ics = new \FalkRoeder\DatedNews\Services\ICS($properties);
                $icsAttachment = [
                    'content' => $ics->to_string(),
                    'name' => str_replace(' ','_',$newsTitle),

                ];
                $senderMail = substr_replace($this->settings['senderMail'],'noreply',0,strpos($this->settings['senderMail'], '@'));
                if (!$this->div->sendIcsInvitation(
                    'MailConfirmationApplyer',
                    $applyer,
                    $recipientsCc,
                    $recipientsBcc,
                    array($senderMail => $this->settings['senderName']),
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.invitation_subject', 'dated_news', array('news' => $news->getTitle())),
                    array('newApplication' => $newApplication, 'news' => $news, 'settings' => $settings),
                    $icsAttachment
                )) {
                    $this->flashMessageService('applicationSendMessageApplyerError','applicationSendStatusApplyerErrorStatus','ERROR' );
                } else {
                    if($confirmation === FALSE) {
                        $this->flashMessageService('applicationSendMessage','applicationSendStatus','OK' );
                    }
                }
            }
        }
    }

    /**
     * adds calendar and event detail specific default css
     *
     * @param string $pathToCss
     */
    public function addCalendarCss($pathToCss = ''){
        $this->pageRenderer->addCssFile('/typo3conf/ext/dated_news/Resources/Public/Plugins/fullcalendar/fullcalendar.min.css');
        $this->pageRenderer->addCssFile('/typo3conf/ext/dated_news/Resources/Public/Plugins/qtip3/jquery.qtip.min.css');
//        $this->pageRenderer->addHeaderData('<link rel="stylesheet" type="text/css" href="/typo3conf/ext/dated_news/Resources/Public/Plugins/fullcalendar/fullcalendar.min.css" media="all">');
//        $this->pageRenderer->addHeaderData('<link rel="stylesheet" type="text/css" href="/typo3conf/ext/dated_news/Resources/Public/Plugins/qtip3/jquery.qtip.min.css" media="all">');
        $pathToCss = str_replace('EXT:','/typo3conf/ext/',$pathToCss);
        $this->pageRenderer->addCssFile($pathToCss);
//        $this->pageRenderer->addHeaderData('<link rel="stylesheet" type="text/css" href="'.$pathToCss.'" media="all">');
    }

    /**
     * adds calendar specific default js
     * and if in typoscript settings set to true, also jQuery
     *
     * @param string $jquery
     * @param string $pathToJS
     */
    public function addCalendarJSLibs($jquery = '0', $pathToJS = '', $qtips = '0'){
        $libs = [
            'jQuery' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js',
            'xmoment' => 'fullcalendar/lib/moment.min.js',
            'xfullcalendar' => 'fullcalendar/fullcalendar.min.js',
            'xlangall' => 'fullcalendar/lang-all.js',
            'xqtip' => 'qtip3/jquery.qtip.min.js',
            'dated_news' => str_replace('EXT:','/typo3conf/ext/',$pathToJS)
        ];
        $extPluginPath = 'typo3conf/ext/dated_news/Resources/Public/Plugins/';
        define('NEW_LINE', "\n");
        $contents = [];
        foreach ($libs as $name => $path) {
            if($name == 'jQuery' && $jquery != "1") {
                continue;
            }
            if($name !== 'jQuery' && $name != 'dated_news') {
                $path = $extPluginPath . $path;
            }
            if($name !== 'jQuery') {
                $contents[] = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($path);
                continue;
            }
            /*jQuery*/
            $this->pageRenderer->addJsFooterLibrary(
                $name,
                $path,
                'text/javascript',
                TRUE
            );
        }


        //other libs
        $file = 'typo3temp/dated_news.js';

        if (!file_exists(PATH_site . $file)) {
            // writeFileToTypo3tempDir() returns NULL on success (please double-read!)
            $error = GeneralUtility::writeFileToTypo3tempDir(PATH_site . $file, implode($contents, NEW_LINE));
            if ($error !== null) {
                throw new \RuntimeException('Dated News JS file could not be written to ' . $file . '. Reason: ' . $error, 1487439381339);
            }
        }

        $this->pageRenderer->addJsFooterLibrary(
            'dated_news',
            $file,
            'text/javascript',
            TRUE
        );
    }

    /**
     * adds needed flashmessages
     * for informations to user
     *
     * @param \string $messageKey
     * @param \string $statusKey
     * @param \string $level
     *
     * @return void
     */
    function flashMessageService($messageKey, $statusKey, $level) {
        switch ($level) {
            case "NOTICE":
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::NOTICE;
                break;
            case "INFO":
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO;
                break;
            case "OK":
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK;
                break;
            case "WARNING":
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING;
                break;
            case "ERROR":
                $level = \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR;
                break;
        }

        $this->addFlashMessage(
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($messageKey, 'dated_news'),
            \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($statusKey, 'dated_news'),
            $level,
            TRUE
        );
    }

    /**
     * takes categories of all
     * news records shown in calendar and put them into a array
     * also adds the colors which might be specified in
     * category to the array to enable filtering of calendar items
     * with colored buttons by category
     *
     * @param $newsRecords
     * @return array
     */
    public function getCategoriesOfNews($newsRecords) {
        $newsCategories = [];
        foreach ($newsRecords as $news) {
            if($news->isShowincalendar() === TRUE) {
                $categories = $news->getCategories();
                foreach ($categories as $category) {
                    $title = $category->getTitle();
                    $bgColor = $category->getBackgroundcolor();
                    if(!array_key_exists($title, $newsCategories)){
                        $newsCategories[$title] =[];
                        $newsCategories[$title]['count'] = 1;
                        if (trim($bgColor) !== '') {
                            $newsCategories[$title]['color'] = $bgColor;
                        } 
                    } else {
                        $newsCategories[$title]['count'] = $newsCategories[$title]['count'] +1;
                    }
                }
            }
        }

        return $newsCategories;
    }

    /**
     * takes tags of all
     * news records shown in calendar
     * and put them into a array to enable filtering of calendar items by tag
     *
     * @param $newsRecords
     * @return array
     */
    public function getTagsOfNews($newsRecords) {
        $newsTags = [];
        foreach ($newsRecords as $news) {
            if($news->isShowincalendar() === TRUE) {
                $tags = $news->getTags();
                foreach ($tags as $tag) {
                    $title = $tag->getTitle();
                    if(!array_key_exists($title, $newsTags)){
                        $newsTags[$title] =[];
                        $newsTags[$title]['count'] = 1;
                    } else {
                        $newsTags[$title]['count'] = $newsTags[$title]['count'] +1;
                    }
                }
            }
        }

        return $newsTags;
    }

    /**
     * getIcsDescription
     *
     * creates the ICS description for the
     * invitation send to Customer
     *
     * @param \GeorgRinger\News\Domain\Model\News $news news item
     * @param array $settings
     * @return boolean|string
     */
    public function getIcsDescription(\GeorgRinger\News\Domain\Model\News $news, $settings) {
        switch ($settings['icsDescriptionField']) {
            case "Teaser":
                if($news->getTeaser() == strip_tags($news->getTeaser())){
                    return $news->getTeaser();
                } else {
                    return FALSE;
                }
                break;
            case "Description":
                if($news->getDescription() == strip_tags($news->getDescription())){
                    return $news->getDescription();
                } else {
                    return FALSE;
                }
                break;
            case "Url":
                $uri = $this->getLinkToNewsItem($news, $settings);
                return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', array('url' => $uri));

//                if($settings['detailPid']){
//                    $uriBuilder = $this->controllerContext->getUriBuilder();
//                    $uri = $uriBuilder
//                        ->reset()
//                        ->setTargetPageUid($settings['detailPid'])
//                        ->setUseCacheHash(TRUE)
//                        ->setArguments(array('tx_news_pi1' => array('controller' => 'News', 'action' => 'eventDetail', 'news' => $news->getUid())))
//                        ->setCreateAbsoluteUri(TRUE)
//                        ->buildFrontendUri();
//                    return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tx_datednews_domain_model_application.ics_description', 'dated_news', array('url' => $uri));
//                } else {
//                    return FALSE;
//                }
                break;
            case "Custom":
                if(trim($settings['icsDescriptionCustomField']) === '') {
                    return FALSE;
                } else {
                    $func = 'get' . ucfirst(trim($settings['icsDescriptionCustomField']));
                    if(method_exists($news, $func) === TRUE){
                        $description = $news->{$func}();
                        if(trim($description) != '' && $description != strip_tags($description)){
                            return FALSE;
                        } else {
                            return $description;
                        }
                    } else {
                        return FALSE;
                    }
                }
                break;
            default:
                return FALSE;
        }
    }

    /*
     * following stuff is almost full copy of tx_news LinkViewhelper
     * better solution needs to be found(?)
     * maybe put it in an Utility Class and inject it in own LinkViewhelper?
     * or inject LinkViewhelper directly here in Controller?
     * */

    /**
     * @var array
     */
    protected $detailPidDeterminationCallbacks = [
        'flexform' => 'getDetailPidFromFlexform',
        'categories' => 'getDetailPidFromCategories',
        'default' => 'getDetailPidFromDefaultDetailPid',
    ];

    /** @var $cObj ContentObjectRenderer */
    protected $cObj;

    /**
     * Gets detailPid from categories of the given news item. First will be return.
     *
     * @param  array $settings
     * @param  News $newsItem
     * @return int
     */
    protected function getDetailPidFromCategories($settings, $newsItem)
    {
        $detailPid = 0;
        if ($newsItem->getCategories()) {
            foreach ($newsItem->getCategories() as $category) {
                if ($detailPid = (int)$category->getSinglePid()) {
                    break;
                }
            }
        }
        return $detailPid;
    }

    /**
     * Gets detailPid from defaultDetailPid setting
     *
     * @param  array $settings
     * @param  News $newsItem
     * @return int
     */
    protected function getDetailPidFromDefaultDetailPid($settings, $newsItem)
    {
        return (int)$settings['defaultDetailPid'];
    }

    /**
     * Gets detailPid from flexform of current plugin.
     *
     * @param  array $settings
     * @param  News $newsItem
     * @return int
     */
    protected function getDetailPidFromFlexform($settings, $newsItem)
    {
        return (int)$settings['detailPid'];
    }

    /**
     * @param News $newsItem
     * @return int
     */
    protected function getNewsId(\GeorgRinger\News\Domain\Model\News $newsItem)
    {
        $uid = $newsItem->getUid();
        // If a user is logged in and not in live workspace
        if ($GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->workspace > 0) {
            $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getLiveVersionOfRecord('tx_news_domain_model_news',
                $newsItem->getUid());
            if ($record['uid']) {
                $uid = $record['uid'];
            }
        }

        return $uid;
    }

    /**
     * Generate the link configuration for the link to the news item
     *
     * @param \GeorgRinger\News\Domain\Model\News $newsItem
     * @param array $tsSettings
     * @param array $configuration
     * @return array
     */
    protected function getLinkToNewsItem(
        \GeorgRinger\News\Domain\Model\News $newsItem,
        $tsSettings,
        array $configuration = []
    ) {
        if (!isset($configuration['parameter'])) {
            $detailPid = 0;
            $detailPidDeterminationMethods = GeneralUtility::trimExplode(',', $tsSettings['detailPidDetermination'],
                true);

            // if TS is not set, prefer flexform setting
            if (!isset($tsSettings['detailPidDetermination'])) {
                $detailPidDeterminationMethods[] = 'flexform';
            }

            foreach ($detailPidDeterminationMethods as $determinationMethod) {
                if ($callback = $this->detailPidDeterminationCallbacks[$determinationMethod]) {
                    if ($detailPid = call_user_func([$this, $callback], $tsSettings, $newsItem)) {
                        break;
                    }
                }
            }

            if (!$detailPid) {
                $detailPid = $GLOBALS['TSFE']->id;
            }
            $configuration['parameter'] = $detailPid;
        }

        $configuration['forceAbsoluteUrl'] = true;

        $configuration['useCacheHash'] = $GLOBALS['TSFE']->sys_page->versioningPreview ? 0 : 1;
        $configuration['additionalParams'] .= '&tx_news_pi1[news]=' . $this->getNewsId($newsItem);

        // action is set to "detail" in original Viewhelper, but we overwiritten this action
        if ((int)$tsSettings['link']['skipControllerAndAction'] !== 1) {
            $configuration['additionalParams'] .= '&tx_news_pi1[controller]=News' .
                '&tx_news_pi1[action]=eventDetail';
        }

        // Add date as human readable
        if ($tsSettings['link']['hrDate'] == 1 || $tsSettings['link']['hrDate']['_typoScriptNodeValue'] == 1) {
            $dateTime = $newsItem->getDatetime();

            if (!empty($tsSettings['link']['hrDate']['day'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[day]=' . $dateTime->format($tsSettings['link']['hrDate']['day']);
            }
            if (!empty($tsSettings['link']['hrDate']['month'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[month]=' . $dateTime->format($tsSettings['link']['hrDate']['month']);
            }
            if (!empty($tsSettings['link']['hrDate']['year'])) {
                $configuration['additionalParams'] .= '&tx_news_pi1[year]=' . $dateTime->format($tsSettings['link']['hrDate']['year']);
            }
        }
        $this->cObj = GeneralUtility::makeInstance(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class);
        $url = $this->cObj->typoLink_URL($configuration);
        return $url;
    }

}