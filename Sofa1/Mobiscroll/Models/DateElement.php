<?php


namespace Sofa1\Mobiscroll;


use DateTime;
use Exception;
use Sofa1\Mobiscroll\Models\AbstractDateElement;

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
				return sprintf("'%s'", $this->date->format("M/Y"));
				break;
			case DateOutputMethod::SpecificDay:
				return sprintf("new Date(%s,%s,%s)",
					$this->date->format("Y"),
					$this->date->format("m") - 1,
					$this->date->format("d"));
				break;
		}
		throw new Exception("no valid method set");
	}
}
