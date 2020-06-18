<?php


namespace Sofa1\Mobiscroll\Models;


class AbstractTimeSettingPeriodDay
{
	/**
	 * @var string
	 */
	public $TimeSettingPeriodId;

	/**
	 * @var string|null
	 */
	public $Day;

	/**
	 * @var \DateTime
	 */
	public $FromTime;

	/**
	 * @var \DateTime
	 */
	public $ToTime;
}
