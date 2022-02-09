<?php


namespace Sofa1\Mobiscroll\v5;

use Sofa1\Core\Api\Dto\DateTime\StationBusinessHolidayDto;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel;

class MobiscrollBusinessHours
{

	public \DateTime $FromDate;

	public \DateTime $ToDate;

	public array $OpenTimeSettingPeriodDays;
	public array $ClosedTimeSettingPeriodDays;

	public function __construct()
	{
		$this->OpenTimeSettingPeriodDays = [];
		$this->ClosedTimeSettingPeriodDays = [];
	}

	public function AddBusinessHoliday(StationBusinessHolidayDto $holiday)
	{
		$this->FromDate = $holiday->From;
		$this->ToDate = $holiday->To;
	}

	public function AddTimeSettingPeriod(TimeSettingPeriodModel $timeSettingPeriod, bool $IsOpen)
	{
		$this->FromDate = $timeSettingPeriod->FromDate;
		$this->ToDate = $timeSettingPeriod->ToDate;
		if ($IsOpen)
		{
			$this->OpenTimeSettingPeriodDays = $timeSettingPeriod->TimeSettingPeriodDays;
		}
		else
		{
			$this->ClosedTimeSettingPeriodDays = $timeSettingPeriod->TimeSettingPeriodDays;
		}
	}

	public function AddBusinessHours(array $businessHours)
	{
		foreach ($businessHours as $bh)
		{
			$record = new TimeSettingPeriodDayModel();
			$record->Day = $bh->Day;
			$record->FromTime = new \DateTime("1980-01-01 " . $bh->From);
			$record->ToTime = new \DateTime("1980-01-01 " . $bh->To);
			if ($bh->IsOpen)
			{
				$this->OpenTimeSettingPeriodDays[] = $record;
			}
			else
			{
				$this->ClosedTimeSettingPeriodDays[] = $record;
			}
		}
	}

	private function RenderBusinessHoliday(): string
	{
		$returnValue = "";
		if ($this->FromDate->format("Y.m.d") != $this->ToDate->format("Y.m.d"))
		{
			// first segment
			$returnValue .= "{start:'{$this->FromDate->format("Y-m-d\TH:i")}',end:'{$this->FromDate->format("Y-m-d")}T23:59'}";
			// end segment
			$returnValue .= (empty($returnValue) ? "" : ",") . "{start:'{$this->ToDate->format("Y-m-d")}T00:00',end:'{$this->ToDate->format("Y-m-d\TH:i")}'}";
			// mid segment
			$newStart = clone $this->FromDate;
			$newStart->add(new \DateInterval('P1D'));
			$newEnd = clone $this->ToDate;
			$newEnd->sub(new \DateInterval('P1D'));
			$returnValue .= (empty($returnValue) ? "" : ",") . "{start:'{$newStart->format("Y-m-d")}',end:'{$newEnd->format("Y-m-d")}'}";
		}

		return $returnValue;
	}

	/**
	 * @param TimeSettingPeriodDayModel[] $timeSettingPeriodDays
	 * @param bool $isOpen
	 *
	 * @return string
	 */
	private function RenderRecurring(array $timeSettingPeriodDays, bool $isOpen): string
	{
		$hourSets = [];

		foreach ($timeSettingPeriodDays as $day)
		{
			$newFromTime = clone $day->FromTime;
			$newToTime = clone $day->ToTime;
			if ($isOpen)
			{
				$newFromTime->sub(new \DateInterval("PT1M"));
				$newToTime->add(new \DateInterval("PT1M"));
			}
			$key = "{$newFromTime->format("H:i")};{$newToTime->format("H:i")}";
			if (empty($hourSets[$key]))
			{
				$hourSets[$key] = [];
			}
			$hourSets[$key][] = $day;
		}

		$returnValue = "";
		foreach ($hourSets as $key => $days)
		{
			$weekdays = "";
			/** @var TimeSettingPeriodDayModel $day */
			foreach ($days as $day)
			{
				if ( ! empty($weekdays))
				{
					$weekdays = "{$weekdays},";
				}
				switch ($day->Day)
				{
					case 1:
					case "Monday":
						$weekdays = "{$weekdays}MO";
						break;
					case 2:
					case "Tuesday":
						$weekdays = "{$weekdays}TU";
						break;
					case 3:
					case "Wednesday":
						$weekdays = "{$weekdays}WE";
						break;
					case 4:
					case "Thursday":
						$weekdays = "{$weekdays}TH";
						break;
					case 5:
					case "Friday":
						$weekdays = "{$weekdays}FR";
						break;
					case 6:
					case "Saturday":
						$weekdays = "{$weekdays}SA";
						break;
					case 7:
					case "Sunday":
						$weekdays = "{$weekdays}SU";
						break;
				}
			}

			$split = preg_split("/;/", $key);
			$from = ! empty($this->FromDate) ? ",from:'{$this->FromDate->format("Y-m-d")}'" : "";
			$until = ! empty($this->ToDate) ? ",until:'{$this->ToDate->format("Y-m-d")}'" : "";

			if ($isOpen)
			{
				$returnValue = sprintf("%s%s{start:'00:00',end:'%s',recurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $split[0], $weekdays, $from, $until);
				$returnValue = sprintf("%s%s{start:'%s',end:'23:59',recurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $split[1], $weekdays, $from, $until);
			}
			else
			{
				$start = $split[0] == $split[1] ? "" : sprintf("start:'{%s}',", $split[0]);
				$end = $split[0] == $split[1] ? "" : sprintf("end:'{%s}',", $split[1]);
				$returnValue = sprintf("%s%s{%s%srecurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $start, $end, $weekdays, $from, $until);
			}

		}

		return $returnValue;
	}

	public function __toString(): string
	{
		$returnValue = "";
		if ( ! empty($this->OpenTimeSettingPeriodDays))
		{
			$returnValue = "{$returnValue}{$this->RenderRecurring($this->OpenTimeSettingPeriodDays, true)}";
		}

		if ( ! empty($this->ClosedTimeSettingPeriodDays))
		{
			$returnValue = "{$returnValue}{$this->RenderRecurring($this->ClosedTimeSettingPeriodDays, false)}";
		}

		if (empty($this->ClosedTimeSettingPeriodDays) && empty($this->OpenTimeSettingPeriodDays))
		{
			$returnValue = "{$returnValue}{$this->RenderBusinessHoliday()}";
		}

		return $returnValue;
	}



	//public function Render()
	//{
//		$hourSets = array();
//		foreach ($this->timeSettingPeriod->TimeSettingPeriodDays as $day)
//		{
//			$hourSets["{$day->FromTime};{$day->ToTime}"][] = $day;
//		}
//
//		$returnValue = "";
//		foreach ($hourSets as $key => $days)
//		{
//			$weekdays = "";
//			foreach ($days as $day)
//			{
//				/** @var TimeSettingPeriodDayModel $day */
//				$offset = $day->Day - 1;
//				$weekdays .= (empty($weekdays) ? "" : ",") . substr(strtoupper(date('D', strtotime("Monday + {$offset} Days"))), 0, 2);
//			}
//			$split = preg_split("/;/", $key);
//			$from = ! empty($this->timeSettingPeriod->FromDate) ? ", from: '{$this->timeSettingPeriod->FromDate->format("Y-m-d")}'" : "";
//			$until = ! empty($this->timeSettingPeriod->ToDate) ? ", until: '{$this->timeSettingPeriod->ToDate->format("Y-m-d")}'," : "";
//			$returnValue .= "{start: '{$split[0]}',end: '$split[1]',recurring: { repeat: 'weekly', weekDays: '{$weekdays}' {$from} {$until}},";
//		}
//
//		return $returnValue;
	//}
}
