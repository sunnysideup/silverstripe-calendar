<?php

abstract class CalendarAbstractTimeView extends CalendarAbstractView {
	
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
	
	function init() {
		$this->timePeriod = new CalendarTimePeriod(new CalendarTime(), new CalendarTime(23, 59, 59));
		$this->subPeriodsLength = new CalendarTime(1);
		
		$this->dayTitleClass = 'return strtolower(date(\'l\', $date));';
		$this->dayTitle = 'return date(\'l jS F Y\', $date);';
		$this->timeClass = 'return \'hour\' . date(\'H\', $subPeriodStartDateTime) . \' minute\' . date(\'i\', $subPeriodStartDateTime) . \' second\' . date(\'s\', $subPeriodStartDateTime);';
		$this->time = 'return date(\'H:i\', $subPeriodStartDateTime);';
		$this->dayClass = 'return strtolower(date(\'l\', $subPeriodStart));';
	}
	
	function needsMonth() {return true;}
	function needsDay() {return true;}
	
	function Calendars(Calendar $calendar) {
		$datesGroups = $this->Dates($calendar);
		
		foreach($datesGroups as $datesGroup) {
			$calendars[] = $this->Calendar($datesGroup, $calendar);
		}
		
		return new DataObjectSet($calendars);
	}
	
	function viewLinkParamsAndTitle(Calendar $calendar) {
		$day = $calendar->getDay();
		if(! $day) $day = 1;
		$month = $calendar->getMonth();
		if(! $month) $month = 1;
		$year = $calendar->getYear();
		$date = mktime(0, 0, 0, $month, $day, $year);
		$params = $this->getLinkParams($date);
		$title = $this->getCustomisedTitle($day, $month, $year);
		return array($params, $title);
	}
	
	function getLinkParams($date) {
		return array(
			'year' => date('Y', $date),
			'month' => date('n', $date),
			'day' => date('j', $date)
		);
	}
	
	function DateTitle(Calendar $calendar) {
		return $this->getCustomisedTitle($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
	}
	
	// Functions
	
	function setTimePeriod(CalendarTimePeriod $timePeriod) {$this->timePeriod = $timePeriod;}
	function setSubPeriodsLength(CalendarTime $subPeriodsLength) {$this->subPeriodsLength = $subPeriodsLength;}
	
	function removeSubPeriods($subPeriods) {
		if(! is_array($subPeriods)) {
			$subPeriods = array($subPeriods);
		}
		foreach($subPeriods as $subPeriod) {
			if(is_a($subPeriod, 'CalendarTimePeriod')) {
				$this->subPeriodsRemoved[] = $subPeriod;
			}
			else {
				user_error('CalendarAbstractTimeView::removeSubPeriods() : you cannot remove a period which class is not \'CalendarTimePeriod\'', E_USER_ERROR);
			}
		}
	}
	
	function getSubPeriods() {
		$startSubPeriodTime = $this->timePeriod->getStartTime();
		$endTime = $this->timePeriod->getEndTime();
		
		while($startSubPeriodTime < $endTime) {
			$endSubPeriodTime = $this->getEndSubPeriodTime($startSubPeriodTime);
			$subPeriods[] = new CalendarTimePeriod($startSubPeriodTime, $endSubPeriodTime);
			$startSubPeriodTime = $endSubPeriodTime;
		}
		
		return $subPeriods;
	}
	
	function setTimeTitle($timeTitle) {$this->timeTitle = $timeTitle;}
	function setDayTitleClass($dayTitleClass) {$this->dayTitleClass = $dayTitleClass;}
	function setDayTitle($dayTitle) {$this->dayTitle = $dayTitle;}
	function setTimeClass($timeClass) {$this->timeClass = $timeClass;}
	function setTime($time) {$this->time = $time;}
	function setDayClass($dayClass) {$this->dayClass = $dayClass;}
	
	// Private Functions
	
	private function getEndSubPeriodTime(CalendarTime $startSubPeriodTime) {
		$endSubPeriodTime = $startSubPeriodTime->add($this->subPeriodsLength);
		
		if($endSubPeriodTime < $startSubPeriodTime) {
			$endSubPeriodTime = clone $this->timePeriod->getEndTime();
		}
		
		return $endSubPeriodTime;
	}
	
	// Abstract Functions
	
	abstract function Dates(Calendar $calendar);
	
	abstract function getCustomisedTitle($day, $month, $year);
	
	// Template Functions
		
	private function Calendar($dates, Calendar $currentCalendar) {
		
		// 1) Single Values
		
		$calendar['InnerClass'] = $this->innerClass;
		$calendar['TimeTitle'] = $this->timeTitle;
		
		// 2) Days Values
		
		foreach($dates as $date) {
			$dayTitleClass = eval($this->dayTitleClass);
			$dayTitle = eval($this->dayTitle);
			$days[] = new ArrayData(array('DayTitleClass' => $dayTitleClass, 'DayTitle' => $dayTitle));
		}
		
		$calendar['Days'] = new DataObjectSet($days);
		
		// 3) Periods Values
		
		$subPeriods = $this->getSubPeriods();
		
		$todayNow = mktime();
		
		foreach($subPeriods as $subPeriod) {
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
			
			foreach($dates as $date) {
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
			
			$period['Days'] = new DataObjectSet($days);
			
			$periods[] = new ArrayData($period);
		}
				
		$calendar['Periods'] = new DataObjectSet($periods);
		
		return new ArrayData($calendar);
	}
}
 
?>
