<?php


namespace Sofa1\Mobiscroll;

use App\TimeSettings\TimeSetting;
use Cassandra\Date;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use Sofa1\Mobiscroll\Helpers\DateTimeHelper;
use Sofa1\Mobiscroll\Models\AbstractBusinessHours;
use Sofa1\Mobiscroll\Models\AbstractStationBusinessHolidayDto;
use Sofa1\Mobiscroll\Models\AbstractTimeSetting;
use Sofa1\Mobiscroll\Models\AbstractTimeSettingPeriod;
use Sofa1\Mobiscroll\Models\AbstractTimeSettingPeriodDay;
use Sofa1\Mobiscroll\Models\DateTimeRangeElement;
use Sofa1\Mobiscroll\Models\WeekDayElement;

class Sofa1MobiscrollConverter
{

    /**
     * @var DateTimeValidation
     */
    private $validation;

    /**
     * @var AbstractTimeSetting[]
     */
    private $timeSettings;

    /**
     * @var DateTimeRangeElement[]
     */
    private $businessHolidays;

    /**
     * @var int $max
     */
    private $max;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var \JsonMapper
     */
    private $mapper;

    public function __construct($max = 365, $startDate = null)
    {
        $this->mapper = new \JsonMapper();
        $this->timeSettings = array();
        $this->max = $max;
        if ($startDate == null) {
            $this->startDate = new DateTime('now', new \DateTimeZone('UTC'));
        }
    }

    /**
     * @param string $json
     * @throws Exception
     */
    public function AddBusinessHoursJson($json)
    {
        $businessHours = array();
        $json = is_object($json) ? $json : json_decode($json);
        $this->mapper->mapArray($json, $businessHours, "Sofa1\Mobiscroll\Models\AbstractBusinessHours");
	    $this->AddBusinessHours($businessHours);
    }

    /**
     * @param AbstractBusinessHours[] $businessHours
     */
    public function AddBusinessHours($businessHours)
    {
        // add time settings from today to max days
        $timeSetting = new AbstractTimeSetting();
        $period = new AbstractTimeSettingPeriod();
        $period->FromDate = clone $this->startDate;
        $lastDay = clone $this->startDate;
        $lastDay->add(new DateInterval('P' . $this->max . "D"));
        $period->ToDate = $lastDay;
        $currentDay = clone $period->FromDate;
        $timeSetting->TimeSettingPeriods[] = $period;

        $bh = array();
        foreach ($businessHours as $businessHour) {
            if ($businessHour->IsOpen) {
                $bh[strtolower($businessHour->Day)][] = $businessHour;
            }
        }

        // add weekday periods
        for ($i = 0; $i <= 6; $i++) {
            $currentWeekday = $currentDay->format("l");
            if (!empty($bh[strtolower($currentWeekday)])) {
                /** @var AbstractBusinessHours $item */
                foreach ($bh[strtolower($currentWeekday)] as $item) {
                    $tsPeriod = new AbstractTimeSettingPeriodDay();
                    $tsPeriod->Day = DateTimeHelper::GetWeekdayNumberFromString($currentWeekday, 'sunday');
                    $tsPeriod->FromTime = $item->From;
                    $tsPeriod->ToTime = $item->To;
                    $period->TimeSettingPeriodDays[] = $tsPeriod;
                }
            }
            $currentDay->add(new DateInterval('P1D'));
        }


        $this->AddTimeSettings($timeSetting);
    }

    /**
     * @param string $json
     * @throws Exception
     */
    public function AddTimeSettingsJson($json)
    {
        $json = is_object($json) ? $json : json_decode($json);
        $timeSettings = $this->mapper->map($json, new AbstractTimeSetting());
        $this->AddTimeSettings($timeSettings);
    }

    /**
     * @param AbstractTimeSetting $timeSettings
     */
    public function AddTimeSettings($timeSettings)
    {
        $this->timeSettings[] = $timeSettings;
    }

    /**
     * @param string $json
     * @throws Exception
     */
    public function AddBusinessHolidaysJson($json)
    {
        $json = is_object($json) ? $json : json_decode($json);
        $businessHolidays = array();
        $this->mapper->mapArray($json, $businessHolidays, "Sofa1\Mobiscroll\Models\AbstractStationBusinessHolidayDto");
        if(!empty($businessHolidays)){
            foreach ($businessHolidays as $businessHoliday){
                /** @var AbstractStationBusinessHolidayDto $businessHoliday */
                $this->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To);
            }
        }
    }

    /**
     * @param DateTime $start
     * @param DateTime $end
     */
    public function AddBusinessHolidays($start, $end)
    {
        if ($start->format("Y-m-d") == $end->format("Y-m-d")) {
            $this->businessHolidays[$start->format("Ymd")][] = new DateTimeRangeElement($start, $start->format("H:i"), $end->format("H:i"));

            return;
        }

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);
        /** @var DateTime $dt */
        foreach ($period as $dt) {
            if ($dt->format("Y-m-d") == $start->format("Y-m-d")) {
                // first element
                $this->businessHolidays[$dt->format("Ymd")][] = new DateTimeRangeElement($dt, $start->format("H:i"), "23:59");
                continue;
            } else if ($dt->format("Y-m-d") == $end->format("Y-m-d")) {
                // last element
                $this->businessHolidays[$dt->format("Ymd")][] = new DateTimeRangeElement($dt, "00:00", $end->format("H:i"));
                continue;
            } else {
                // element between start and end
                $this->businessHolidays[$dt->format("Ymd")][] = new DateTimeRangeElement($dt, "00:00", "23:59");
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function ToString()
    {
        $this->validation = new DateTimeValidation();
        $this->ValidateTimeSettings();

        return $this->validation->ToString();
    }

    private function ValidateTimeSettings()
    {
        if (empty($this->timeSettings)) {
            return;
        }

        for ($i = 0; $i < 7; $i++) {
            $this->validation->Invalid->Add(new WeekDayElement($i, DateOutputMethod::EveryWeekDay));
        }

        foreach ($this->timeSettings as $timeSetting) {
            $date = clone $this->startDate;
            for ($i = 0; $i <= $this->max; $i++) {
                foreach ($timeSetting->TimeSettingPeriods as $timeSettingPeriod) {
                    $fromDate = $timeSettingPeriod->FromDate->format("ymd");
                    $toDate = $timeSettingPeriod->ToDate->format("ymd");
                    if ($date->format("ymd") < $fromDate || $date->format("ymd") > $toDate) {
                        continue;
                    }

                    if (!empty($timeSettingPeriod->TimeSettingPeriodDays)) {
                        foreach ($timeSettingPeriod->TimeSettingPeriodDays as $periodDay) {
                            $dayofweek = $date->format('l');
                            if ((is_null($periodDay->Day) ? true : $periodDay->Day == DateTimeHelper::GetWeekdayNumberFromString($dayofweek, 'sunday'))) {
                                $periodDate = clone $date;
                                $from = is_a($periodDay->FromTime, "\DateTime") ? $periodDay->FromTime->format("H:i") : $periodDay->FromTime;
                                $to = is_a($periodDay->ToTime, "\DateTime") ? $periodDay->ToTime->format("H:i") : $periodDay->ToTime;

                                $this->TryAddValidDateTimeRange($periodDate, $from, $to);
                            }
                        }
                    } else {
                        $this->TryAddValidDateTimeRange(clone $date, "00:00", "23:59");
                    }
                }

                $date->add(new DateInterval('P1D'));
            }
        }
    }

    /**
     * Try to add ValidDate with respecting the businessholidays
     *
     * @param DateTime $date
     * @param string $from
     * @param string $to
     */
    private function TryAddValidDateTimeRange($date, $from, $to)
    {
        $dateTimeRangeElements = $this->ValidateDateRange($date, $from, $to);
        if (!empty($dateTimeRangeElements)) {
            foreach ($dateTimeRangeElements as $dateTimeRangeElement) {
                $this->validation->AddValidDateTimeRange($dateTimeRangeElement->date, $dateTimeRangeElement->fromTime, $dateTimeRangeElement->toTime);
            }
        }
    }

    /**
     * @param DateTime $date
     * @param string $fromTime format [h:i]
     * @param string $toTime format [h:i]
     *
     * @return DateTimeRangeElement[]|void
     */
    private function ValidateDateRange($date, $fromTime, $toTime)
    {
        if (empty($this->businessHolidays) || empty($this->businessHolidays[$date->format("Ymd")])) {
            return [new DateTimeRangeElement($date, $fromTime, $toTime)];
        }

        $fromTimeValue = strtotime($fromTime);
        $toTimeValue = strtotime($toTime);

        $holidays = $this->businessHolidays[$date->format("Ymd")];
        /** @var DateTimeRangeElement $holiday */
        foreach ($holidays as $holiday) {
            $holidayStart = strtotime($holiday->fromTime);
            $holidayEnd = strtotime($holiday->toTime);
            // if between start and end
            if ($holidayStart > $fromTimeValue && $holidayEnd < $toTimeValue) {
                return [
                    new DateTimeRangeElement($date, date("H:i", $fromTimeValue), date("H:i", $holidayStart)),
                    new DateTimeRangeElement($date, date("H:i", $holidayEnd), date("H:i", $toTimeValue))
                ];
            } // if holiday end for endtime
            else if ($holidayStart < $fromTimeValue && $holidayEnd < $toTimeValue) {
                return [new DateTimeRangeElement($date, date("H:i", $holidayEnd), date("H:i", $toTimeValue))];
            } // if holiday starts after fromtime
            else if ($holidayStart > $fromTimeValue && $holidayEnd > $toTimeValue) {
                return [new DateTimeRangeElement($date, date("H:i", $fromTimeValue), date("H:i", $holidayEnd > $toTimeValue ? $holidayStart : $toTimeValue))];
            } // holiday overrides timeframe
            else if ($holidayStart < $fromTimeValue && $holidayEnd > $toTimeValue) {
                return [];
            }
        }

        // no match
        return [];
    }
}
