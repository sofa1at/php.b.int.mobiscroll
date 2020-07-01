<?php


namespace Sofa1\Mobiscroll\Models;


class AbstractStationBusinessHolidayDto
{
	/**
	 * @var int
	 */
	public $Id;
	/**
	 * @var int
	 */
	public $StationId;
	/**
	 * @var \DateTime
	 */
	public $From;
	/**
	 * @var \DateTime
	 */
	public $To;
	/**
	 * @var string
	 */
	public $Text;
}
