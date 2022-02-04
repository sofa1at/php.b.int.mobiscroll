<?php

namespace Sofa1\Mobiscroll;

use Exception;
use Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel;
use Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection;

interface IMobiscrollStringConverterService
{
	/**
	 * @param StationDateElementCollection $stationDateElementCollectionInvalid
	 * @param StationDateElementCollection $stationDateElementCollectionValid
	 *
	 * @return string
	 * @throws Exception
	 */
	public function DateTimeValidationToString(StationDateElementCollection $stationDateElementCollectionInvalid, StationDateElementCollection $stationDateElementCollectionValid): string;

	/**
	 * @param DateLabelElementModel $label
	 *
	 * @return string
	 */
	public function DateLabelElementToString(DateLabelElementModel $label): string;
}
