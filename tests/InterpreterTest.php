<?php

require_once 'valid-mock-data.php';

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

    public function testGetLabels() {
	    $mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
	    $mock = new ValidMockData();
	    $businessHoliday = $mock->BusinessHolidays();
	    $mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

	    $this->assertEquals(
	    	"{start: new Date(2020,10,16), end: new Date(2020,10,19), text: 'Urlaub'}",
		    $mobiHelper->GetLabels()
	    );
    }
}
