<?php

class CalendarDayView extends CalendarAbstractTimeView {
		
	// Abstract Functions Implemented
	
	function init() {
		parent::init();
		$this->containerClass = 'dayView';
		$this->innerClass = 'day';
		$this->viewTitle = 'return date(\'l jS F Y\', $date);';
	}
	
	function prevLinkParams(Calendar $calendar) {
		$date = mktime(0, 0, 0, $calendar->getMonth(), $calendar->getDay() - $this->number, $calendar->getYear());
		return $this->getLinkParams($date);
	}
	
	function nextLinkParams(Calendar $calendar) {
		$date = mktime(0, 0, 0, $calendar->getMonth(), $calendar->getDay() + $this->number, $calendar->getYear());
		return $this->getLinkParams($date);
	}
	
	function title() {return $this->number == 1 ? 'day' : "$this->number days";}
	
	function Dates(Calendar $calendar) {
		$year = $calendar->getYear();
		$month = $calendar->getMonth();
		$day = $calendar->getDay();
		
		for($i = 0; $i < $this->number; $i++) {
			if($i == 0) {
				$lastDate = mktime(0, 0, 0, $month, $day, $year);
			}
			else {
				$lastDate = mktime(0, 0, 0, date('n', $lastDate), date('j', $lastDate) + 1, date('Y', $lastDate));
			}
			$datesGroups[] = array($lastDate);
		}
		
		return $datesGroups;
	}
	
	function getCustomisedTitle($day, $month, $year) {
		$date = mktime(0, 0, 0, $month, $day, $year);
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$date = mktime(0, 0, 0, $month, $day + $this->number - 1, $year);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}
}
 
?>
