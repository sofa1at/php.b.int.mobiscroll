<?php


namespace Sofa1\Mobiscroll;


class AbstractBusinessHours
{
	/**
	 * @var int
	 */
	public $StationId;

	/**
	 * @var string
	 */
	public $Day;

	/**
	 * @var string
	 */
	public $From;

	/**
	 * @var string
	 */
	public $To;

	/**
	 * @var boolean
	 */
	public $IsOpen;
}
