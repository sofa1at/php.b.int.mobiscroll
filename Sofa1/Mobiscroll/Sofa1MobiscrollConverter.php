<?php


namespace Sofa1\Mobiscroll;

use DateInterval;
use DateTime;
use Sofa1\Mobiscroll\Helpers\DateTimeHelper;
use Sofa1\Mobiscroll\Models\AbstractBusinessHours;
use Sofa1\Mobiscroll\Models\AbstractTimeSetting;
use Sofa1\Mobiscroll\Models\AbstractTimeSettingPeriod;
use Sofa1\Mobiscroll\Models\AbstractTimeSettingPeriodDay;
use Sofa1\Mobiscroll\Models\WeekDayElement;
use Sofa1\Mobiscroll\Models\WeekdayTimeRangeElement;

class Sofa1MobiscrollConverter
{

	/**
	 * @var DateTimeValidation
	 */
	private $validation;

	/**
	 * @var AbstractBusinessHours[]
	 */
	private $businessHours;

	/**
	 * @var AbstractTimeSetting[]
	 */
	private $timeSettings;

	/**
	 * @var int $max
	 */
	private $max;

	/**
	 * @var DateTime
	 */
	private $startDate;

	public function __construct($max = 365, $startDate = null)
	{
		$this->businessHours = array();
		$this->timeSettings = array();
		$this->max = $max;
		if ($startDate == null)
		{
			$this->startDate = new DateTime('now', new \DateTimeZone('UTC'));
		}
	}

	/**
	 * @param AbstractBusinessHours[] $businessHours
	 */
	public function AddBusinessHours($businessHours)
	{
		$this->businessHours = array_merge($this->businessHours, $businessHours);
	}

	/**
	 * @param AbstractTimeSetting $timeSettings
	 */
	public function AddTimeSettings($timeSettings)
	{
		$this->timeSettings[] = $timeSettings;
	}

	/**
	 * @return string
	 */
	public function ToString()
	{
		$this->validation = new DateTimeValidation();
		if ( ! empty($this->businessHours) && empty($this->timeSettings))
		{
			$this->ValidateBusinessHours();
		}
		else if ( ! empty($this->timeSettings) && empty($this->businessHours))
		{
			$this->ValidateTimeSettings();
		}
		else
		{
			$this->AddTimeSettings($this->ConvertBusinessHoursToTimeSetting());
			$this->ValidateTimeSettings();
		}

		return $this->validation->ToString();
	}


	public function ValidateBusinessHours()
	{
		if (empty($this->businessHours))
		{
			return null;
		}

		$sortedBusinessHours = $this->GetBusinessHoursSorted($this->businessHours, 'sunday'); // sunday is needed for the mobiscroll control offset
		for ($i = 0; $i <= 6; $i ++)
		{
			if ( ! empty($sortedBusinessHours[$i]) && count($sortedBusinessHours[$i]) > 0)
			{
				$valideElements = array();
				$hasClosedElement = false;
				foreach ($sortedBusinessHours[$i] as $businessHour)
				{
					if ($sortedBusinessHours[$i][0]->IsOpen)
					{
						$valideElements[] = new WeekdayTimeRangeElement($i, $businessHour->From, $businessHour->To, DateOutputMethod::EveryWeekDayTimeRange);
					}
					else
					{
						$hasClosedElement = true;
						break;
					}
				}

				if ( ! $hasClosedElement && ! empty($valideElements))
				{
					$this->validation->Invalid->AddWeekday($i);
					$this->validation->Valid->AddArray($valideElements);
				}
				else
				{
					$this->validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
				}
			}
			else
			{
				$this->validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
			}
		}
	}

	public function ValidateTimeSettings()
	{
		if (empty($this->timeSettings))
		{
			return;
		}

		for ($i = 0; $i < 7; $i ++)
		{
			$this->validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
		}

		foreach ($this->timeSettings as $timeSetting)
		{
			$date = clone $this->startDate;
			for ($i = 0; $i <= $this->max; $i ++)
			{
				foreach ($timeSetting->TimeSettingPeriods as $timeSettingPeriod)
				{
					$fromDate = $timeSettingPeriod->FromDate->format("md");
					$toDate = $timeSettingPeriod->ToDate->format("md");
					if ($date->format("md") < $fromDate || $date->format("md") > $toDate)
					{
						continue;
					}
					if ( ! empty($timeSettingPeriod->TimeSettingPeriodDays))
					{
						foreach ($timeSettingPeriod->TimeSettingPeriodDays as $periodDay)
						{
							$dayofweek = $date->format('l');
							if ((empty($periodDay->Day) ? true : $periodDay->Day == DateTimeHelper::GetWeekdayNumberFromString($dayofweek)))
							{
								$this->validation->AddValidDateTimeRange(clone $date, $periodDay->FromTime->format("H:i"), $periodDay->ToTime->format("H:i"));
							}
						}
					}
					else
					{
						$this->validation->AddValidDateTimeRange(clone $date, "00:00", "23:59");
					}
				}

				$date->add(new DateInterval('P1D'));
			}
		}
	}

	/**
	 * @return AbstractTimeSetting|null
	 * @throws \Exception
	 */
	private function ConvertBusinessHoursToTimeSetting()
	{
		if (empty($this->businessHours))
		{
			return null;
		}

		$startDate = clone $this->startDate;
		$endDate = clone $this->startDate;
		$endDate->add(new DateInterval('P' . $this->max . 'D'));

		$timeSetting = new AbstractTimeSetting();
		$timeSettingPeriod = new AbstractTimeSettingPeriod();
		$timeSettingPeriod->FromDate = $startDate;
		$timeSettingPeriod->ToDate = $endDate;

		foreach ($this->businessHours as $businessHour)
		{
			$periodDay = new AbstractTimeSettingPeriodDay();
			$periodDay->Day = $businessHour->Day;
			$periodDay->FromTime = new DateTime(sprintf("1990-01-01 %s", $businessHour->From));
			$periodDay->ToTime = new DateTime(sprintf("1990-01-01 %s", $businessHour->To));
			$timeSettingPeriod->TimeSettingPeriodDays[] = $periodDay;
		}
		$timeSetting->TimeSettingPeriods[] = $timeSettingPeriod;

		return $timeSetting;
	}

	private function GetBusinessHoursSorted($businessHours, $firstDayOfWeek = 'monday')
	{
		if (empty($businessHours))
		{
			return false;
		}
		$days = array();
		foreach ($businessHours as $stationBusinessHour)
		{
			$days[DateTimeHelper::GetWeekdayNumberFromString($stationBusinessHour->Day, $firstDayOfWeek)][strtotime($stationBusinessHour->From)] = $stationBusinessHour;
			ksort($days[DateTimeHelper::GetWeekdayNumberFromString($stationBusinessHour->Day, $firstDayOfWeek)]);
		}
		ksort($days);
		$sort = array();
		foreach ($days as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $time)
				{
					$sort[$key][] = $time;
				}
			}
		}

		return $sort;
	}

}
