<?php

require_once 'valid-mock-data.php';

use Sofa1\Core\StationDateTimeService\Models\TimeSettingModel;
use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

final class InterpreterTest extends PHPUnit\Framework\TestCase
{
	public function testTimeSettingsAndBusinessHolidays()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$businessHoliday = $mock->BusinessHolidays();
		$mobiHelper->AddTimeSettings($mock->PickupTimeSettings());
		$mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		$this->assertEquals(
			$mock->resultStringTimeSettingsAndBusinessHolidays(),
			$mobiHelper->ToString()
		);
	}

	public function testTimeSettings()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$mobiHelper->AddTimeSettings($mock->PickupTimeSettings());

		$this->assertEquals(
			$mock->resultStringTimeSettings(),
			$mobiHelper->ToString()
		);
	}

	public function testBusinessHours()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$mobiHelper->AddBusinessHours($mock->BusinessHours());

		$this->assertEquals(
			$mock->resultStringBusinessHours(),
			$mobiHelper->ToString()
		);
	}

	public function testBusinessHoursAndBusinessHolidays()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$mobiHelper->AddBusinessHours($mock->BusinessHours());
		$businessHoliday = $mock->BusinessHolidays();
		$mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		$this->assertEquals(
			$mock->resultStringBusinessHoursAndBusinessHolidays(),
			$mobiHelper->ToString()
		);
	}

	public function testGetLabels()
	{
		$mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
		$mock = new ValidMockData();
		$businessHoliday = $mock->BusinessHolidays();
		$mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

		$this->assertEquals(
			"{start: new Date(2020,10,16), end: new Date(2020,10,19), text: 'Urlaub'}",
			$mobiHelper->GetLabels()
		);
	}

	public function testVersion5(){
		$businessHour = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
		$businessHour->Day = 1;
		$businessHour->From = "08:00";
		$businessHour->To = "20:00";
		$businessHour->IsOpen = true;

		$mobiScrollConverter = new \Sofa1\Mobiscroll\v5\Mobiscroll5Service();
		$mobiScrollConverter->AddBusinessHours([$businessHour]);

		// soh = special opening hours
		$soh = new TimeSettingModel();
		$sohp1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel();
		$sohp1->FromDate = DateTime::createFromFormat("Y-m-d", "2022-01-01");
		$sohp1->ToDate = DateTime::createFromFormat("Y-m-d", "2022-02-01");
		$sohp1d1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel();
		$sohp1d1->Day = 1;
		$sohp1d1->FromTime = "09:00";
		$sohp1d1->ToTime = "17:00";
		$sohp1->TimeSettingPeriodDays[] = $sohp1d1;
		$soh->TimeSettingPeriods[] = $sohp1;

		$mobiScrollConverter->AddTimeSettings($soh);

		$mobiScrollConverter->Render();
	}
}
