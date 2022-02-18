<?php


namespace Sofa1\Mobiscroll\v5;

use Sofa1\Core\Api\Dto\DateTime\StationBusinessHolidayDto;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel;

class MobiscrollBusinessHours
{

	public ?\DateTime $FromDate = null;

	public ?\DateTime $ToDate = null;

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
	 * @throws \Exception
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
		$invertWeekdays = ["MO", "TU", "WE", "TH", "FR", "SA", "SU"];
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
				$shortWeekday = $this->GetWeekdayShort($day->Day);
				$weekdays .= $shortWeekday;
				$searchIndex = array_search(strtoupper($shortWeekday), $invertWeekdays);
				if ($searchIndex !== false)
				{
					unset($invertWeekdays[$searchIndex]);
				}
			}

			$split = preg_split("/;/", $key);


			if ($isOpen)
			{
				$from = ! empty($this->FromDate) ? ",from:'{$this->FromDate->format("Y-m-d")}'" : "";
				$until = ! empty($this->ToDate) ? ",until:'{$this->ToDate->format("Y-m-d")}'" : "";
				$returnValue = sprintf("%s%s{start:'00:00',end:'%s',recurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $split[0], $weekdays, $from, $until);
				$returnValue = sprintf("%s%s{start:'%s',end:'23:59',recurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $split[1], $weekdays, $from, $until);
			}
			else
			{
				$fromDate = $this->FromDate != null ? clone $this->FromDate : null;
				$toDate = $this->ToDate != null ? clone $this->ToDate : null;
				$from = ! empty($fromDate) ? ",from:'{$fromDate->sub(new \DateInterval("P1D"))->format("Y-m-d")}'" : "";
				$until = ! empty($toDate) ? ",until:'{$toDate->add(new \DateInterval("P1D"))->format("Y-m-d")}'" : "";
				$start = $split[0] == $split[1] ? "" : sprintf("start:'{%s}',", $split[0]);
				$end = $split[0] == $split[1] ? "" : sprintf("end:'{%s}',", $split[1]);
				$returnValue = sprintf("%s%s{%s%srecurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", $start, $end, $weekdays, $from, $until);
			}
		}

		if ( ! empty($invertWeekdays))
		{
			$returnValue = sprintf("%s%s{recurring:{repeat:'weekly',weekDays:'%s'%s%s}}", $returnValue, ! empty($returnValue) ? "," : "", implode(",", $invertWeekdays), $from, $until);
		}

		return $returnValue;
	}


	/**
	 * @throws \Exception
	 */
	private function GetWeekdayShort($weekday): string
	{
		switch (strtolower($weekday))
		{
			case 1:
			case "monday":
				return "MO";
			case 2:
			case "tuesday":
				return "TU";
			case 3:
			case "wednesday":
				return "WE";
			case 4:
			case "thursday":
				return "TH";
			case 5:
			case "friday":
				return "FR";
			case 6:
			case "saturday":
				return "SA";
			case 7:
			case "sunday":
				return "SU";
		}
		throw  new \Exception("key {$weekday} not found");
	}
}
