<?php


namespace Sofa1\Mobiscroll\v5;


use DateTime;
use Exception;
use Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto;
use Sofa1\Core\Api\Dto\DateTime\StationBusinessHolidayDto;
use Sofa1\Core\StationDateTimeService\Models\TimeSettingModel;
use Sofa1\Core\Utilities\WeekDayUtilities;

class Mobiscroll5Service
{
	/**
	 * The regular business hours
	 * @var MobiscrollBusinessHours[]|null
	 */
	protected ?array $businessHours = null;

	/**
	 * The business hours for pickup option
	 * @var MobiscrollBusinessHours[]|null
	 */
	protected ?array $pickupBusinessHours = null;

	/**
	 * The business hours for delivery option
	 * @var MobiscrollBusinessHours[]|null
	 */
	protected ?array $deliveryBusinessHours = null;

	/**
	 * The special opening times
	 * @var MobiscrollBusinessHours[]|null
	 */
	protected ?array $timeSettings = null;

	/**
	 * The holidays
	 * @var MobiscrollBusinessHours[]|null
	 */
	protected ?array $businessHolidays = null;

	/**
	 * Adds BusinessHours
	 *
	 * @param BusinessHoursDto[] $businessHours
	 *
	 * @throws Exception
	 */
	public function AddBusinessHours(array $businessHours): void
	{
		$invalidObject = new MobiscrollBusinessHours();
		$invalidObject->AddBusinessHours($businessHours);
		$this->businessHours[] = $invalidObject;
	}

	/**
	 * Adds BusinessHours for pickup
	 *
	 * @param TimeSettingModel $timeSettings
	 *
	 */
	public function AddPickupBusinessHours(TimeSettingModel $timeSettings): void
	{
		foreach ($timeSettings->TimeSettingPeriods as $timeSettingPeriod)
		{
			$invalidObject = new MobiscrollBusinessHours();
			$invalidObject->AddTimeSettingPeriod($timeSettingPeriod, true);
			$this->pickupBusinessHours[] = $invalidObject;
		}
	}

	/**
	 * Adds BusinessHours for delivery
	 *
	 * @param TimeSettingModel $timeSettings
	 *
	 */
	public function AddDeliveryBusinessHours(TimeSettingModel $timeSettings): void
	{
		foreach ($timeSettings->TimeSettingPeriods as $timeSettingPeriod)
		{
			$invalidObject = new MobiscrollBusinessHours();
			$invalidObject->AddTimeSettingPeriod($timeSettingPeriod, true);
			$this->deliveryBusinessHours[] = $invalidObject;
		}
	}

	/**
	 * Adds the given time settings to this timeSettings
	 *
	 * @param TimeSettingModel $timeSettings
	 */
	public function AddSpecialOpeningHours(TimeSettingModel $timeSettings): void
	{
		foreach ($timeSettings->TimeSettingPeriods as $timeSettingPeriod)
		{
			$invalidObject = new MobiscrollBusinessHours();
			$invalidObject->AddTimeSettingPeriod($timeSettingPeriod, true);
			$this->timeSettings[] = $invalidObject;
		}
	}

	/**
	 * @param StationBusinessHolidayDto $businessHolidayDto
	 */
	public function AddBusinessHolidays(StationBusinessHolidayDto $businessHolidayDto): void
	{
		$invalidObject = new MobiscrollBusinessHours();
		$invalidObject->AddBusinessHoliday($businessHolidayDto);
		$this->businessHolidays[] = $invalidObject;
	}

	/**
	 * Turns the regular business hours (+ special hours, + holidays) into invalid objects
	 * @return string
	 */
	public function RenderBusinessHours(): string
	{
		$returnValue = "";
		$bhs = array();

		// regular business hours
		foreach ($this->businessHours as $invalidObject)
		{
			$this->MergeBusinessHours($bhs, $invalidObject);
		}

		// add custom time settings
		foreach ($this->timeSettings as $invalidObject)
		{
			$this->MergeBusinessHours($bhs, $invalidObject);
		}

		// add holidays
		foreach ($this->businessHolidays as $invalidObject)
		{
			$this->MergeBusinessHours($bhs, $invalidObject);
		}

		// render the invalid objects
		foreach ($bhs as $invalidObject)
		{
			$returnValue .= (empty($returnValue) ? "" : ",") . (string) $invalidObject;
		}

		return $returnValue;
	}

	/**
	 * Turns the delivery business hours (+ holidays) into invalid objects
	 *
	 * @return string
	 */
	public function RenderPickupBusinessHours(): string
	{
		return $this->RenderExclusiveBusinessHours($this->pickupBusinessHours);
	}

	/**
	 * Turns the delivery business hours (+ holidays) into invalid objects
	 *
	 * @return string
	 */
	public function RenderDeliveryBusinessHours(): string
	{
		return $this->RenderExclusiveBusinessHours($this->deliveryBusinessHours);
	}

	/**
	 * Turns the pickup or delivery business hours (+ holidays) into invalid objects
	 *
	 * @param array|null $mobiscrollBusinessHours
	 *
	 * @return string
	 */
	private function RenderExclusiveBusinessHours(array $mobiscrollBusinessHours = null): string
	{
		$returnValue = "";
		$bhs = array();

		if ( ! empty($mobiscrollBusinessHours))
		{
			// pickup business hours
			foreach ($mobiscrollBusinessHours as $invalidObject)
			{
				$this->MergeBusinessHours($bhs, $this->GetClosedAll());
				$this->MergeBusinessHours($bhs, $invalidObject);
			}
		}
		else if ($this->businessHours)
		{
			// regular business hours
			foreach ($this->businessHours as $invalidObject)
			{
				$this->MergeBusinessHours($bhs, $invalidObject);
			}
		}

		// add holidays
		if ($this->businessHolidays)
		{
			foreach ($this->businessHolidays as $invalidObject)
			{
				$this->MergeBusinessHours($bhs, $invalidObject);
			}
		}

		// render the invalid objects
		if ( ! empty($bhs))
		{
			foreach ($bhs as $invalidObject)
			{
				$returnValue .= (empty($returnValue) ? "" : ",") . (string) $invalidObject;
			}
		}

		return $returnValue;
	}

	/**
	 *
	 * @param MobiscrollBusinessHours[] $actual
	 * @param $new MobiscrollBusinessHours
	 *
	 * @return void
	 */
	private function MergeBusinessHours(array &$actual, MobiscrollBusinessHours $new): void
	{
		if (empty($actual))
		{
			$actual = [$new];

			return;
		}

		foreach ($actual as $existingTimeSettingPeriod)
		{
			$existingFromDate = empty($existingTimeSettingPeriod->FromDate) ? DateTime::createFromFormat("Y-m-d", "1970-01-01") : clone $existingTimeSettingPeriod->FromDate;
			$existingToDate = empty($existingTimeSettingPeriod->ToDate) ? DateTime::createFromFormat("Y-m-d", "3000-01-01") : clone $existingTimeSettingPeriod->ToDate;
			if ($existingFromDate <= $new->FromDate && $existingToDate >= $new->ToDate)
			{
				$preTimeSettingPeriod = clone $existingTimeSettingPeriod;

				// here comes an collision and we have to split
				//$preTimeSettingPeriod = clone $existingTimeSettingPeriod;
				$preTimeSettingPeriod->ToDate = clone $new->FromDate;
				$preTimeSettingPeriod->ToDate->sub(new \DateInterval("P1D"));
				$actual[] = $preTimeSettingPeriod;

				// set end of existing timesetting period
				$existingTimeSettingPeriod->FromDate = clone $new->ToDate;
				$existingTimeSettingPeriod->FromDate->add(new \DateInterval("P1D"));

				// add the new one
				$actual[] = $new;
			}
		}
	}

	/**
	 * @return MobiscrollBusinessHours
	 */
	private function GetClosedAll(): MobiscrollBusinessHours
	{
		$bhs = array();
		foreach (WeekDayUtilities::$weekdays as $weekday)
		{
			$bh = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
			$bh->Day = ucwords($weekday);
			$bh->IsOpen = false;
			$bhs[] = $bh;
		}
		$businessHours = new MobiscrollBusinessHours();
		$businessHours->AddBusinessHours($bhs);

		return $businessHours;
	}
}
