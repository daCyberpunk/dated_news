<?php

namespace FalkRoeder\DatedNews\Controller;

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


/**
 * FeuserController.
 */
class FeuserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * @var \FalkRoeder\DatedNews\Service\FeuserService
     */
    protected $feuserService;

    /**
     * PluginService
     *
     * @var \FalkRoeder\DatedNews\Service\PluginService
     * @inject
     */
    protected $pluginService;

    /**
     * applicationRepository.
     *
     * @var \FalkRoeder\DatedNews\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $applicationRepository = null;

    /**
     * @var \GeorgRinger\News\Domain\Repository\NewsRepository
     */
    protected $newsRepository;



    /**
     * Current logged in user object
     *
     * @var Feuser
     */
    public $feuser;


    /**
     * Initializes the current action
     *
     */
    public function initializeAction()
    {

        $this->feuserService = $this->objectManager->get('FalkRoeder\DatedNews\Service\FeuserService');
        $this->mailService = $this->objectManager->get('FalkRoeder\DatedNews\Service\MailService');
        $this->pluginService = $this->objectManager->get('FalkRoeder\DatedNews\Service\PluginService');
        $this->applicationService = $this->objectManager->get('FalkRoeder\DatedNews\Service\ApplicationService');
        $this->settingsService = $this->objectManager->get('FalkRoeder\DatedNews\Service\SettingsService');
        $this->calendarService = $this->objectManager->get('FalkRoeder\DatedNews\Service\CalendarService');
        $this->newsRepository = $this->objectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');

        $cObj =  $this->configurationManager->getContentObject();
        if ($cObj && isset($cObj->data['uid'])) {
            $pluginConfiguration = $this->pluginService->getPluginConfiguration($cObj->data['uid']);
            $this->settings['switchableControllerActions'] = $pluginConfiguration['switchableControllerActions'];
        }

        $this->feuser = $this->feuserService->getFrontendUserObject();
    }

    /**
     * action show.
     *
     * @param \FalkRoeder\DatedNews\Domain\Model\Feuser $feuser
     *
     * @return void
     */
    public function showAction(\FalkRoeder\DatedNews\Domain\Model\Feuser $feuser = null)
    {
        if($feuser === null) {
            return;
        }
        $applications = $this->feuser->getApplications()->toArray();
        $today = (new \DateTime())->setTimezone(new \DateTimeZone('UTC'));
        $today->setTime(0, 0, 0);

        $pastEvents=[];
        $actualEvents=[];
        $newsRecords = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $recurrencesWithValidApplications=[];
        foreach($applications as $key => $application) {
            $events = $application->getEvents();
            if($events->count() > 0){
                $event = $events->toArray()[0];
                $eventInfo = [
                    'title' => $event->getTitle(),
                    'eventstart' => $event->getEventstart(),
                    'eventend' => $event->getEventend(),
                    'event' => $event
                ];
                if(!$application->isCancelled()) {
                    $newsRecords->attach($event);
                }
            } else {
                //applications on recurrences
                $event = $application->getRecurringevents()->toArray()[0];
                $parentEvent = $event->getParentEvent()->toArray()[0];
                $eventInfo = [
                    'title' => $parentEvent->getTitle(),
                    'eventstart' => $event->getEventstart(),
                    'eventend' => $event->getEventend(),
                    'event' => $parentEvent,
                ];

                if($application->isCancelled() && !in_array($event->getUid(), $recurrencesWithValidApplications)) {
                    $event->setShowincalendar(false);
                } else {
                    //step 1: we want only recurrences with valid applications on our parent events
                    $recurrencesWithValidApplications[$parentEvent->getUid()][] = $event->getUid();
                }
            }

            //step 2: we want only recurrences with valid applications on our parent events
            foreach ($recurrencesWithValidApplications as $newsUid => $recs) {
                $news = $this->newsRepository->findByUid($newsUid());
                $recStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
                foreach ($recs as $rec) {
                    $recStorage->attach($rec);
                }
                $news->setNewsRecurrence($recStorage);
                $newsRecords->attach($news);
            }


            $eventInfo['application'] = $application;

            $eventEnd = $event->getEventend()->setTime(0, 0, 0);
            if($today > $eventEnd) {
                $pastEvents[] = $eventInfo;
            } else {
                $actualEvents[] = $eventInfo;
            }
        }




        $settings = array_merge($this->settingsService->getByPath('tx_news.settings', false), $this->settings);

        $cObj =  $this->configurationManager->getContentObject();
        //todo: Brauchen anderen Kalender, einen der ned über JAAX events holt sondern diese hier nimmt. und nur diese.
        $cal = $this->calendarService->renderCalendar($newsRecords, $settings, null, null, $cObj->data['uid'], true);




        $assignedValues = [
            'pastEvents' => $pastEvents,
            'actualEvents' => $actualEvents,
            'feuser' => $this->feuser,
            'calendar' => $cal->render()
        ];

        $this->view->assignMultiple($assignedValues);
    }



    /**
     * editAction
     *
     * @return void
     */
    public function editAction()
    {
        
    }
}


