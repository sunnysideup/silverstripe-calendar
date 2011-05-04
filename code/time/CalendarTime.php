<?php

class CalendarTime {

	// Constants
	
	private static $hours_max = 23;
	private static $minutes_max = 59;
	private static $seconds_max = 59;

	// Attributes
	
	private $hours;
	private $minutes;
	private $seconds;

	// Constructor
	
	function __construct($hours = 0, $minutes = 0, $seconds = 0) {
		$this->setHours($hours);
		$this->setMinutes($minutes);
		$this->setSeconds($seconds);
	}
	
	// Functions
	
	function getHours() {
		return $this->hours;
	}
	
	function getMinutes() {
		return $this->minutes;
	}
	
	function getSeconds() {
		return $this->seconds;
	}
	
	function setHours($hours) {
		$this->setAttribute('hours', $hours, self::$hours_max);
	}
	
	function setMinutes($minutes) {
		$this->setAttribute('minutes', $minutes, self::$minutes_max);
	}
	
	function setSeconds($seconds) {
		$this->setAttribute('seconds', $seconds, self::$seconds_max);
	}
	
	function __less(CalendarTime $time) {
		return $this->hours < $time->hours || ($this->hours == $time->hours && ($this->minutes < $time->minutes || ($this->minutes == $time->minutes && $this->seconds < $time->seconds)));
	}
	
	function add(CalendarTime $time) {
		$hours = $this->hours + $time->hours;
		$minutes = $this->minutes + $time->minutes;
		$seconds = $this->seconds + $time->seconds;
		
		if($seconds > self::$seconds_max) {
			$factor = self::$seconds_max + 1;
			$minutes += intval($seconds / $factor);
			$seconds %= $factor;
		}
		if($minutes > self::$minutes_max) {
			$factor = self::$minutes_max + 1;
			$hours += intval($minutes / $factor);
			$minutes %= $factor;
		}
		$factor = self::$hours_max + 1;
		$hours %= $factor;
		
		return new CalendarTime($hours, $minutes, $seconds);
	}
	
	// Private Functions
	
	private function setAttribute($name, $value, $max) {
		if(is_numeric($value)) {
			if(is_int($value + 0)) {
				if($value >= 0 && $value <= $max) {
					$this->$name = $value;
				}
				else if($value < 0) {
					user_error("CalendarTime::setAttribute() : you cannot set the \$$name attribute with a negative value", E_USER_ERROR);
				}
				else {
					user_error("CalendarTime::setAttribute() : you cannot set the \$$name attribute with a value more important than $max", E_USER_ERROR);
				}
			}
			else {
				user_error("CalendarTime::setAttribute() : you cannot set the \$$name attribute with a non integer value", E_USER_ERROR);
			}
		}
		else {
			user_error("CalendarTime::setAttribute() : you cannot set the \$$name attribute with a non numeric value", E_USER_ERROR);
		}
	}
}

?>
