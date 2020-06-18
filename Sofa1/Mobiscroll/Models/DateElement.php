<?php


namespace Sofa1\Mobiscroll\Models;


use DateTime;
use Exception;
use Sofa1\Mobiscroll\DateOutputMethod;

class DateElement extends AbstractDateElement
{
	/**
	 * @var DateTime
	 */
	public $date;

	/**
	 * @var string
	 */
	public $method;

	/**
	 * DateElement constructor.
	 *
	 * @param $date
	 * @param $method
	 */
	public function __construct($date, $method)
	{
		$this->date = $date;
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
			case DateOutputMethod::SpecificDayEveryYear:
				return sprintf("'%s'", $this->date->format("m/d"));
				break;
			case DateOutputMethod::SpecificDay:
				return sprintf("'%s-%s-%s'",
					$this->date->format("Y"),
					$this->date->format("m"),
					$this->date->format("d"));
				break;
		}
		throw new Exception("no valid method set");
	}
}
