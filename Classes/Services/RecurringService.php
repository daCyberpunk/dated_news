<?php

namespace FalkRoeder\DatedNews\Services;

use \Recurr\Rule;
use \Recurr\Transformer\ArrayTransformer;

/**
 * RecurringService.php
 * 
 */
class RecurringService
{
    
    public function getRule(){
        return 'AAA';
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
}
