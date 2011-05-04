<?php

class CalendarTimePeriod {

	// Attributes
	
	private $startTime;
	private $endTime;

	// Constructor
	
	function __construct(CalendarTime $startTime, CalendarTime $endTime) {
		$this->setAttributes($startTime, $endTime);
	}
	
	// Functions
	
	function setAttributes(CalendarTime $startTime, CalendarTime $endTime) {
		if($this->isValidPeriod($startTime, $endTime)) {
			$this->startTime = $startTime;
			$this->endTime = $endTime;
		}
		else {
			user_error('CalendarTimePeriod::setAttributes() : you cannot construct a \'CalendarTimePeriod\' with the $startTime attribute superior or equal to the $endTime attribute', E_USER_ERROR);
		}
	}
	
	function isValidPeriod(CalendarTime $startTime, CalendarTime $endTime) {
		return $startTime < $endTime;
	}
	
	function getStartTime() {return $this->startTime;}
	function getEndTime() {return $this->endTime;}
}

?>
