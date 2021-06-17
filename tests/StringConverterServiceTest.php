<?php

require_once 'valid-mock-data.php';

use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

class StringConverterServiceTest extends \PHPUnit\Framework\TestCase
{

	public function testDateElementToString()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$businessHoliday = $mock->BusinessHolidays();
		$mobiHelper->AddTimeSettings($mock->PickupTimeSettings());
		$mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		var_dump(json_encode($mobiHelper->GetLabels()));
	}

	public function testDateTimeRangeElementToString()
	{

	}

	public function testWeekdayTimeRangeElementToString()
	{

	}

	public function testWeekDayElementToString()
	{

	}

	public function testDateLabelElementToString()
	{
		$converter = new Sofa1\Mobiscroll\StringConverterService();
		$mock = new ValidMockData();
		$businessHoliday = $mock->BusinessHolidays();
		$lable = new Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		$this->assertEquals(
			"{start: new Date(2020,10,16), end: new Date(2020,10,19), text: 'Urlaub'}",
			$converter->DateLabelElementToString($lable)
		);
	}
}
