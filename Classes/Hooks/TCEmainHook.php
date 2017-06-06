<?php
namespace FalkRoeder\DatedNews\Hooks;

use \FalkRoeder\DatedNews\Services\RecurringService;
class TCEmainHook {

    protected $availableWeekdays = ['MO','TU','WE','TH','FR','SA','SU'];


    public function processCmdmap_preProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($command,'TCEmainHook:6');

    }
    public function processCmdmap_postProcess($command, $table, $id, $value, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }

    public function processDatamap_preProcessFieldArray(array &$fieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
        // render hook only for news table
        if($table !== 'tx_news_domain_model_news' || $fieldArray['recurrence_updated_behavior'] === 1){
            return;
        }

        //get all recurrence settings
        $settings = [];
        foreach ($fieldArray as $key => $val){
            if($this->startsWith($key, 'recurrence') || $this->startsWith($key, 'ud_')){
                $settings[$key] = $val;
            }
        }
        unset($fieldArray['recurrence_updated_behavior']);

        /** @var $extbaseObjectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
        $extbaseObjectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        /** @var $newsRepository \GeorgRinger\News\Domain\Repository\NewsRepository */
        $newsRepository = $extbaseObjectManager->get('GeorgRinger\News\Domain\Repository\NewsRepository');
        /** @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository */
        $newsRecurrenceRepository = $extbaseObjectManager->get('FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository');

        $news = $newsRepository->findByIdentifier($id);

        /** @var $newsRecurrenceRepository \FalkRoeder\DatedNews\Domain\Repository\NewsRecurrenceRepository */
        $recurrenceService = $extbaseObjectManager->get('FalkRoeder\DatedNews\Service\RecurrenceService');
        
        $recurrences = $recurrenceService->getRule();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($recurrences,'TCEmainHook:46');
        

        if($settings['recurrence'] === 0 || $settings['recurrence'] === null ) {
            //delete all recurrences if exist
        } else {
            $rule = new Rule;
            $rule
                ->setStartDate($news->getEventstart())
                ->setEndDate($news->getEventend());
            switch ($settings['recurrence']) {
                case 1:
                    // daily
                    $rule->setFreq('DAILY');
                    break;
                case 2:
                    // weekly
                    $rule->setFreq('WEEKLY');
                    break;
                case 3:
                    // workdays
                    $rule->setByDay(['MO','TU','WE','TH','FR']);
                    break;
                case 4:
                    // every other week
                    $rule->setFreq('WEEKLY')->setInterval(2);
                    break;
                case 5:
                    // monthly
                    $rule->setFreq('MONTHLY');
                    break;
                case 6:
                    // yearly
                    $rule->setFreq('YEARLY');
                    break;
                case 7:
                    // user defined
                    $rule = $this->getUserdefinedRule($rule, $settings);
                    break;
                default:
            }
            if($settings['recurrence_type'] === '1') {
                $until = new \DateTime($settings['recurrence_until']);
                $rule->setUntil($until->add(new \DateInterval('P1D')));
            } else {
                $rule->setCount($settings['recurrence_count']);
            }
            $transformer = new ArrayTransformer();
            $recurrences = $transformer->transform($rule);

            foreach ($recurrences as $rec) {
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($rec,'TCEmainHook:88');
            }
        }
    }

    public function disolveBitValues($bit,$values = null){
        $result = [];
        for ($n = 6; $n >= 0; $n--){
            if( $bit & (1 << $n) ) {
                if (is_array($values) && !empty($values)){
                    array_push($result,$values[$n]);
                } else {
                    array_push($result,$n);
                }
            }
        }
        return array_reverse($result);
    }

    public function getUserdefinedRule($rule, $settings){
        switch ($settings['ud_type']) {
            case 1:
                // daily
                $rule
                    ->setFreq('DAILY')
                    ->setInterval($settings['ud_daily_everycount']);
                break;
            case 2:
                // weekly
                $rule
                    ->setFreq('WEEKLY')
                    ->setInterval($settings['ud_weekly_everycount'])
                    ->setByDay($this->disolveBitValues($settings['ud_weekly_weekdays'], $this->availableWeekdays));
                break;
            case 3:
                // monthly
                $rule
                    ->setFreq('MONTHLY')
                    ->setInterval($settings['ud_monthly_everycount']);
                if($settings['ud_monthly_base'] === "1"){
                    //per day
                    $rule
                        ->setByDay([$this->availableWeekdays[$settings['ud_monthly_perday_weekdays']]])
                        ->setBySetPosition($this->disolveBitValues($settings['ud_monthly_perday'], [1,2,3,4,5,-1]));
                } else {
                    //per date
                    $monthDays = $this->disolveBitValues($settings['ud_monthly_perdate_day']);
                    foreach ($monthDays as $key => $day){
                        $monthDays[$key] = $day + 1;
                    }
                    if($settings['ud_monthly_perdate_lastday']){
                        array_push($monthDays, -1);
                    }
                    $rule->setByMonthDay($monthDays);
                }
                break;
            case 4:
                // yearly
                $position = $this->disolveBitValues($settings['ud_yearly_perday'], [1,2,3,4,5,-1,8]);
                $rule
                    ->setFreq('YEARLY')
                    ->setInterval($settings['ud_yearly_everycount'])
                    ->setByMonth([$settings['ud_yearly_perday_month']])
                    ->setByDay([$this->availableWeekdays[$settings['ud_yearly_perday_weekdays']]]);
                if(!in_array(8,$position)){
                    $rule->setBySetPosition($position);
                }
                break;
            default:
        }

        return $rule;
    }

    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public function processCmdmap_deleteAction($table, $id, $recordToDelete, $recordWasDeleted=NULL, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
    public function processDatamap_afterAllOperations(\TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {
//        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pObj,'TCEmainHook:22');
        
    }
    public function processDatamap_postProcessFieldArray($status, $table, $id, array &$fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
    public function processDatamap_afterDatabaseOperations($status, $table, $id, array $fieldArray, \TYPO3\CMS\Core\DataHandling\DataHandler &$pObj) {

    }
}