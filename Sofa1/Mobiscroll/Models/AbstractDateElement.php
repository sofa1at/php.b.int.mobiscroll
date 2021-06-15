<?php


namespace Sofa1\Mobiscroll\Models;


abstract class AbstractDateElement
{
	abstract function ToString();

	/**
	 * @var string
	 */
	public $method;

    /**
     * @var \DateTime
     */
    public $date;
}
