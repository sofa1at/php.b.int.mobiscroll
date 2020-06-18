<?php


namespace Sofa1\Mobiscroll;


use Sofa1\Mobiscroll\Models\AbstractDateElement;
use Sofa1\Mobiscroll\Models\WeekdayTimeRangeElement;

class DateElementCollection
{
	/**
	 * @var AbstractDateElement[]
	 */
	private $Items = array();

	/**
	 * @param AbstractDateElement $element
	 */
	public function Add($element)
	{
		$this->Items[] = $element;
	}

	/**
	 * @param int $weekday
	 */
	public function AddWeekday($weekday)
	{
		$this->Items[] = new WeekdayTimeRangeElement($weekday, "00:00", "23:59", DateOutputMethod::EveryWeekDayTimeRange);
	}

	/**
	 * @param AbstractDateElement[] $elements
	 */
	public function AddArray($elements)
	{
		$this->Items = array_merge($this->Items, $elements);
	}

	public function ToString()
	{
		$returnValues = array();
		foreach ($this->Items as $item)
		{
			$returnValues[] = $item->ToString();
		}

		return implode(",", $returnValues);
	}
}
