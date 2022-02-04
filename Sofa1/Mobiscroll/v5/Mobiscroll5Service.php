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

class Mobiscroll5Service
{
	/** @var  TimeSettingPeriodModel */
	protected $businessHours;
	/** @var TimeSettingPeriodModel[] */
	protected $timeSettingPeriods;
	/** @var DateTimeRangeElementModel[] */
	protected $businessHolidays;

	/**
	 * Checks the given businessHours and creates timeSettingsPeriods
	 *
	 * @param BusinessHoursDto[] $businessHours
	 *
	 * @throws Exception
	 */
	public function AddBusinessHours(array $businessHours): void
	{
		$timeSettingPeriod = new TimeSettingPeriodModel();
		foreach ($businessHours as $businessHour)
		{
			$timeSettingPeriodDay = new TimeSettingPeriodDayModel();
			$timeSettingPeriodDay->Day = $businessHour->Day;
			$timeSettingPeriodDay->FromTime = $businessHour->From;
			$timeSettingPeriodDay->ToTime = $businessHour->To;
			$timeSettingPeriod->TimeSettingPeriodDays[] = $timeSettingPeriodDay;
		}
		$this->businessHours = $timeSettingPeriod;
	}

	/**
	 * Adds the given time settings to this timeSettings
	 *
	 * @param TimeSettingModel $timeSettings
	 */
	public function AddTimeSettings(TimeSettingModel $timeSettings): void
	{
		if (empty($this->timeSettingPeriods))
		{
			$this->timeSettingPeriods = $timeSettings->TimeSettingPeriods;

			return;
		}
		$this->timeSettingPeriods = array_merge($this->timeSettingPeriods, $timeSettings->TimeSettingPeriods);
	}

	/**
	 * @param StationBusinessHolidayDto $businessHolidayDto
	 */
	public function AddBusinessHolidays(StationBusinessHolidayDto $businessHolidayDto): void
	{
		$this->businessHolidays[] = $businessHolidayDto;
	}

	/** @var MobiscrollBusinessHours[] $businessHours */
	private array $tempTimeSettingPeriods;

	public function Render()
	{
		$this->RendererAddTimeSettingPeriod(new MobiscrollBusinessHours($this->businessHours));
		foreach ($this->timeSettingPeriods as $timeSettingPeriod)
		{
			$this->RendererAddTimeSettingPeriod(new MobiscrollBusinessHours($timeSettingPeriod));
		}

		$returnValue = "";
		foreach ($this->tempTimeSettingPeriods as $timeSettingPeriod){
			$returnValue .= $timeSettingPeriod->Render();
		}
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
			$existingFromDate = empty($existingTimeSettingPeriod->FromDate) ? DateTime::createFromFormat("Y-m-d", "1970-01-01") : $existingTimeSettingPeriod->FromDate;
			$existingToDate = empty($existingTimeSettingPeriod->ToDate) ? DateTime::createFromFormat("Y-m-d", "3000-01-01") : $existingTimeSettingPeriod->ToDate;
			if ($existingFromDate <= $newTimeSettingPeriod->timeSettingPeriod->FromDate && $existingToDate >= $newTimeSettingPeriod->timeSettingPeriod->ToDate)
			{
				// here comes an collision and we have to split
				$preTimeSettingPeriod = clone $existingTimeSettingPeriod;
				$preTimeSettingPeriod->timeSettingPeriod->ToDate = $newTimeSettingPeriod->timeSettingPeriod->FromDate;
				$this->tempTimeSettingPeriods[] = $preTimeSettingPeriod;

				$afterTimeSettingPeriod = $existingTimeSettingPeriod;
				$afterTimeSettingPeriod->timeSettingPeriod->FromDate = $newTimeSettingPeriod->timeSettingPeriod->ToDate;
				$this->tempTimeSettingPeriods[] = $newTimeSettingPeriod;
			}
		}
	}
}
