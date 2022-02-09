<?php


namespace Sofa1\Mobiscroll\v5;


use DateTime;
use Exception;
use Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto;
use Sofa1\Core\Api\Dto\DateTime\StationBusinessHolidayDto;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel;
use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel;
use Sofa1\Core\StationDateTimeService\Elements\DateTimeRangeElementModel;
use Sofa1\Core\StationDateTimeService\Models\TimeSettingModel;
use Sofa1\Core\Utilities\WeekDayUtilities;

class Mobiscroll5Service
{
	/** @var  TimeSettingPeriodModel */
	protected $businessHours;
	/** @var TimeSettingPeriodModel[] */
	protected $timeSettingPeriods;
	/** @var DateTimeRangeElementModel[] */
	protected $businessHolidays;

	/** @var MobiscrollBusinessHours[] */
	protected $invalidObjects;


	/**
	 * Checks the given businessHours and creates timeSettingsPeriods
	 *
	 * @param BusinessHoursDto[] $businessHours
	 *
	 * @throws Exception
	 */
	public function AddBusinessHours(array $businessHours): void
	{
		$invalidObject = new MobiscrollBusinessHours();
		$invalidObject->AddBusinessHours($businessHours);
		$this->invalidObjects[] = $invalidObject;
//		$timeSettingPeriod = new TimeSettingPeriodModel();
//		foreach ($businessHours as $businessHour)
//		{
//			$timeSettingPeriodDay = new TimeSettingPeriodDayModel();
//			$timeSettingPeriodDay->Day = WeekDayUtilities::GetWeekdayNumberFromString($businessHour->Day, "Monday") + 1;
//			$timeSettingPeriodDay->FromTime = $businessHour->From;
//			$timeSettingPeriodDay->ToTime = $businessHour->To;
//			$timeSettingPeriod->TimeSettingPeriodDays[] = $timeSettingPeriodDay;
//		}
//		$this->businessHours = $timeSettingPeriod;
	}

	/**
	 * Adds the given time settings to this timeSettings
	 *
	 * @param TimeSettingModel $timeSettings
	 */
	public function AddTimeSettings(TimeSettingModel $timeSettings): void
	{
		foreach ($timeSettings->TimeSettingPeriods as $timeSettingPeriod){
			$invalidObject = new MobiscrollBusinessHours();
			$invalidObject->AddTimeSettingPeriod($timeSettingPeriod, true);
			$this->invalidObjects[] = $invalidObject;
		}

//		if (empty($this->timeSettingPeriods))
//		{
//			$this->timeSettingPeriods = $timeSettings->TimeSettingPeriods;
//
//			return;
//		}
//		$this->timeSettingPeriods = array_merge($this->timeSettingPeriods, $timeSettings->TimeSettingPeriods);
	}

	/**
	 * @param StationBusinessHolidayDto $businessHolidayDto
	 */
	public function AddBusinessHolidays(StationBusinessHolidayDto $businessHolidayDto): void
	{
		$invalidObject = new MobiscrollBusinessHours();
		$invalidObject->AddBusinessHoliday($businessHolidayDto);
		$this->invalidObjects[] = $invalidObject;
		//$this->businessHolidays[] = $businessHolidayDto;
	}

	/** @var MobiscrollBusinessHours[] $businessHours */
	private array $tempTimeSettingPeriods;

	public function Render(): string
	{
		$returnValue = "";

		foreach ($this->invalidObjects as $invalidObject){
			$this->RendererAddTimeSettingPeriod($invalidObject);
		}

		foreach ($this->tempTimeSettingPeriods as $invalidObject){
			$returnValue .= (empty($returnValue) ? "" : ",") . (string)$invalidObject;
		}

		return $returnValue;
	}

	/**
	 * @param $newTimeSettingPeriod MobiscrollBusinessHours
	 */
	private function RendererAddTimeSettingPeriod(MobiscrollBusinessHours $newTimeSettingPeriod)
	{
		if (empty($this->tempTimeSettingPeriods))
		{
			$this->tempTimeSettingPeriods[] = $newTimeSettingPeriod;

			return;
		}

		foreach ($this->tempTimeSettingPeriods as $existingTimeSettingPeriod)
		{
			$existingFromDate = empty($existingTimeSettingPeriod->FromDate) ? DateTime::createFromFormat("Y-m-d", "1970-01-01") : clone $existingTimeSettingPeriod->FromDate;
			$existingToDate = empty($existingTimeSettingPeriod->ToDate) ? DateTime::createFromFormat("Y-m-d", "3000-01-01") : clone $existingTimeSettingPeriod->ToDate;
			if ($existingFromDate <= $newTimeSettingPeriod->FromDate && $existingToDate >= $newTimeSettingPeriod->ToDate)
			{
				$preTimeSettingPeriod = clone $existingTimeSettingPeriod;

				// here comes an collision and we have to split
				//$preTimeSettingPeriod = clone $existingTimeSettingPeriod;
				$preTimeSettingPeriod->ToDate = clone $newTimeSettingPeriod->FromDate;
				$preTimeSettingPeriod->ToDate->sub(new \DateInterval("P1D"));
				$this->tempTimeSettingPeriods[] = $preTimeSettingPeriod;

				// set end of existing timesetting period
				$existingTimeSettingPeriod->FromDate = clone $newTimeSettingPeriod->ToDate;
				$existingTimeSettingPeriod->FromDate->add(new \DateInterval("P1D"));

				// add the new one
				$this->tempTimeSettingPeriods[] = $newTimeSettingPeriod;
			}
		}
	}
}
