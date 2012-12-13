<?php

class Calendar extends ViewableData {
	
	// Static
	
	private static $names = array();
	
	static $session_calendars = 'Calendars';
	
	// Attributes
		
	protected $controller;
	
	protected $name;
	
	protected $initDone = false;
	
	protected $views = array();
	
	protected $view;
	
	protected $year;
	protected $month;
	protected $day;
	
	protected $defaultView;
	protected $defaultYear;
	protected $defaultMonth;
	protected $defaultDay;
	
	protected $sessionMode = false;
	
	protected $navigationBarTemplate;
	protected $viewBarTemplate;
	
	// Constructor
	
	function __construct($controller, $name, $views = null) {		
		parent::__construct();
		
		// 1) Controller Setting
		
		$this->controller = $controller;
		
		// 2) Name Setting
		
		if(is_string($name)) {
			if(! in_array($name, self::$names)) {
				$this->name = $name;
				self::$names[] = $name;
			}
			else {
				user_error("Calendar::__construct() : you cannot set the \$name attribute with the value '$name' because an other calendar with this name already exists", E_USER_ERROR);
			}
		}
		else {
			user_error('Calendar::__construct() : you cannot set the $name attribute with a non string value', E_USER_ERROR);
		}
		
		// 3) Views Setting
		
		if($views != null) {
			$this->addViews($views);
		}
	}
	
	function initValues() {
		if($this->initDone) return;
		
		$sessionName = self::$session_calendars . ".$this->name";
		$sessionValues = Session::get($sessionName);
		if($sessionValues) {
			$sessionValues = unserialize($sessionValues);
		}
		
		// 1) View Setting
		
		$views = array();
		if(isset($_REQUEST[$this->name]['view'])) {
			$views[] = $_REQUEST[$this->name]['view'];
		}
		if($this->sessionMode && $sessionValues && isset($sessionValues['view'])) {
			$views[] = $sessionValues['view'];
		}
		if($this->defaultView) {
			$views[] = is_a($this->defaultView, 'CalendarAbstractView') ? $this->defaultView->getName() : $this->defaultView;
		}
		foreach($views as $view) {
			$view = $this->getView($view);
			if($view) {
				$this->view = $view;
				break;
			}
		}
		if(! $this->view) {
			if(count($this->views) > 0) {
				$this->view = $this->views[0];
			}
			else {
				return;
			}
		}
		
		// 2) Year Setting
		
		$years = array();
		if(isset($_REQUEST[$this->name]['year'])) {
			$years[] = $_REQUEST[$this->name]['year'];
		}
		if($this->sessionMode && $sessionValues && isset($sessionValues['year'])) {
			$years[] = $sessionValues['year'];
		}
		if($this->defaultYear) {
			$years[] = $this->defaultYear;
		}
		foreach($years as $year) {
			if(is_numeric($year) && is_int($year + 0) && $year >= 1) {
				$this->year = $year;
				break;
			}
		}
		if(! $this->year) {
			$this->year = date('Y');
		}
		
		// 3) Month Setting
		
		if($this->view->needsMonth()) {
			$months = array();
			if(isset($_REQUEST[$this->name]['month'])) {
				$months[] = $_REQUEST[$this->name]['month'];
			}
			if($this->sessionMode && $sessionValues && isset($sessionValues['month'])) {
				$months[] = $sessionValues['month'];
			}
			if($this->defaultMonth) {
				$months[] = $this->defaultMonth;
			}
			foreach($months as $month) {
				if(is_numeric($month) && is_int($month + 0)) {
					if($month >= 1 && $month <= 12) {
						$this->month = $month;
					}
					else if($month < 1) {
						$this->year = $this->year > 1 ?	$this->year - 1 : date('Y');
						$this->month = 12;
					}
					else {
						$this->year++;
						$this->month = 1;
					}
					break;
				}
			}
			if(! $this->month) {
				$this->month = date('n');
			}
		}
		
		// 4) Day Setting
		
		if($this->view->needsDay()) {
			if(isset($_REQUEST[$this->name]['day'])) {
				$days[] = $_REQUEST[$this->name]['day'];
			}
			if($this->sessionMode && $sessionValues && isset($sessionValues['day'])) {
				$days[] = $sessionValues['day'];
			}
			if($this->defaultDay) {
				$days[] = $this->defaultDay;
			}
			$days[] = date('j');
			foreach($days as $day) {
				if(is_numeric($day) && is_int($day + 0)) {
					if($day >= 1 && $day <= 28) {
						$this->day = $day;
					}
					else if($day < 1) {
						if($this->month == 1) {
							$this->year = $this->year > 1 ?	$this->year - 1 : date('Y');
							$this->month = 12;
						}
						else {
							$this->month--;
						}
						$dayAfter = mktime(0, 0, 0, $this->month + 1, 1, $this->year);
						$this->day = date('j', mktime(0, 0, 0, date('n', $dayAfter), date('j', $dayAfter) - 1, date('Y', $dayAfter)));
					}
					else {
						$date = mktime(0, 0, 0, $this->month, $day, $this->year);
						if(date('n', $date) == $this->month && date('j', $date) == $day && date('Y', $date) == $this->year) {
							$this->day = $day;
						}
						else {
							if($this->month == 12) {
								$this->year++;
								$this->month = 1;
							}
							else {
								$this->month++;
							}
							$this->day = 1;
						}
					}
					break;
				}
			}
		}
		
		$this->initDone = true;
		
		// Session Mode
		
		if($this->sessionMode) {
			list($sessionValues, $title) = $this->view->viewLinkParamsAndTitle($this);
			$sessionValues = array_merge(array('view' => $this->view->getName()), $sessionValues);
			$sessionValues = serialize($sessionValues);
			Session::set($sessionName, $sessionValues);
		}
		else {
			Session::clear($sessionName);
		}
		
		// Css Requirements
		
		Requirements::themedCSS('calendar');
	}
	
	// Field Functions
	
	function addViews($views) {
		if(! is_array($views)) {
			$views = array($views);
		}
		foreach($views as $view) {
			if(is_a($view, 'CalendarAbstractView')) {
				if(! in_array($view, $this->views)) {
					$this->views[] = $view;
				}
			}
			else {
				user_error('Calendar::addViews() : you cannot add a view which class does not extend \'CalendarAbstractView\'', E_USER_ERROR);
			}
		}
	}
	
	private function getView($viewName) {
		foreach($this->views as $view) {
			if($view->getName() == $viewName) {
				return $view;
			}
		}
	}
	
	function removeViews($views) {
		if(! is_array($views)) {
			$views = array($views);
		}
		foreach($views as $view) {
			if(is_a($view, 'CalendarAbstractView')) {
				$index = array_search($view, $this->views);
				if($index) {
					unset($this->views[$index]);
				}
			}
			else {
				user_error('Calendar::removeViews() : you cannot remove a view which class does not extend \'CalendarAbstractView\'', E_USER_ERROR);
			}
		}
	}
	
	function forTemplate() {
		$this->initValues();
		if($this->view) return $this->view->showCalendar($this);
	}
	
	function NavigationBar() {
		if($this->navigationBarTemplate) $templates[] = $this->navigationBarTemplate;
		$templates[] = 'CalendarNavigationBar';
		return $this->renderWith($templates);
	}
	function ViewBar() {
		if($this->viewBarTemplate) $templates[] = $this->viewBarTemplate;
		$templates[] = 'CalendarViewBar';
		return $this->renderWith($templates);
	}
	
	function ID() {
		return "{$this->class}_{$this->name}";
	}
	function NavigationBarID() {
		return "{$this->ID()}_NavigationBar";
	}
	function ViewBarID() {
		return "{$this->ID()}_ViewBar";
	}
	
	function ViewTitle() {return $this->view->title();}
	function ViewDateTitle() {
		$this->initValues();
		return $this->view->DateTitle($this);
	}
	
	function Views() {
		$this->initValues();
		foreach($this->views as $view) {
			list($params, $title) = $view->viewLinkParamsAndTitle($this);
			$link = $this->Link($this->controller, $view, $params);
			$views[] = new ArrayData(array('Title' => $title, 'Link' => $link, 'Current' => $view->getName() == $this->view->getName()));
		}
		return new ArrayList($views);
	}
	
	function PrevLink() {
		$this->initValues();
		$params = $this->view->prevLinkParams($this);
		return $this->Link($this->controller, $this->view, $params);
	}
	
	function NextLink() {
		$this->initValues();
		$params = $this->view->nextLinkParams($this);
		return $this->Link($this->controller, $this->view, $params);
	}
	
	function Link($controller, CalendarAbstractView $view, array $params) {
		$link = is_string($controller) ? $controller : $controller->URLSegment;
		$params = array_merge(array('view' => $view->getName()), $params);
		foreach($params as $id => $val) {
			$link = HTTP::RAW_setGetVar("$this->name[$id]", $val, $link);
		}
		return $link;
	}
	
	function getController() {return $this->controller;}
	function getYear() {return $this->year;}
	function getMonth() {return $this->month;}
	function getDay() {return $this->day;}
	
	function setDefaultView($view) {$this->defaultView = $view;}
	function setDefaultYear($year) {$this->defaultYear = $year;}
	function setDefaultMonth($month) {$this->defaultMonth = $month;}
	function setDefaultDay($day) {$this->defaultDay = $day;}
	
	function setSessionMode($value) {$this->sessionMode = $value;}
	
	function setNavigationBarTemplate($template) {$this->navigationBarTemplate = $template;}
	function setViewBarTemplate($template) {$this->viewBarTemplate = $template;}
}

?>
