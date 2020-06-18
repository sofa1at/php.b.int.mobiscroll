<?php


namespace Sofa1\Mobiscroll;

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

		$sortedBusinessHours = $this->GetBusinessHoursSorted($businessHours, 'sunday');
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
					$validation->Invalid->Add(new WeekdayTimeRangeElement($i, "00:00", "23:59", DateOutputMethod::EveryWeekDayTimeRange));
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
