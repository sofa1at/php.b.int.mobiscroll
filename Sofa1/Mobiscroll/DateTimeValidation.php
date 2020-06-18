<?php


namespace Sofa1\Mobiscroll;


use DateTime;
use Sofa1\Mobiscroll\Models\DateElement;
use Sofa1\Mobiscroll\Models\DateTimeRangeElement;

class DateTimeValidation
{
	/**
	 * @var DateElementCollection
	 */
	public $Valid;

	/**
	 * @var DateElementCollection
	 */
	public $Invalid;

	public function __construct()
	{
		$this->Valid = new DateElementCollection();
		$this->Invalid = new DateElementCollection();
	}

	/***
	 * @param DateTime $day
	 * @param string $fromTime
	 * @param string $toTime
	 */
	public function AddValidDateTimeRange($day, $fromTime, $toTime)
	{
		$this->Invalid->Add(new DateTimeRangeElement($day, "00:00", "23:59"));
		$this->Valid->Add(new DateElement($day, DateOutputMethod::SpecificDay));
		$this->Valid->Add(new DateTimeRangeElement($day, $fromTime, $toTime));
	}

	public function ToString()
	{
		return sprintf("invalid: [%s], valid: [%s]", $this->Invalid->ToString(), $this->Valid->ToString());
	}
}
