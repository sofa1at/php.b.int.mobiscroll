<?php


namespace Sofa1\Mobiscroll;

use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Sofa1\Mobiscroll\Helpers\DateTimeHelper;
use Sofa1\Mobiscroll\Models\AbstractBusinessHours;
use Sofa1\Mobiscroll\Models\AbstractTimeSetting;
use Sofa1\Mobiscroll\Models\WeekDayElement;
use Sofa1\Mobiscroll\Models\WeekdayTimeRangeElement;

class Sofa1MobiscrollConverter
{
	/**
	 * @param $businessHours AbstractBusinessHours[]
	 *
	 * @return string
	 */
	public function GetValidTimesByBusinessHoursJson($businessHours)
	{
		if (empty($businessHours))
		{
			return "";
		}

		$sortedBusinessHours = $this->GetBusinessHoursSorted($businessHours, 'sunday'); // sunday is needed for the mobiscroll control offset
		$validation = new DateTimeValidation();
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
					$validation->Invalid->AddWeekday($i);
					$validation->Valid->AddArray($valideElements);
				}
				else
				{
					$validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
				}
			}
			else
			{
				$validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
			}
		}

		return $validation->ToString();
	}

	/**
	 * @param $timeSetting AbstractTimeSetting
	 *
	 * @param $startDate DateTime
	 * @param $maxDays int
	 *
	 * @return string
	 * @throws Exception
	 */
	public function GetValidTimesByTimeSettingsJson($timeSetting, $startDate, $maxDays)
	{
		/**
		 * excepted result
		 * invalid: [
		 * 'w2', // Every Tuesday
		 * { d: new Date(2020, 5, 23), start: '00:00', end: '23:59' } // Specific Tuesday invalid times
		 * ],
		 * valid: [
		 * '2020-06-23', // Enable specific Tuesday
		 * { d: 'new Date(2020, 5, 23),' start: '12:00', end: '21:59' } // Specific Tuesday valid times
		 * ]
		 */

		if (empty($timeSetting) || empty($timeSetting->TimeSettingPeriods))
		{
			return "";
		}

		$validation = new DateTimeValidation();
		for ($i = 0; $i < 7; $i ++)
		{
			$validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
		}

		$date = clone $startDate;
		for ($i = 0; $i <= $maxDays; $i ++)
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
							$validation->AddValidDateTimeRange(clone $date, $periodDay->FromTime->format("H:i"), $periodDay->ToTime->format("H:i"));
						}
					}
				}
				else
				{
					$validation->AddValidDateTimeRange(clone $date, "00:00", "23:59");
				}
			}

			$date->add(new DateInterval('P1D'));
		}

		// format timesettings to array
		// '06-05' =>
		//    array (size=1)
		//      0 =>
		//        object(App\TimeSettings\TimeSettingPeriodDay)[70]
		//  '07-05' =>
		//    array (size=1)
		//      0 =>
		//        object(App\TimeSettings\TimeSettingPeriodDay)[70]


		return $validation->ToString();
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
