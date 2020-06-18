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
}
