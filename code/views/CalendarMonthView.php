<?php

class CalendarMonthView extends CalendarAbstractWeekView
{

    // Attributes

    private $monthClass;

    // Abstract Functions Implemented

    public function init()
    {
        parent::init();
        $this->containerClass = 'monthView';
        $this->innerClass = 'month';
        $this->viewTitle = 'return date(\'F Y\', $date);';
        $this->monthClass = 'return strtolower(date(\'F\', $monthDate));';
    }

    public function needsMonth()
    {
        return true;
    }
    public function needsDay()
    {
        return false;
    }

    public function prevLinkParams(Calendar $calendar)
    {
        $date = mktime(0, 0, 0, $calendar->getMonth() - $this->number, 1, $calendar->getYear());
        return $this->getLinkParams($date);
    }

    public function nextLinkParams(Calendar $calendar)
    {
        $date = mktime(0, 0, 0, $calendar->getMonth() + $this->number, 1, $calendar->getYear());
        return $this->getLinkParams($date);
    }

    public function viewLinkParamsAndTitle(Calendar $calendar)
    {
        $month = $calendar->getMonth();
        if (! $month) {
            $month = 1;
        }
        $year = $calendar->getYear();
        $date = mktime(0, 0, 0, $month, 1, $year);
        $params = $this->getLinkParams($date);
        $title = $this->getCustomisedTitle($month, $year);
        return array($params, $title);
    }

    public function getLinkParams($date)
    {
        return array(
            'year' => date('Y', $date),
            'month' => date('n', $date)
        );
    }

    public function title()
    {
        return $this->number == 1 ? 'month' : "$this->number months";
    }

    public function DateTitle(Calendar $calendar)
    {
        return $this->getCustomisedTitle($calendar->getMonth(), $calendar->getYear());
    }

    public function Weeks(Calendar $calendar)
    {
        $year = $calendar->getYear();
        $month = $calendar->getMonth();

        $nowYear = date('Y');
        $nowMonth = date('n');

        for ($i = 0; $i < $this->number; $i++) {
            $weeksGroup = $this->MonthWeeks($month, $year);

            // 1) Single Values

            $monthDate = mktime(0, 0, 0, $month, 1, $year);
            $values['ExtraInnerClass'] = eval($this->monthClass) . " year$year";
            $values['IsNowYear'] = $year == $nowYear;
            $values['IsPastYear'] = $year < $nowYear;
            $values['IsNow'] = $values['IsNowYear'] && $month == $nowMonth;
            $values['IsPast'] = $values['IsPastYear'] || ($values['IsNowYear'] && $month < $nowMonth);

            $weeksGroups[] = array($weeksGroup, $values);

            if (++$month > 12) {
                $month = 1;
                $year++;
            }
        }

        return $weeksGroups;
    }

    // Private Functions

    protected function MonthWeeks($month, $year)
    {
        $firstDate = mktime(0, 0, 0, $month, 1, $year);
        $firstDateWeek = date('W', $firstDate);
        $firstDateWeekYear = $year;

        if ($month == 1 && $firstDateWeek >= 52) {
            $firstDateWeekYear--;
        }

        $weekFirstDate = $this->getWeekStartDay($firstDateWeek, $firstDateWeekYear, true);
        $nextWeekFirstDate = mktime(0, 0, 0, date('n', $weekFirstDate), date('j', $weekFirstDate) + 7, date('Y', $weekFirstDate));

        if (date('j', $nextWeekFirstDate) == 1) {
            $weekFirstDate = $nextWeekFirstDate;
        }

        while (date('Y', $weekFirstDate) < $year || (date('Y', $weekFirstDate) == $year && date('n', $weekFirstDate) <= $month)) {
            $weekMonday = $weekFirstDate;
            while (date('N', $weekMonday) != 1) {
                $weekMonday = mktime(0, 0, 0, date('n', $weekMonday), date('j', $weekMonday) + 1, date('Y', $weekMonday));
            }
            $week = date('W', $weekMonday);
            $yearOfWeek = date('Y', $weekMonday);
            if ($month == 1) {
                if ($week == 1 && $yearOfWeek == $year - 1) {
                    $yearOfWeek++;
                } elseif ($week >= 52 && $yearOfWeek == $year) {
                    $yearOfWeek--;
                }
            } elseif ($month == 12 && $week == 1) {
                $yearOfWeek++;
            }

            $weeks[] = array('week' => $week, 'yearOfWeek' => $yearOfWeek, 'month' => $month, 'yearOfMonth' => $year);
            $weekFirstDate = mktime(0, 0, 0, date('n', $weekFirstDate), date('j', $weekFirstDate) + 7, date('Y', $weekFirstDate));
        }

        return $weeks;
    }

    // Other Functions

    public function getCustomisedTitle($month, $year)
    {
        $date = mktime(0, 0, 0, $month, 1, $year);
        $result = eval($this->viewTitle);
        if ($this->number > 1) {
            $date = mktime(0, 0, 0, $month + $this->number - 1, 1, $year);
            $result .= $this->viewTitleDelimiter . eval($this->viewTitle);
        }
        return $result;
    }
}
