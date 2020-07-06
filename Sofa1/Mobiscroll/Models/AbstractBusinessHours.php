<?php


namespace Sofa1\Mobiscroll\Models;


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

    /**
     * @var string
     */
	public $InfoText;
}
