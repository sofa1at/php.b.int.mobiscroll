<?php


namespace Sofa1\Mobiscroll\v5;

use Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel;

class MobiscrollBusinessHours extends MobiscrollInvalidObject
{
	/** @var TimeSettingPeriodModel */
	public TimeSettingPeriodModel $timeSettingPeriod;

	public function __construct($timeSettingPeriod)
	{
		$this->timeSettingPeriod = $timeSettingPeriod;
	}

	public function Render()
	{
		$hourSets = array();
		foreach ($this->timeSettingPeriod->TimeSettingPeriodDays as $day)
		{
			$hourSets["{$day->FromTime};{$day->ToTime}"][] = $day;
		}

		$returnValue = "";
		foreach ($hourSets as $key => $days)
		{
			$weekdays = "";
			foreach ($days as $day)
			{
				// TODO
				$weekdays = "MO,TU,WE,TH,FR,SA";
			}
			$split = preg_split("/;/", $key);
			$returnValue .= "{start: '{$split[0]}',end: '$split[1]',recurring: { repeat: 'weekly', weekDays: '{$weekdays}', from: '{$this->timeSettingPeriod->FromDate->format("Y-m-d")}', until: '{$this->timeSettingPeriod->ToDate->format("Y-m-d")}' }},";
		}

		return $returnValue;
	}
}
