<?php

require_once 'valid-mock-data.php';

use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

class StringConverterServiceTest extends \PHPUnit\Framework\TestCase
{

	public function testDateElementToString()
	{
		$converter = new Sofa1\Mobiscroll\MobiscrollStringConverterService();
		$element = '{"method":"specific_day","date":{"date":"2020-11-20 00:00:00.000000","timezone_type":3,"timezone":"UTC"}}';
		$element = json_decode($element);
		$element->date = new DateTime($element->date->date);
		$element = new \Sofa1\Core\StationDateTimeService\Elements\DateElementModel($element->date, $element->method);

		$this->assertEquals(
			"'2020-11-20'",
			$converter->DateElementToString($element)
		);
	}

	public function testDateTimeRangeElementToString()
	{
		$converter = new Sofa1\Mobiscroll\MobiscrollStringConverterService();
		$element = '{"fromTime":"10:00","toTime":"22:00","method":null,"date":{"date":"2020-11-20 00:00:00.000000","timezone_type":3,"timezone":"UTC"}}';
		$element = json_decode($element);
		$element->date = new DateTime($element->date->date);
		$element = new \Sofa1\Core\StationDateTimeService\Elements\DateTimeRangeElementModel($element->date, $element->fromTime, $element->toTime);

		$this->assertEquals(
			"{d: new Date(2020,10,20), start:'10:00', end:'22:00' }",
			$converter->DateTimeRangeElementToString($element)
		);
	}

	public function testWeekdayTimeRangeElementToString()
	{
		$converter = new Sofa1\Mobiscroll\MobiscrollStringConverterService();
		$element = new \Sofa1\Core\StationDateTimeService\Elements\WeekdayTimeRangeElementModel(0, "00:00", "23:59", 'every_week_day_time_range');

		$this->assertEquals(
			"{d: 'w0', start:'00:00', end:'23:59'}",
			$converter->WeekdayTimeRangeElementToString($element)
		);
	}

	public function testWeekDayElementToString()
	{
		$converter = new Sofa1\Mobiscroll\MobiscrollStringConverterService();
		$element = '{"method":"every_week_day","weekday":0,"date":null}';
		$element = json_decode($element);
		$element = new \Sofa1\Core\StationDateTimeService\Elements\WeekDayElementModel($element->weekday, $element->method);

		$this->assertEquals(
			"'w0'",
			$converter->WeekDayElementToString($element)
		);
	}

	public function testDateLabelElementToString()
	{
		$converter = new Sofa1\Mobiscroll\MobiscrollStringConverterService();
		$mock = new ValidMockData();
		$businessHoliday = $mock->BusinessHolidays();
		$lable = new Sofa1\Core\StationDateTimeService\Elements\DateLabelElementModel($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		$this->assertEquals(
			"{start: new Date(2020,10,16), end: new Date(2020,10,19), text: 'Urlaub'}",
			$converter->DateLabelElementToString($lable)
		);
	}
}
