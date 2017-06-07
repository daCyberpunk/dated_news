<?php

namespace FalkRoeder\DatedNews\Services;

use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;

/**
 * RecurringService.php.
 */
class RecurrenceService
{
    protected $availableWeekdays = ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU'];

    /**
     * getRecurrences
     * 
     * @param $startDate
     * @param $endDate
     * @param $settings
     * @return \Recurr\Recurrence[]|\Recurr\RecurrenceCollection
     */
    public function getRecurrences($startDate, $endDate, $settings)
    {
        $rule = $this->buildRule($startDate, $endDate, $settings);

        $transformer = new ArrayTransformer();
        $recurrences = $transformer->transform($rule);

        return $recurrences;
    }

    /**
     * buildRule
     * 
     * @param $startDate
     * @param $endDate
     * @param $settings
     * @return mixed|Rule
     * @throws \Recurr\Exception\InvalidArgument
     * @throws \Recurr\Exception\InvalidRRule
     */
    public function buildRule($startDate, $endDate, $settings)
    {
        $rule = new Rule();
        $rule
            ->setStartDate($startDate)
            ->setEndDate($endDate);

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
                $rule->setByDay(['MO', 'TU', 'WE', 'TH', 'FR']);
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

        if ($settings['recurrence_type'] === '1') {
            $until = new \DateTime($settings['recurrence_until']);
            $rule->setUntil($until->add(new \DateInterval('P1D')));
        } else {
            $rule->setCount($settings['recurrence_count']);
        }

        return $rule;
    }

    /**
     * disolveBitValues
     * 
     * @param $bit
     * @param $values
     * @return array
     */
    public function disolveBitValues($bit, $values = null)
    {
        $result = [];
        for ($n = 6; $n >= 0; $n--) {
            if ($bit & (1 << $n)) {
                if (is_array($values) && !empty($values)) {
                    array_push($result, $values[$n]);
                } else {
                    array_push($result, $n);
                }
            }
        }

        return array_reverse($result);
    }

    /**
     * getUserdefinedRule
     * 
     * @param $rule
     * @param $settings
     * @return mixed
     */
    public function getUserdefinedRule($rule, $settings)
    {
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
                if ($settings['ud_monthly_base'] === '1') {
                    //per day
                    $rule
                        ->setByDay([$this->availableWeekdays[$settings['ud_monthly_perday_weekdays']]])
                        ->setBySetPosition($this->disolveBitValues($settings['ud_monthly_perday'], [1, 2, 3, 4, 5, -1]));
                } else {
                    //per date
                    $monthDays = $this->disolveBitValues($settings['ud_monthly_perdate_day']);
                    foreach ($monthDays as $key => $day) {
                        $monthDays[$key] = $day + 1;
                    }
                    if ($settings['ud_monthly_perdate_lastday']) {
                        array_push($monthDays, -1);
                    }
                    $rule->setByMonthDay($monthDays);
                }
                break;
            case 4:
                // yearly
                $position = $this->disolveBitValues($settings['ud_yearly_perday'], [1, 2, 3, 4, 5, -1, 8]);
                $rule
                    ->setFreq('YEARLY')
                    ->setInterval($settings['ud_yearly_everycount'])
                    ->setByMonth([$settings['ud_yearly_perday_month']])
                    ->setByDay([$this->availableWeekdays[$settings['ud_yearly_perday_weekdays']]]);
                if (!in_array(8, $position)) {
                    $rule->setBySetPosition($position);
                }
                break;
            default:
        }

        return $rule;
    }
}
