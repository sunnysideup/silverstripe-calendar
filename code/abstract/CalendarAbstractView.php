<?php

abstract class CalendarAbstractView extends ViewableData {
	
	// Static
	
	private static $names = array();
	
	// Attributes
	
	private $name;
	protected $number;
	
	protected $containerClass;
	protected $innerClass;
	
	protected $viewTitle;
	
	protected $template;
	protected $viewTitleDelimiter = ' - ';
	
	// Contructor
	
	function __construct($name, $number = 1) {
		parent::__construct();
		if(is_string($name)) {
			if(! in_array($name, self::$names)) {
				$this->name = $name;
				self::$names[] = $name;
			}
			else {
				user_error("CalendarAbstractView::__construct() : you cannot set the \$name attribute with the value '$name' because an other view with this name already exists", E_USER_ERROR);
			}
		}
		else {
			user_error('CalendarAbstractView::__construct() : you cannot set the $name attribute with a non string value', E_USER_ERROR);
		}
		if(is_int($number + 0)) {
			if($number >= 1) {
				$this->number = $number;
			}
			else {
				user_error('CalendarAbstractView::__construct() : you cannot set the $number attribute with a value less than 1', E_USER_ERROR);
			}
		}
		else {
			user_error('CalendarAbstractView::__construct() : you cannot set the $number attribute with a non integer value', E_USER_ERROR);
		}
		$this->init();
	}
	
	// Abstract Functions
	
	abstract function init();
	
	abstract function needsMonth();
	abstract function needsDay();
	
	abstract function Calendars(Calendar $calendar);
	
	abstract function prevLinkParams(Calendar $calendar);
	abstract function nextLinkParams(Calendar $calendar);
	
	abstract function viewLinkParamsAndTitle(Calendar $calendar);
	
	abstract function getLinkParams($date);
	
	abstract function title();
	abstract function DateTitle(Calendar $calendar);
	
	// Functions
	
	function getName() {return $this->name;}
	
	function setContainerClass($containerClass) {$this->containerClass = $containerClass;}
	function setInnerClass($innerClass) {$this->innerClass = $innerClass;}
	
	function setViewTitle($viewTitle) {$this->viewTitle = $viewTitle;}
	
	// Template Functions
	
	function setTemplate($template) {$this->template = $template;}
	
	function getTemplates() {
		if($this->template) $templates[] = $this->template;
		$class = get_class($this);
		while($class != 'CalendarAbstractView') {
			$templates[] = $class;
			$class = get_parent_class($class);
		}
		return $templates;
	}
	
	function setViewTitleDelimiter($viewTitleDelimiter) {$this->viewTitleDelimiter = $viewTitleDelimiter;}
	
	function NameClass() {
		$class = "{$this->class}_{$this->name}";
		$class[0] = strtolower($class[0]);
		return $class;
	}
	
	function showCalendar(Calendar $calendar) {
		$calendars = $this->Calendars($calendar);
		$templates = $this->getTemplates();
		return $this->customise(array('ID' => $calendar->ID(), 'ContainerClass' => $this->containerClass, 'Calendars' => $calendars))->renderWith($templates);
	}
}
 
?>
