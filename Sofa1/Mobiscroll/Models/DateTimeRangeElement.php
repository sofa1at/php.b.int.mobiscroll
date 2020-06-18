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
	public $date;
	/**
	 * @var string
	 */
	public $fromTime;

	/**
	 * @var string
	 */
	public $toTime;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * DateTimeRangeElement constructor.
	 **
	 *
	 * @param DateTime $date
	 * @param string $fromTime
	 * @param string $toTime
	 */
	public function __construct($date, $fromTime, $toTime)
	{
		$this->date = $date;
		$this->fromTime = $fromTime;
		$this->toTime = $toTime;

	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function ToString()
	{
		return sprintf("{d: new Date(%s,%s,%s), start:'%s', end:'%s' }",
			$this->date->format("Y"),
			$this->date->format("m") - 1,
			$this->date->format("d"),
			$this->fromTime,
			$this->toTime);

	}
}
