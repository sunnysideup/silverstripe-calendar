<?php

class CalendarTimePeriod
{

    // Attributes

    private $startTime;
    private $endTime;

    // Constructor

    public function __construct(CalendarTime $startTime, CalendarTime $endTime)
    {
        $this->setAttributes($startTime, $endTime);
    }
    
    // Functions

    public function setAttributes(CalendarTime $startTime, CalendarTime $endTime)
    {
        if ($this->isValidPeriod($startTime, $endTime)) {
            $this->startTime = $startTime;
            $this->endTime = $endTime;
        } else {
            user_error('CalendarTimePeriod::setAttributes() : you cannot construct a \'CalendarTimePeriod\' with the $startTime attribute superior or equal to the $endTime attribute', E_USER_ERROR);
        }
    }
    
    public function isValidPeriod(CalendarTime $startTime, CalendarTime $endTime)
    {
        return $startTime < $endTime;
    }
    
    public function getStartTime()
    {
        return $this->startTime;
    }
    public function getEndTime()
    {
        return $this->endTime;
    }
}
