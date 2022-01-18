<?php


namespace Sofa1\Mobiscroll;

use Exception;

class Sofa1MobiscrollConverter extends \Sofa1\Core\StationDateTimeService\StationDateTimeService
{
	/** @var MobiscrollStringConverterService */
	private $_stringConverter;
	/**
	 * Sofa1MobiscrollConverter constructor.
	 *
	 * @param int $max
	 * @param null $startDate
	 *
	 * @throws Exception
	 */
	public function __construct($max = 365, $startDate = null)
	{
		parent::__construct($max, $startDate);
		$this->_stringConverter = new  MobiscrollStringConverterService();
	}

	/**
	//     * @return string
	 * @throws Exception
	 */
	public function ToString()
	{
		$this->validation = new \Sofa1\Core\StationDateTimeService\Helpers\StationDateTimeValidation();
		$this->ValidateTimeSettings();
		return $this->_stringConverter->DateTimeValidationToString($this->validation->Invalid, $this->validation->Valid);
	}

	/**
	 * Converts this labels to an string
	 *
	 * @return string
	 */
	public function GetLabels()
	{
		$returnValue = array();
		if ( ! empty($this->labels))
		{
			foreach ($this->labels as $label)
			{
				$returnValue[] = $this->_stringConverter->DateLabelElementToString($label);
			}
		}

		return implode($returnValue);
	}
}
