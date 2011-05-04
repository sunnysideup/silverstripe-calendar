<?php

class CalendarYearView extends CalendarMonthView {
	
	// Attributes
	
	private $monthStart = 1;
	private $monthsRemoved = array();
	
	protected $monthInnerClass;
	protected $monthTitle;
	
	private $monthLinkView;
	private $monthLinkCalendar;
	private $monthLinkController;
	
	// Abstract Functions Implemented
	
	function init() {
		parent::init();
		$this->containerClass = 'yearView';
		$this->monthInnerClass = $this->innerClass;
		$this->viewTitle = 'return date(\'Y\', $date);';
		$this->innerClass = 'year';
		$this->monthTitle = 'return date(\'F Y\', $monthDate);';
	}
	
	function needsMonth() {return false;}
	
	function Calendars(Calendar $calendar) {
		$years = $this->Years($calendar);
		
		foreach($years as $year) {
			$calendars[] = $this->YearCalendar($year, $calendar);
		}
		
		return new DataObjectSet($calendars);
	}
	
	function prevLinkParams(Calendar $calendar) {
		$date = mktime(0, 0, 0, 1, 1, $calendar->getYear() - $this->number);
		return $this->getLinkParams($date);
	}
	
	function nextLinkParams(Calendar $calendar) {
		$date = mktime(0, 0, 0, 1, 1, $calendar->getYear() + $this->number);
		return $this->getLinkParams($date);
	}
	
	function viewLinkParamsAndTitle(Calendar $calendar) {
		$year = $calendar->getYear();
		$date = mktime(0, 0, 0, 1, 1, $year);
		$params = $this->getLinkParams($date);
		$title = $this->getCustomisedTitle($year);
		return array($params, $title);
	}
	
	function getLinkParams($date) {
		return array(
			'year' => date('Y', $date)
		);
	}
	
	function title() {return $this->number == 1 ? 'year' : "$this->number years";}
	
	function DateTitle(Calendar $calendar) {
		return $this->getCustomisedTitle($calendar->getYear());
	}
	
	function Years(Calendar $calendar) {
		$year = $calendar->getYear();
		
		for($i = 0; $i < $this->number; $i++) {
			$years[] = $year + $i;
		}
		
		return $years;
	}
	
	// Functions
	
	function startByJanuary() {$this->monthStart = 1;}
	function startByFebruary() {$this->monthStart = 2;}
	function startByMarch() {$this->monthStart = 3;}
	function startByApril() {$this->monthStart = 4;}
	function startByMay() {$this->monthStart = 5;}
	function startByJune() {$this->monthStart = 6;}
	function startByJuly() {$this->monthStart = 7;}
	function startByAugust() {$this->monthStart = 8;}
	function startBySeptember() {$this->monthStart = 9;}
	function startByOctober() {$this->monthStart = 10;}
	function startByNovember() {$this->monthStart = 11;}
	function startByDecember() {$this->monthStart = 12;}
	
	function removeJanuary() {$this->removeMonth(1);}
	function removeFebruary() {$this->removeMonth(2);}
	function removeMarch() {$this->removeMonth(3);}
	function removeApril() {$this->removeMonth(4);}
	function removeMay() {$this->removeMonth(5);}
	function removeJune() {$this->removeMonth(6);}
	function removeJuly() {$this->removeMonth(7);}
	function removeAugust() {$this->removeMonth(8);}
	function removeSeptember() {$this->removeMonth(9);}
	function removeOctober() {$this->removeMonth(10);}
	function removeNovember() {$this->removeMonth(11);}
	function removeDecember() {$this->removeMonth(12);}
	
	// Private Functions
	
	private function removeMonth($month) {
		if(! in_array($month, $this->monthsRemoved)) {
			$this->monthsRemoved[] = $month;
		}
	}
	
	private function YearCalendar($year, Calendar $currentCalendar) {
		
		// 1) Single Values
		
		$nowYear = date('Y');
		$nowMonth = date('n');
		
		$calendar['InnerClass'] = $this->innerClass;
		$calendar['ExtraInnerClass'] = "$this->innerClass$year";
		$calendar['IsNow'] = $year == $nowYear;
		$calendar['IsPast'] = $year < $nowYear;
		
		// 2) Months Values
		
		$months = $this->Months();
		
		if(count($months) == 0) {
			return new ArrayData($calendar);
		}
		
		foreach($months as $month) {
			$weeksGroups = $this->MonthWeeks($month, $year);
			
			// 1) Single Values
			
			$monthDate = mktime(0, 0, 0, $month, 1, $year);
			$values['IsNow'] = $calendar['IsNow'] && $month == $nowMonth;
			$values['IsPast'] = $calendar['IsPast'] || ($calendar['IsNow'] && $month < $nowMonth);
			$values['MonthClass'] = eval($this->monthClass);
			$values['MonthTitle'] = eval($this->monthTitle);
			
			$period = $this->Calendar($weeksGroups, $values, $currentCalendar);
			$period->setField('InnerClass', $this->monthInnerClass);
			
			if($this->monthLinkView) {
				$linkController = $currentCalendar->getController();
				if($this->monthLinkController) $linkController = $this->monthLinkController;
				$linkCalendar = $currentCalendar;
				if($this->monthLinkCalendar) $linkCalendar = $this->monthLinkCalendar;
				$params = $this->monthLinkView->getLinkParams($monthDate);
				$period->setField('Link', $linkCalendar->Link($linkController, $this->monthLinkView, $params));
			}
			$periods[] = $period;
		}
		
		$calendar['Months'] = new DataObjectSet($periods);
		
		return new ArrayData($calendar);
	}
	
	private function Months() {
		$month = $this->monthStart;
		
		$months = array();
		while($month <= 12) {
			if(! in_array($month, $this->monthsRemoved)) {
				$months[] = $month;
			}
			$month++;
		}
		
		return $months;
	}
	
	// Link Functions
	
	function linkMonthTo(CalendarMonthView $view, Calendar $calendar = null, $controller = null) {
		$this->monthLinkView = $view;
		$this->monthLinkCalendar = $calendar;
		$this->monthLinkController = $controller;
	}
	
	// Other Functions
	
	function getCustomisedTitle($year) {
		$date = mktime(0, 0, 0, 1, 1, $year);
		$result = eval($this->viewTitle);
		if($this->number > 1) {
			$date = mktime(0, 0, 0, 1, 1, $year + $this->number - 1);
			$result .= $this->viewTitleDelimiter . eval($this->viewTitle);
		}
		return $result;
	}
}
 
?>
