<?php


namespace Sofa1\Mobiscroll;

use Exception;
use Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel;
use Sofa1\Core\StationDateTimeService\Helpers\StationDateElementCollection;

class Mobiscroll5StringConverterService implements IMobiscrollStringConverterService
{
	public function DateTimeValidationToString(StationDateElementCollection $stationDateElementCollectionInvalid, StationDateElementCollection $stationDateElementCollectionValid): string
	{
		foreach ($stationDateElementCollectionValid as $valid){
			var_dump($valid);
		}
	}

	public function DateLabelElementToString(DateLabelElementModel $label): string
	{
		// TODO: Implement DateLabelElementToString() method.
	}
}
