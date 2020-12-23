<?php


namespace Sofa1\Mobiscroll\Models;


use Sofa1\Mobiscroll\Helpers\DateTimeHelper;

class AbstractTimeSetting
{
    /**
     * @var string|null
     */
    public $Id;

    /**
     * @var int
     */
    public $StationId;

    /**
     * @var string
     */
    public $Name;

    /**
     * @var string|null
     */
    public $Description;

    /**
     * @var \Sofa1\Mobiscroll\Models\AbstractTimeSettingPeriod[]|null
     */
    public $TimeSettingPeriods;

    /**
     * @param $date \DateTime
     *
     * @return bool
     */
    function IsDateInRange($date)
    {
        foreach ($this->TimeSettingPeriods as $timeSettingPeriod)
        {
            $fromDate = $timeSettingPeriod->FromDate->format("md");
            $toDate = $timeSettingPeriod->ToDate->format("md");
            if ($date->format("md") < $fromDate || $date->format("md") > $toDate)
            {
                continue;
            }

            if (empty($timeSettingPeriod->TimeSettingPeriodDays))
            {
                return true;
            }

            foreach ($timeSettingPeriod->TimeSettingPeriodDays as $periodDay)
            {
                $dayofweek = $date->format('l');
                $hour = $date->format('His');

                if ((is_null($periodDay->Day) ? true : $periodDay->Day == DateTimeHelper::GetWeekdayNumberFromString($dayofweek, 'sunday')) && $hour >= $periodDay->FromTime->format("His") && $periodDay->ToTime->format("His") >= $hour)
                {
                    return true;
                }
            }
        }

        return false;
    }

}
