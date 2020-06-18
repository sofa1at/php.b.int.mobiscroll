<?php


namespace Sofa1\Mobiscroll\Helpers;

use DateTime;
use Exception;

class DateTimeHelper
{
	/**
	 * @return DateTime
	 * @throws Exception
	 */
	static function GetNextDueDateInterval()
	{
		$t = time();
		$interval = 15 * 60;
		$last = $t - $t % $interval;
		$next = $last + $interval;

		return new DateTime(Date('Y - m - d') . " " . strftime(' % H:%M:%S', $next));
	}

	/**
	 * Returns 0-6 for given monday-sunday
	 *
	 * @param $weekday "accepting 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'"
	 *
	 * @param string $firstDayOfWeek
	 *
	 * @return bool|int
	 */
	static function GetWeekdayNumberFromString($weekday, $firstDayOfWeek = 'monday')
	{
		$weekdays = [
			"monday",
			"tuesday",
			"wednesday",
			"thursday",
			"friday",
			"saturday",
			"sunday"
		];

		if ( ! in_array(strtolower($weekday), $weekdays))
		{
			return false;
		}

		$firstDayIndex = array_search($firstDayOfWeek, $weekdays);
		for ($i = 0; $i <= 6; $i ++)
		{
			if ($weekdays[$i] == strtolower($weekday))
			{
				$index = $i - $firstDayIndex;
				if ($index < 0)
				{
					$index += 7;
				}

				return $index;
			}
		}

		return false;
	}


}
