<?php

abstract class CalendarAbstractWeekView extends CalendarAbstractView
{

    // Attributes

    private $dayStart = 1;
    private $daysRemoved = array();

    protected $showWeekLeft;
    protected $weekLeftTitle;
    protected $weekLeft;
    protected $showWeekRight;
    protected $weekRightTitle;
    protected $weekRight;
    protected $dayTitleClass;
    protected $dayTitle;
    protected $weekClass;
    protected $dayClass;

    private $weekLinkView;
    private $weekLinkCalendar;
    private $weekLinkController;

    private $dayLinkView;
    private $dayLinkCalendar;
    private $dayLinkController;

    // Abstract Functions Implemented

    public function init()
    {
        $this->weekLeft = $this->weekRight = 'return $week[\'week\'];';
        $this->dayTitleClass = 'return strtolower(date(\'l\', $day));';
        $this->dayTitle = 'return date(\'l\', $day);';
        $this->weekClass = 'return \'week\' . $week[\'week\'] . \' year\' . $week[\'yearOfWeek\'];';
        $this->dayClass = 'return strtolower(date(\'l\', $date));';
    }

    public function Calendars(Calendar $calendar)
    {
        $weeksGroups = $this->Weeks($calendar);

        foreach ($weeksGroups as $weeksGroup) {
            list($weeksGroup, $values) = $weeksGroup;
            $calendars[] = $this->Calendar($weeksGroup, $values, $calendar);
        }

        return new ArrayList($calendars);
    }

    // Functions

    public function startByMonday()
    {
        $this->dayStart = 1;
    }
    public function startByTuesday()
    {
        $this->dayStart = 2;
    }
    public function startByWednesday()
    {
        $this->dayStart = 3;
    }
    public function startByThursday()
    {
        $this->dayStart = 4;
    }
    public function startByFriday()
    {
        $this->dayStart = 5;
    }
    public function startBySaturday()
    {
        $this->dayStart = 6;
    }
    public function startBySunday()
    {
        $this->dayStart = 7;
    }

    public function removeMonday()
    {
        $this->removeDay(1);
    }
    public function removeTuesday()
    {
        $this->removeDay(2);
    }
    public function removeWednesday()
    {
        $this->removeDay(3);
    }
    public function removeThursday()
    {
        $this->removeDay(4);
    }
    public function removeFriday()
    {
        $this->removeDay(5);
    }
    public function removeSaturday()
    {
        $this->removeDay(6);
    }
    public function removeSunday()
    {
        $this->removeDay(7);
    }

    public function showWeekLeft()
    {
        $this->showWeekLeft = true;
    }
    public function hideWeekLeft()
    {
        $this->showWeekLeft = false;
    }
    public function setWeekLeftTitle($weekLeftTitle)
    {
        $this->weekLeftTitle = $weekLeftTitle;
    }
    public function setWeekLeft($weekLeft)
    {
        $this->weekLeft = $weekLeft;
    }
    public function showWeekRight()
    {
        $this->showWeekRight = true;
    }
    public function hideWeekRight()
    {
        $this->showWeekRight = false;
    }
    public function setWeekRightTitle($weekRightTitle)
    {
        $this->weekRightTitle = $weekRightTitle;
    }
    public function setWeekRight($weekRight)
    {
        $this->weekRight = $weekRight;
    }
    public function setDayTitleClass($dayTitleClass)
    {
        $this->dayTitleClass = $dayTitleClass;
    }
    public function setDayTitle($dayTitle)
    {
        $this->dayTitle = $dayTitle;
    }
    public function setWeekClass($weekClass)
    {
        $this->weekClass = $weekClass;
    }
    public function setDayClass($dayClass)
    {
        $this->dayClass = $dayClass;
    }

    // Private Functions

    private function removeDay($day)
    {
        if (! in_array($day, $this->daysRemoved)) {
            $this->daysRemoved[] = $day;
        }
    }

    // Abstract Functions

    abstract public function Weeks(Calendar $calendar);

    // Template Functions

    protected function Calendar($weeks, $values, Calendar $currentCalendar)
    {
        // 1) Single Values

        $calendar = $values;
        $calendar['InnerClass'] = $this->innerClass;
        $calendar['ShowWeekLeft'] = $this->showWeekLeft;
        $calendar['WeekLeftTitle'] = $this->weekLeftTitle;
        $calendar['ShowWeekRight'] = $this->showWeekRight;
        $calendar['WeekRightTitle'] = $this->weekRightTitle;
        //Hack
        $week = $weeks[1]['week'];
        $year = $weeks[1]['yearOfWeek'];
        $monthTitleDate = $this->getWeekStartDay($week, $year);
        $calendar['MonthTitle'] = date('F Y', $monthTitleDate);

        // 2) Days Values

        $daysByDateFormat = $this->DaysByDateFormat();

        if (count($daysByDateFormat) == 0) {
            return new ArrayData($calendar);
        }

        foreach ($daysByDateFormat as $day) {
            $dayTitleClass = eval($this->dayTitleClass);
            $dayTitle = eval($this->dayTitle);
            $days[] = new ArrayData(array('DayTitleClass' => $dayTitleClass, 'DayTitle' => $dayTitle));
        }

        $calendar['Days'] = new ArrayList($days);

        // 3) Weeks Values

        $today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));

        foreach ($weeks as $week) {
            $period = array();

            // 1) Single Values

            $period['WeekClass'] = eval($this->weekClass);
            $period['ShowWeekLeft'] = $this->showWeekLeft;
            $period['WeekLeft'] = eval($this->weekLeft);
            $period['ShowWeekRight'] = $this->showWeekRight;
            $period['WeekRight'] = eval($this->weekRight);

            if ($this->weekLinkView) {
                $date = $this->getWeekStartDay($week['week'], $week['yearOfWeek']);
                $linkController = $currentCalendar->getController();
                if ($this->weekLinkController) {
                    $linkController = $this->weekLinkController;
                }
                $linkCalendar = $currentCalendar;
                if ($this->weekLinkCalendar) {
                    $linkCalendar = $this->weekLinkCalendar;
                }
                $params = $this->weekLinkView->getLinkParams($date);
                $period['WeekLink'] = $linkCalendar->Link($linkController, $this->weekLinkView, $params);
            }

            // 2) Days Values

            $days = array();

            $dates = $this->WeekDates($daysByDateFormat, $week['week'], $week['yearOfWeek']);

            foreach ($dates as $date) {
                $day = date('j', $date);
                $month = date('n', $date);
                $year = date('Y', $date);

                $dateParams = array();

                $dateParams['IsToday'] = $date == $today;
                $dateParams['IsPast'] = $date < $today;
                if ($month == $week['month']) {
                    $dateParams['CurrentMonth'] = true;
                } elseif (($year == $week['yearOfMonth'] && $month < $week['month']) || ($year == $week['yearOfMonth'] - 1 && $month == 12)) {
                    $dateParams['PrevMonth'] = true;
                    if ($year == $week['yearOfMonth'] - 1) {
                        $dateParams['PrevYear'] = true;
                    }
                } else {
                    $dateParams['NextMonth'] = true;
                    if ($year == $week['yearOfMonth'] + 1) {
                        $dateParams['NextYear'] = true;
                    }
                }

                $dateParams['DayClass'] = eval($this->dayClass);
                $dateParams['Day'] = $day;

                if ($this->dayLinkView) {
                    $linkController = $currentCalendar->getController();
                    if ($this->dayLinkController) {
                        $linkController = $this->dayLinkController;
                    }
                    $linkCalendar = $currentCalendar;
                    if ($this->dayLinkCalendar) {
                        $linkCalendar = $this->dayLinkCalendar;
                    }
                    $params = $this->dayLinkView->getLinkParams($date);
                    $dateParams['Link'] = $linkCalendar->Link($linkController, $this->dayLinkView, $params);
                }



                $this->extend('updateDateParams', $date, $dateParams, $currentCalendar);

                $days[] = new ArrayData($dateParams);
            }

            $period['Days'] = new ArrayList($days);

            $periods[] = new ArrayData($period);
        }

        $calendar['Weeks'] = new ArrayList($periods);

        return new ArrayData($calendar);
    }

    public function WeekDates($days, $week, $year)
    {
        $firstDate = $this->getWeekStartDay($week, $year);

        $beforeMonday = true;

        foreach ($days as $day) {
            $date = $firstDate;

            if (date('N', $day) == 1) {
                $beforeMonday = false;
            }

            while (date('N', $date) != date('N', $day)) {
                $date = mktime(0, 0, 0, date('n', $date), date('j', $date) + ($beforeMonday ? -1 : 1), date('Y', $date));
            }

            $dates[] = $date;
        }

        return $dates;
    }

    private function DaysByDateFormat()
    {
        $day = $this->dayStart;

        $days = array();
        for ($i = 1; $i <= 7; $i++) {
            if (! in_array($day, $this->daysRemoved)) {
                $days[] = mktime(0, 0, 0, 1, $day, 1);
            }
            $day = $day < 7 ? $day + 1 : 1;
        }

        return $days;
    }

    protected function getWeekStartDay($week, $year, $fromStartDay = false)
    {

        // 1) Research of the week

        $firstDate = mktime(0, 0, 0, 1, 1, $year);
        while (date('W', $firstDate) != 1) {
            $firstDate = mktime(0, 0, 0, date('n', $firstDate), date('j', $firstDate) + 1, date('Y', $firstDate));
        }
        while (date('W', $firstDate) < $week) {
            $firstDate = mktime(0, 0, 0, date('n', $firstDate), date('j', $firstDate) + 7, date('Y', $firstDate));
        }

        // 2) Research of the first day of the week

        $diff = date('N', $firstDate) - 1;
        if ($fromStartDay && $this->dayStart != 1) {
            $diff += 8 - $this->dayStart;
        }
        $firstDate = mktime(0, 0, 0, date('n', $firstDate), date('j', $firstDate) - $diff, date('Y', $firstDate));

        return $firstDate;
    }

    // Link Functions

    public function linkWeekTo(CalendarWeekView $view, Calendar $calendar = null, $controller = null)
    {
        $this->weekLinkView = $view;
        $this->weekLinkCalendar = $calendar;
        $this->weekLinkController = $controller;
    }

    public function linkDayTo(CalendarDayView $view, Calendar $calendar = null, $controller = null)
    {
        $this->dayLinkView = $view;
        $this->dayLinkCalendar = $calendar;
        $this->dayLinkController = $controller;
    }
}
