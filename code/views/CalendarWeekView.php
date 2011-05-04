<?php

class CalendarWeekView extends CalendarAbstractTimeView {
	
	// Attributes
	
	private $dayStart = 1;
	private $daysRemoved = array();
	
	// Abstract Functions Implemented
	
	function init() {
		parent::init();
		$this->containerClass = 'weekView';
		$this->innerClass = 'week';
		$this->viewTitle = 'return \'Week Of \' . date(\'l jS F Y\', $date);';
	}
	
	function prevLinkParams(Calendar $calendar) {
		$date = $this->getWeekStartDay($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
		$dayValue = date('j', $date) - ($this->number * 7);
		$monthValue = date('n', $date);
		$yearValue = date('Y', $date);
		$date = mktime(0, 0, 0, $monthValue, $dayValue, $yearValue);
		return $this->getLinkParams($date);
	}
	
	function nextLinkParams(Calendar $calendar) {
		$date = $this->getWeekStartDay($calendar->getDay(), $calendar->getMonth(), $calendar->getYear());
		$dayValue = date('j', $date) + ($this->number * 7);
		$monthValue = date('n', $date);
		$yearValue = date('Y', $date);
		$date = mktime(0, 0, 0, $monthValue, $dayValue, $yearValue);
		return $this->getLinkParams($date);
	}
	
	function title() {return $this->number == 1 ? 'week' : "$this->number weeks";}
	
	function Dates(Calendar $calendar) {
		$year = $calendar->getYear();
		$month = $calendar->getMonth();
		$day = $calendar->getDay();
		
		if(count($this->daysRemoved) == 7) {
			return $datesGroups;
		}
		
		$lastDate = $this->getWeekStartDay($day, $month, $year);
		
		while(date('N', $lastDate) != $this->dayStart) {
			$lastDate = mktime(0, 0, 0, date('n', $lastDate), date('j', $lastDate) - 1, date('Y', $lastDate));
		}
		while(in_array(date('N', $lastDate), $this->daysRemoved)) {
			$lastDate = mktime(0, 0, 0, date('n', $lastDate), date('j', $lastDate) + 1, date('Y', $lastDate));
		}
		
		for($i = 0; $i < $this->number; $i++) {
			$datesGroup = array();
			for($j = 0; $j < 7; $j++) {
				if(! in_array(date('N', $lastDate), $this->daysRemoved)) {
					$datesGroup[] = $lastDate;
				}
				$lastDate = mktime(0, 0, 0, date('n', $lastDate), date('j', $lastDate) + 1, date('Y', $lastDate));
			}
			$datesGroups[] = $datesGroup;
		}
		
		return $datesGroups;
	}
	
	function getCustomisedTitle($day, $month, $year) {
		$date = $this->getWeekStartDay($day, $month, $year);
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$dayValue = date('j', $date) + (($this->number - 1) * 7);
			$monthValue = date('n', $date);
			$yearValue = date('Y', $date);
			$date = mktime(0, 0, 0, $monthValue, $dayValue, $yearValue);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}
	
	// Functions
	
	function startByMonday() {$this->dayStart = 1;}
	function startByTuesday() {$this->dayStart = 2;}
	function startByWednesday() {$this->dayStart = 3;}
	function startByThursday() {$this->dayStart = 4;}
	function startByFriday() {$this->dayStart = 5;}
	function startBySaturday() {$this->dayStart = 6;}
	function startBySunday() {$this->dayStart = 7;}
	
	function removeMonday() {$this->removeDay(1);}
	function removeTuesday() {$this->removeDay(2);}
	function removeWednesday() {$this->removeDay(3);}
	function removeThursday() {$this->removeDay(4);}
	function removeFriday() {$this->removeDay(5);}
	function removeSaturday() {$this->removeDay(6);}
	function removeSunday() {$this->removeDay(7);}
	
	// Private Functions
	
	private function getWeekStartDay($day, $month, $year) {
		$date = mktime(0, 0, 0, $month, $day, $year);
		
		while(date('N', $date) > 1) { // It means that the 1st day of this week is not Monday
			$date = mktime(0, 0, 0, date('n', $date), date('j', $date) - 1, date('Y', $date));
		}
		
		return $date;
	}
	
	private function getWeekEndDay($day, $month, $year) {
		$date = $this->getWeekStartDay($day, $month, $year);
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date) + 6, date('Y', $date));
		return $date;
	}
	
	private function removeDay($day) {
		if(! in_array($day, $this->daysRemoved)) {
			$this->daysRemoved[] = $day;
		}
	}
}
 
?>
