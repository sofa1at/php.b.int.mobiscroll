<?php


namespace Sofa1\Mobiscroll\Models;


class AbstractTimeSettingPeriod
{
	/**
	 * @var string
	 */
	public $Id;

	/**
	 * @var string
	 */
	public $TimeSettingId;

	/**
	 * @var \DateTime
	 */
	public $FromDate;

	/**
	 * @var \DateTime
	 */
	public $ToDate;

	/**
	 * @var AbstractTimeSettingPeriodDay[]
	 */
	public $TimeSettingPeriodDays;

    /**
     * @param $weekday string Name of weekday
     * @return boolean
     *
     * Checks if the period has a day record for a specific weekday
     */
    public function ContainsSpecificDay($weekday) {
        foreach ($this->TimeSettingPeriodDays as $day) {
            if ($day->Day == $weekday) {
                return true;
            }
        }
        return false;
    }
}
