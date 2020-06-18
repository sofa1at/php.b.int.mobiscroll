<?php


namespace Sofa1\Mobiscroll;


use Exception;
use Sofa1\Mobiscroll\Models\AbstractDateElement;

class WeekdayTimeRangeElement extends AbstractDateElement
{
	/**
	 * @var int
	 */
	public $weekday;
	/**
	 * From Time in the format H:i
	 * @var string
	 */
	public $from;
	/**
	 * From Time in the format H:i
	 * @var string
	 */
	public $to;

	/**
	 * DateTimeRangeElement constructor.
	 *
	 * @param int $weekday
	 * @param $from
	 * @param $to
	 * @param string $method
	 */
	public function __construct($weekday, $from, $to, $method)
	{
		$this->method = $method;
		$this->weekday = $weekday;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function ToString()
	{
		switch ($this->method)
		{
			case DateOutputMethod::EveryWeekDayTimeRange:
				return sprintf("{d: 'w%s', start:'%s', end:'%s'}",
					$this->weekday,
					$this->from,
					$this->to);
		}

		throw new Exception("method not provided on DateTimeRangeElement");
	}
}
