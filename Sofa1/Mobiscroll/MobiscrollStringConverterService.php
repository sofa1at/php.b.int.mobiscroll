<?php


namespace Sofa1\Mobiscroll;


use Exception;
use Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel;
use Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection;

class MobiscrollStringConverterService implements IMobiscrollStringConverterService
{
	/**
	 * @param \Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection $stationDateElementCollectionInvalid
	 * @param \Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection $stationDateElementCollectionValid
	 *
	 * @return string
	 * @throws Exception
	 */
	public function DateTimeValidationToString(StationDateElementCollection $stationDateElementCollectionInvalid, StationDateElementCollection $stationDateElementCollectionValid): string
	{
		return sprintf("invalid: [%s], valid: [%s]", $this->DateElementCollectionToString($stationDateElementCollectionInvalid), $this->DateElementCollectionToString($stationDateElementCollectionValid));
	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection $stationDateElementCollection
	 *
	 * @return string
	 * @throws Exception
	 */
	public function DateElementCollectionToString($stationDateElementCollection)
	{
		$returnValues = array();
		foreach ($stationDateElementCollection->Items as $item)
		{
			$itemClass = get_class($item);
			switch ($itemClass)
			{
				case 'Sofa1\Core\StationDateTimeService\Elements\DateElementModel' :
					$returnValues[] = $this->DateElementToString($item);
					break;
				case 'Sofa1\Core\StationDateTimeService\Elements\DateTimeRangeElementModel' :
					$returnValues[] = $this->DateTimeRangeElementToString($item);
					break;
				case 'Sofa1\Core\StationDateTimeService\Elements\WeekDayElementModel':
					$returnValues[] = $this->WeekDayElementToString($item);
					break;
				case 'Sofa1\Core\StationDateTimeService\Elements\WeekdayTimeRangeElementModel' :
					$returnValues[] = $this->WeekdayTimeRangeElementToString($item);
					break;
			}
		}

		return implode(",", $returnValues);
	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Elements\AbstractDateElementDto $dateElementModel
	 *
	 * @return string
	 * @throws Exception
	 */
	public function DateElementToString($dateElementModel)
	{
		switch ($dateElementModel->method)
		{
			case \Sofa1\Core\StationDateTimeService\Helpers\StationDateOutputMethod::SpecificDayEveryYear:
				return sprintf("'%s'", $this->date->format("m/d"));
				break;
			case \Sofa1\Core\StationDateTimeService\Helpers\StationDateOutputMethod::SpecificDay:
				return sprintf("'%s-%s-%s'",
					$dateElementModel->date->format("Y"),
					$dateElementModel->date->format("m"),
					$dateElementModel->date->format("d"));
				break;
		}
		throw new Exception("no valid method set");
	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Elements\AbstractDateElementDto $dateTimeRangeElementModel
	 *
	 * @return string
	 * @throws Exception
	 */
	public function DateTimeRangeElementToString($dateTimeRangeElementModel)
	{
		return sprintf("{d: new Date(%s,%s,%s), start:'%s', end:'%s' }",
			$dateTimeRangeElementModel->date->format("Y"),
			$dateTimeRangeElementModel->date->format("m") - 1,
			$dateTimeRangeElementModel->date->format("d"),
			$dateTimeRangeElementModel->fromTime,
			$dateTimeRangeElementModel->toTime);

	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Elements\AbstractDateElementDto $weekdayTimeRangeElementModel
	 *
	 * @return string
	 * @throws Exception
	 */
	public function WeekdayTimeRangeElementToString($weekdayTimeRangeElementModel)
	{
		switch ($weekdayTimeRangeElementModel->method)
		{
			case \Sofa1\Core\StationDateTimeService\Helpers\StationDateOutputMethod::EveryWeekDayTimeRange:
				return sprintf("{d: 'w%s', start:'%s', end:'%s'}",
					$weekdayTimeRangeElementModel->weekday,
					$weekdayTimeRangeElementModel->from,
					$weekdayTimeRangeElementModel->to);
		}

		throw new Exception("method not provided on DateTimeRangeElement");
	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Elements\AbstractDateElementDto $weekDayElementModel
	 *
	 * @return string
	 * @throws Exception
	 */
	function WeekDayElementToString($weekDayElementModel)
	{
		switch ($weekDayElementModel->method)
		{
			case \Sofa1\Core\StationDateTimeService\Helpers\StationDateOutputMethod::EveryWeekDay:
				return sprintf("'w%s'", $weekDayElementModel->weekday);
				break;
		}
		throw new Exception("no valid method set");
	}

	/**
	 * @param \Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel $lable
	 *
	 * @return string
	 */
	public function DateLabelElementToString(DateLabelElementModel $label): string
	{
		return sprintf("{start: new Date(%s,%s,%s), end: new Date(%s,%s,%s), text: '%s'}",
			$label->from->format("Y"),
			$label->from->format("m") - 1,
			$label->from->format("d"),
			$label->to->format("Y"),
			$label->to->format("m") - 1,
			$label->to->format("d"),
			$label->infoText);
	}
}
