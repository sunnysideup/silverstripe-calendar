<?php

abstract class CalendarAbstractView extends ViewableData
{

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

    public function __construct($name, $number = 1)
    {
        parent::__construct();
        if (is_string($name)) {
            if (! in_array($name, self::$names)) {
                $this->name = $name;
                self::$names[] = $name;
            } else {
                user_error("CalendarAbstractView::__construct() : you cannot set the \$name attribute with the value '$name' because an other view with this name already exists", E_USER_ERROR);
            }
        } else {
            user_error('CalendarAbstractView::__construct() : you cannot set the $name attribute with a non string value', E_USER_ERROR);
        }
        if (is_int($number + 0)) {
            if ($number >= 1) {
                $this->number = $number;
            } else {
                user_error('CalendarAbstractView::__construct() : you cannot set the $number attribute with a value less than 1', E_USER_ERROR);
            }
        } else {
            user_error('CalendarAbstractView::__construct() : you cannot set the $number attribute with a non integer value', E_USER_ERROR);
        }
        $this->init();
    }

    // Abstract Functions

    abstract public function init();

    abstract public function needsMonth();
    abstract public function needsDay();

    abstract public function Calendars(Calendar $calendar);

    abstract public function prevLinkParams(Calendar $calendar);
    abstract public function nextLinkParams(Calendar $calendar);

    abstract public function viewLinkParamsAndTitle(Calendar $calendar);

    abstract public function getLinkParams($date);

    abstract public function title();
    abstract public function DateTitle(Calendar $calendar);

    // Functions

    public function getName()
    {
        return $this->name;
    }

    public function setContainerClass($containerClass)
    {
        $this->containerClass = $containerClass;
    }
    public function setInnerClass($innerClass)
    {
        $this->innerClass = $innerClass;
    }

    public function setViewTitle($viewTitle)
    {
        $this->viewTitle = $viewTitle;
    }

    // Template Functions

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplates()
    {
        if ($this->template) {
            $templates[] = $this->template;
        }
        $class = get_class($this);
        while ($class != 'CalendarAbstractView') {
            $templates[] = $class;
            $class = get_parent_class($class);
        }
        return $templates;
    }

    public function setViewTitleDelimiter($viewTitleDelimiter)
    {
        $this->viewTitleDelimiter = $viewTitleDelimiter;
    }

    public function NameClass()
    {
        $class = "{$this->class}_{$this->name}";
        $class[0] = strtolower($class[0]);
        return $class;
    }

    public function showCalendar(Calendar $calendar)
    {
        $calendars = $this->Calendars($calendar);
        $templates = $this->getTemplates();

        return $this->customise(array('ID' => $calendar->ID(), 'ContainerClass' => $this->containerClass, 'Calendars' => $calendars))->renderWith($templates);
    }
}
