<?php

require_once 'valid-mock-data.php';

use PHPUnit\Framework\TestCase;
use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

final class InterpreterTest extends TestCase
{
    public function testTimeSettingsAndBusinessHolidays()
    {
        $mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
        $mock = new ValidMockData();
        $businessHoliday = $mock->businessHolidays();
        $mobiHelper->AddTimeSettings($mock->pickupTimeSettings());
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
        $mobiHelper->AddTimeSettings($mock->pickupTimeSettings());

        $this->assertEquals(
            $mock->resultStringTimeSettings(),
            $mobiHelper->ToString()
        );
    }

    public function testBusinessHours()
    {
        $mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
        $mock = new ValidMockData();
        $mobiHelper->AddBusinessHours($mock->businessHours());

        $this->assertEquals(
            $mock->resultStringBusinessHours(),
            $mobiHelper->ToString()
        );
    }

    public function testBusinessHoursAndBusinessHolidays()
    {
        $mobiHelper = new Sofa1MobiscrollConverter(365, new DateTime('2020-11-17'));
        $mock = new ValidMockData();
        $mobiHelper->AddBusinessHours($mock->businessHours());
        $businessHoliday = $mock->businessHolidays();
        $mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);

        $this->assertEquals(
            $mock->resultStringBusinessHoursAndBusinessHolidays(),
            $mobiHelper->ToString()
        );
    }

    public function testValidTimeSettings() {
        $mobiHelper = new Sofa1MobiscrollConverter(7, new DateTime('2020-11-17'));
        $mock = new ValidMockData();
        $mobiHelper->AddBusinessHours($mock->businessHours());
        $businessHoliday = $mock->businessHolidays();
        $mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);
        $mobiHelper->AddTimeSettings($mock->pickupTimeSettings());
        $this->assertEquals(
            $mock->resultStringValidTimeSettings(),
            json_encode($mobiHelper->GetValidTimeSettings())
        );
    }

    public function testInvalidTimeSettings() {
        $mobiHelper = new Sofa1MobiscrollConverter(7, new DateTime('2020-11-17'));
        $mock = new ValidMockData();
        $mobiHelper->AddBusinessHours($mock->businessHours());
        $businessHoliday = $mock->businessHolidays();
        $mobiHelper->AddBusinessHolidays($businessHoliday->From, $businessHoliday->To, $businessHoliday->InfoText);
        $mobiHelper->AddTimeSettings($mock->pickupTimeSettings());
        $this->assertEquals(
            $mock->resultStringInvalidTimeSettings(),
            json_encode($mobiHelper->GetInvalidTimeSettings())
        );
    }
}
