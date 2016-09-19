<?php

class CalendarYearView extends CalendarMonthView
{
    
    // Attributes

    private $monthStart = 1;
    private $monthsRemoved = array();
    
    protected $monthInnerClass;
    protected $monthTitle;
    
    private $monthLinkView;
    private $monthLinkCalendar;
    private $monthLinkController;
    
    // Abstract Functions Implemented

    public function init()
    {
        parent::init();
        $this->containerClass = 'yearView';
        $this->monthInnerClass = $this->innerClass;
        $this->viewTitle = 'return date(\'Y\', $date);';
        $this->innerClass = 'year';
        $this->monthTitle = 'return date(\'F Y\', $monthDate);';
    }
    
    public function needsMonth()
    {
        return false;
    }
    
    public function Calendars(Calendar $calendar)
    {
        $years = $this->Years($calendar);
        
        foreach ($years as $year) {
            $calendars[] = $this->YearCalendar($year, $calendar);
        }
        
        return new ArrayList($calendars);
    }
    
    public function prevLinkParams(Calendar $calendar)
    {
        $date = mktime(0, 0, 0, 1, 1, $calendar->getYear() - $this->number);
        return $this->getLinkParams($date);
    }
    
    public function nextLinkParams(Calendar $calendar)
    {
        $date = mktime(0, 0, 0, 1, 1, $calendar->getYear() + $this->number);
        return $this->getLinkParams($date);
    }
    
    public function viewLinkParamsAndTitle(Calendar $calendar)
    {
        $year = $calendar->getYear();
        $date = mktime(0, 0, 0, 1, 1, $year);
        $params = $this->getLinkParams($date);
        $title = $this->getCustomisedTitle($year);
        return array($params, $title);
    }
    
    public function getLinkParams($date)
    {
        return array(
            'year' => date('Y', $date)
        );
    }
    
    public function title()
    {
        return $this->number == 1 ? 'year' : "$this->number years";
    }
    
    public function DateTitle(Calendar $calendar)
    {
        return $this->getCustomisedTitle($calendar->getYear());
    }
    
    public function Years(Calendar $calendar)
    {
        $year = $calendar->getYear();
        
        for ($i = 0; $i < $this->number; $i++) {
            $years[] = $year + $i;
        }
        
        return $years;
    }
    
    // Functions

    public function startByJanuary()
    {
        $this->monthStart = 1;
    }
    public function startByFebruary()
    {
        $this->monthStart = 2;
    }
    public function startByMarch()
    {
        $this->monthStart = 3;
    }
    public function startByApril()
    {
        $this->monthStart = 4;
    }
    public function startByMay()
    {
        $this->monthStart = 5;
    }
    public function startByJune()
    {
        $this->monthStart = 6;
    }
    public function startByJuly()
    {
        $this->monthStart = 7;
    }
    public function startByAugust()
    {
        $this->monthStart = 8;
    }
    public function startBySeptember()
    {
        $this->monthStart = 9;
    }
    public function startByOctober()
    {
        $this->monthStart = 10;
    }
    public function startByNovember()
    {
        $this->monthStart = 11;
    }
    public function startByDecember()
    {
        $this->monthStart = 12;
    }
    
    public function removeJanuary()
    {
        $this->removeMonth(1);
    }
    public function removeFebruary()
    {
        $this->removeMonth(2);
    }
    public function removeMarch()
    {
        $this->removeMonth(3);
    }
    public function removeApril()
    {
        $this->removeMonth(4);
    }
    public function removeMay()
    {
        $this->removeMonth(5);
    }
    public function removeJune()
    {
        $this->removeMonth(6);
    }
    public function removeJuly()
    {
        $this->removeMonth(7);
    }
    public function removeAugust()
    {
        $this->removeMonth(8);
    }
    public function removeSeptember()
    {
        $this->removeMonth(9);
    }
    public function removeOctober()
    {
        $this->removeMonth(10);
    }
    public function removeNovember()
    {
        $this->removeMonth(11);
    }
    public function removeDecember()
    {
        $this->removeMonth(12);
    }
    
    // Private Functions

    private function removeMonth($month)
    {
        if (! in_array($month, $this->monthsRemoved)) {
            $this->monthsRemoved[] = $month;
        }
    }
    
    private function YearCalendar($year, Calendar $currentCalendar)
    {
        
        // 1) Single Values

        $nowYear = date('Y');
        $nowMonth = date('n');
        
        $calendar['InnerClass'] = $this->innerClass;
        $calendar['ExtraInnerClass'] = "$this->innerClass$year";
        $calendar['IsNow'] = $year == $nowYear;
        $calendar['IsPast'] = $year < $nowYear;
        
        // 2) Months Values

        $months = $this->Months();
        
        if (count($months) == 0) {
            return new ArrayData($calendar);
        }
        
        foreach ($months as $month) {
            $weeksGroups = $this->MonthWeeks($month, $year);
            
            // 1) Single Values

            $monthDate = mktime(0, 0, 0, $month, 1, $year);
            $values['IsNow'] = $calendar['IsNow'] && $month == $nowMonth;
            $values['IsPast'] = $calendar['IsPast'] || ($calendar['IsNow'] && $month < $nowMonth);
            $values['MonthClass'] = eval($this->monthClass);
            $values['MonthTitle'] = eval($this->monthTitle);
            
            $period = $this->Calendar($weeksGroups, $values, $currentCalendar);
            $period->setField('InnerClass', $this->monthInnerClass);
            
            if ($this->monthLinkView) {
                $linkController = $currentCalendar->getController();
                if ($this->monthLinkController) {
                    $linkController = $this->monthLinkController;
                }
                $linkCalendar = $currentCalendar;
                if ($this->monthLinkCalendar) {
                    $linkCalendar = $this->monthLinkCalendar;
                }
                $params = $this->monthLinkView->getLinkParams($monthDate);
                $period->setField('Link', $linkCalendar->Link($linkController, $this->monthLinkView, $params));
            }
            $periods[] = $period;
        }
        
        $calendar['Months'] = new ArrayList($periods);
        
        return new ArrayData($calendar);
    }
    
    private function Months()
    {
        $month = $this->monthStart;
        
        $months = array();
        while ($month <= 12) {
            if (! in_array($month, $this->monthsRemoved)) {
                $months[] = $month;
            }
            $month++;
        }
        
        return $months;
    }
    
    // Link Functions

    public function linkMonthTo(CalendarMonthView $view, Calendar $calendar = null, $controller = null)
    {
        $this->monthLinkView = $view;
        $this->monthLinkCalendar = $calendar;
        $this->monthLinkController = $controller;
    }
    
    // Other Functions

    public function getCustomisedTitle($year)
    {
        $date = mktime(0, 0, 0, 1, 1, $year);
        $result = eval($this->viewTitle);
        if ($this->number > 1) {
            $date = mktime(0, 0, 0, 1, 1, $year + $this->number - 1);
            $result .= $this->viewTitleDelimiter . eval($this->viewTitle);
        }
        return $result;
    }
}
