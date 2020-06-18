<?php


namespace Sofa1\Mobiscroll;


use Exception;
use Sofa1\Mobiscroll\Models\AbstractDateElement;

class WeekDayElement extends AbstractDateElement
{
	/**
	 * @var string
	 */
	public $method;

	/**
	 * @var int
	 */
	public $weekday;

	public function __construct($weekday, $method)
	{
		$this->weekday = $weekday;
		$this->method = $method;
	}


	/**
	 * @return string
	 * @throws Exception
	 */
	function ToString()
	{
		switch ($this->method)
		{
			case DateOutputMethod::EveryWeekDay:
				return sprintf("'w%s'", $this->weekday);
				break;
		}
		throw new Exception("no valid method set");
	}
}
