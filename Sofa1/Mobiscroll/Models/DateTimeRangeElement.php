<?php


namespace Sofa1\Mobiscroll\Models;


use DateTime;
use Exception;
use Sofa1\Mobiscroll\DateOutputMethod;
use Sofa1\Mobiscroll\Models\AbstractDateElement;

class DateTimeRangeElement extends AbstractDateElement
{

	/**
	 * @var DateTime
	 */
	public $from;

	/**
	 * @var DateTime
	 */
	public $to;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * DateTimeRangeElement constructor.
	 *
	 * @param DateTime $from
	 * @param DateTime $to
	 * @param string $method
	 */
	public function __construct($from, $to, $method)
	{
		$this->from = $from;
		$this->to = $to;
		$this->method = $method;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function ToString()
	{
		switch ($this->method)
		{
			case DateOutputMethod::EveryDayTimeRange:
				return sprintf("{start:'%s', end:'%s'}",
					$this->from->format("H:i"),
					$this->to->format("H:i"));
				break;
			case DateOutputMethod::SpecificDayTimeRange:
				return sprintf("{d: new Date(%s,%s,%s), start:'%s', end:'%s' }",
					$this->from->format("Y"),
					$this->from->format("m") - 1,
					$this->from->format("d"),
					$this->from->format("H:i"),
					$this->to->format("H:i"));
		}

		throw new Exception("method not provided on DateTimeRangeElement");
	}
}
