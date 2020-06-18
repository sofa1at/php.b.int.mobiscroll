<?php


namespace Sofa1\Mobiscroll;


class DateTimeValidation
{
	/**
	 * @var DateElementCollection
	 */
	public $Valid;

	/**
	 * @var DateElementCollection
	 */
	public $Invalid;

	public function __construct()
	{
		$this->Valid = new DateElementCollection();
		$this->Invalid = new DateElementCollection();
	}

	public function ToString()
	{
		return sprintf("invalid: [%s], valid: [%s]", $this->Invalid->ToString(), $this->Valid->ToString());
	}
}
