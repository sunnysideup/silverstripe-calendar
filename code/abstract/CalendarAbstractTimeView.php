<?php

abstract class CalendarAbstractTimeView extends CalendarAbstractView
{
    
    // Attributes

    private $timePeriod;
    private $subPeriodsLength;
    private $subPeriodsRemoved = array();
    
    protected $timeTitle;
    protected $dayTitleClass;
    protected $dayTitle;
    protected $timeClass;
    protected $time;
    protected $dayClass;
        
    // Abstract Functions Implemented

    public function init()
    {
        $this->timePeriod = new CalendarTimePeriod(new CalendarTime(), new CalendarTime(23, 59, 59));
        $this->subPeriodsLength = new CalendarTime(1);
        
        $this->dayTitleClass = 'return strtolower(date(\'l\', $date));';
        $this->dayTitle = 'return date(\'l jS F Y\', $date);';
        $this->timeClass = 'return \'hour\' . date(\'H\', $subPeriodStartDateTime) . \' minute\' . date(\'i\', $subPeriodStartDateTime) . \' second\' . date(\'s\', $subPeriodStartDateTime);';
        $this->time = 'return date(\'H:i\', $subPeriodStartDateTime);';
        $this->dayClass = 'return strtolower(date(\'l\', $subPeriodStart));';
    }
    
    public function needsMonth()
    {
        return true;
    }
    public function needsDay()
    {
        return true;
    }
    
    public function Calendars(Calendar $calendar)
    {
        $datesGroups = $this->Dates($calendar);
        
        foreach ($datesGroups as $datesGroup) {
            $calendars[] = $this->Calendar($datesGroup, $calendar);
        }
        
        return new ArrayList($calendars);
    }
    
    public function viewLinkParamsAndTitle(Calendar $calendar)
    {
        $day = $calendar->getDay();
        if (! $day) {
            $day = 1;
        }
        $month = $calendar->getMonth();
        if (! $month) {
            $month = 1;
        }
        $year = $calendar->getYear();
        $date = mktime(0, 0, 0, $month, $day, $year);
        $params = $this->getLinkParams($date);
        $title = $this->getCustomisedTitle($day, $month, $year);
        return array($params, $title);
    }
    
    public function getLinkParams($date)
    {
        return array(
            'year' => date('Y', $date),
            'month' => date('n', $date),
            'day' => date('j', $date)
        );
    }
    
    public function DateTitle(Calendar $calendar)
    {
        return $this->getCustomisedTitle($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
    }
    
    // Functions

    public function setTimePeriod(CalendarTimePeriod $timePeriod)
    {
        $this->timePeriod = $timePeriod;
    }
    public function setSubPeriodsLength(CalendarTime $subPeriodsLength)
    {
        $this->subPeriodsLength = $subPeriodsLength;
    }
    
    public function removeSubPeriods($subPeriods)
    {
        if (! is_array($subPeriods)) {
            $subPeriods = array($subPeriods);
        }
        foreach ($subPeriods as $subPeriod) {
            if (is_a($subPeriod, 'CalendarTimePeriod')) {
                $this->subPeriodsRemoved[] = $subPeriod;
            } else {
                user_error('CalendarAbstractTimeView::removeSubPeriods() : you cannot remove a period which class is not \'CalendarTimePeriod\'', E_USER_ERROR);
            }
        }
    }
    
    public function getSubPeriods()
    {
        $startSubPeriodTime = $this->timePeriod->getStartTime();
        $endTime = $this->timePeriod->getEndTime();
        
        while ($startSubPeriodTime < $endTime) {
            $endSubPeriodTime = $this->getEndSubPeriodTime($startSubPeriodTime);
            $subPeriods[] = new CalendarTimePeriod($startSubPeriodTime, $endSubPeriodTime);
            $startSubPeriodTime = $endSubPeriodTime;
        }
        
        return $subPeriods;
    }
    
    public function setTimeTitle($timeTitle)
    {
        $this->timeTitle = $timeTitle;
    }
    public function setDayTitleClass($dayTitleClass)
    {
        $this->dayTitleClass = $dayTitleClass;
    }
    public function setDayTitle($dayTitle)
    {
        $this->dayTitle = $dayTitle;
    }
    public function setTimeClass($timeClass)
    {
        $this->timeClass = $timeClass;
    }
    public function setTime($time)
    {
        $this->time = $time;
    }
    public function setDayClass($dayClass)
    {
        $this->dayClass = $dayClass;
    }
    
    // Private Functions

    private function getEndSubPeriodTime(CalendarTime $startSubPeriodTime)
    {
        $endSubPeriodTime = $startSubPeriodTime->add($this->subPeriodsLength);
        
        if ($endSubPeriodTime < $startSubPeriodTime) {
            $endSubPeriodTime = clone $this->timePeriod->getEndTime();
        }
        
        return $endSubPeriodTime;
    }
    
    // Abstract Functions

    abstract public function Dates(Calendar $calendar);
    
    abstract public function getCustomisedTitle($day, $month, $year);
    
    // Template Functions

    private function Calendar($dates, Calendar $currentCalendar)
    {
        
        // 1) Single Values

        $calendar['InnerClass'] = $this->innerClass;
        $calendar['TimeTitle'] = $this->timeTitle;
        
        // 2) Days Values

        foreach ($dates as $date) {
            $dayTitleClass = eval($this->dayTitleClass);
            $dayTitle = eval($this->dayTitle);
            $days[] = new ArrayData(array('DayTitleClass' => $dayTitleClass, 'DayTitle' => $dayTitle));
        }
        
        $calendar['Days'] = new ArrayList($days);
        
        // 3) Periods Values

        $subPeriods = $this->getSubPeriods();
        
        $todayNow = mktime();
        
        foreach ($subPeriods as $subPeriod) {
            $period = array();
            
            $subPeriodStartTime = $subPeriod->getStartTime();
            $subPeriodEndTime = $subPeriod->getEndTime();
            
            $subPeriodStartTimeHours = $subPeriodStartTime->getHours();
            $subPeriodEndTimeHours = $subPeriodEndTime->getHours();
            
            $subPeriodStartTimeMinutes = $subPeriodStartTime->getMinutes();
            $subPeriodEndTimeMinutes = $subPeriodEndTime->getMinutes();
            
            $subPeriodStartTimeSeconds = $subPeriodStartTime->getSeconds();
            $subPeriodEndTimeSeconds = $subPeriodEndTime->getSeconds();
            
            $subPeriodStartDateTime = mktime($subPeriodStartTimeHours, $subPeriodStartTimeMinutes, $subPeriodStartTimeSeconds, 0, 0, 0);
            $subPeriodEndDateTime = mktime($subPeriodEndTimeHours, $subPeriodEndTimeMinutes, $subPeriodEndTimeSeconds, 0, 0, 0);
            
            // 1) Single Values

            $period['TimeClass'] = eval($this->timeClass);
            $period['Time'] = eval($this->time);
            
            // 2) Days Values

            $days = array();
            
            foreach ($dates as $date) {
                $day = date('j', $date);
                $month = date('n', $date);
                $year = date('Y', $date);
                
                $subPeriodStart = mktime($subPeriodStartTimeHours, $subPeriodStartTimeMinutes, $subPeriodStartTimeSeconds, $month, $day, $year);
                $subPeriodEnd = mktime($subPeriodEndTimeHours, $subPeriodEndTimeMinutes, $subPeriodEndTimeSeconds, $month, $day, $year);
            
                $subPeriodParams = array();
                    
                $subPeriodParams['IsTodayNow'] = $subPeriodStart <= $todayNow && $todayNow <= $subPeriodEnd;
                $subPeriodParams['IsToday'] = $day == date('j') && $month == date('n') && $year == date('Y');
                $subPeriodParams['IsPast'] = $subPeriodStart < $todayNow;
                $subPeriodParams['DayClass'] = eval($this->dayClass);
                
                $this->extend('updateSubPeriodParams', $subPeriodStart, $subPeriodEnd, $subPeriodParams, $currentCalendar);
                
                $days[] = new ArrayData($subPeriodParams);
            }
            
            $period['Days'] = new ArrayList($days);
            
            $periods[] = new ArrayData($period);
        }
                
        $calendar['Periods'] = new ArrayList($periods);
        
        return new ArrayData($calendar);
    }
}
