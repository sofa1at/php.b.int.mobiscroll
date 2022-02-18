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

	public function testRegularBusinessHours()
	{
		$regularOpeningHoursService = new \Sofa1\Mobiscroll\v5\Mobiscroll5Service();

		$bh1 = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
		$bh1->Day = "Monday";
		$bh1->From = "08:00";
		$bh1->To = "20:00";
		$bh1->IsOpen = true;

		$bh2 = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
		$bh2->Day = "Tuesday";
		$bh2->From = "07:00";
		$bh2->To = "19:00";
		$bh2->IsOpen = true;

		$bh3 = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
		$bh3->Day = "Wednesday";
		$bh3->From = "06:00";
		$bh3->To = "18:00";
		$bh3->IsOpen = true;

		$bh4 = new \Sofa1\Core\Api\Dto\DateTime\BusinessHoursDto();
		$bh4->Day = "Thursday";
		$bh4->From = "05:00";
		$bh4->To = "17:00";
		$bh4->IsOpen = true;

		// soh = special opening hours
		$soh = new TimeSettingModel();
		$sohp1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel();
		$sohp1->FromDate = DateTime::createFromFormat("Y-m-d", "2022-01-01");
		$sohp1->ToDate = DateTime::createFromFormat("Y-m-d", "2022-02-01");
		$sohp1d1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel();
		$sohp1d1->Day = 4;
		$sohp1d1->FromTime = new \DateTime("1980-01-01 09:00");
		$sohp1d1->ToTime = new \DateTime("1980-01-01 17:00");
		$sohp1->TimeSettingPeriodDays[] = $sohp1d1;
		$soh->TimeSettingPeriods[] = $sohp1;

		// poh = pickup time settings
		$poh = new TimeSettingModel();
		$pohp1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel();
		$pohp1->FromDate = DateTime::createFromFormat("Y-m-d", "2022-01-01");
		$pohp1->ToDate = DateTime::createFromFormat("Y-m-d", "2022-02-01");
		$pohp1d1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel();
		$pohp1d1->Day = 1;
		$pohp1d1->FromTime = new \DateTime("1980-01-01 09:00");
		$pohp1d1->ToTime = new \DateTime("1980-01-01 17:00");
		$pohp1->TimeSettingPeriodDays[] = $pohp1d1;
		$poh->TimeSettingPeriods[] = $pohp1;

		// poh = pickup time settings
		$doh = new TimeSettingModel();
		$dohp1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodModel();
		$dohp1->FromDate = DateTime::createFromFormat("Y-m-d", "2022-01-01");
		$dohp1->ToDate = DateTime::createFromFormat("Y-m-d", "2022-12-31");
		$dohp1d1 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel();
		$dohp1d1->Day = 1;
		$dohp1d1->FromTime = new \DateTime("1980-01-01 09:00");
		$dohp1d1->ToTime = new \DateTime("1980-01-01 17:00");
		$dohp1->TimeSettingPeriodDays[] = $dohp1d1;
		$dohp1d2 = new \Sofa1\Core\Api\Model\DateTime\TimeSettingPeriodDayModel();
		$dohp1d2->Day = 2;
		$dohp1d2->FromTime = new \DateTime("1980-01-01 09:00");
		$dohp1d2->ToTime = new \DateTime("1980-01-01 17:00");
		$dohp1->TimeSettingPeriodDays[] = $dohp1d2;
		$doh->TimeSettingPeriods[] = $dohp1;

		$holidays = new \Sofa1\Core\Api\Dto\DateTime\StationBusinessHolidayDto();
		$holidays->From = new DateTime("2022-05-01 12:00");
		$holidays->To = new DateTime("2022-05-15 10:00");

		// add the regular business hours
		// Mo 08:00-20:00, Tu 07:00-19:00, We 06:00-18:00
		$regularOpeningHoursService->AddBusinessHours([$bh1, $bh2, $bh3]);

		// add special opening hours. the whole january every thursday from 09:00 - 17:00
		$regularOpeningHoursService->AddSpecialOpeningHours($soh);

		// add pickup and delivery times. these should not affect the end string
		$regularOpeningHoursService->AddPickupBusinessHours($poh);
		$regularOpeningHoursService->AddDeliveryBusinessHours($doh);

		// add business holidays from 2022-05-01 12:00 to 2022-05-15 10:00
		$regularOpeningHoursService->AddBusinessHolidays($holidays);

		$actualValue = $regularOpeningHoursService->RenderBusinessHours();
		$this->assertEquals(
			"{start:'00:00',end:'07:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-05-16'}},{start:'20:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-05-16'}},{start:'00:00',end:'06:59',recurring:{repeat:'weekly',weekDays:'TU',from:'2022-05-16'}},{start:'19:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'TU',from:'2022-05-16'}},{start:'00:00',end:'05:59',recurring:{repeat:'weekly',weekDays:'WE',from:'2022-05-16'}},{start:'18:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'WE',from:'2022-05-16'}},{recurring:{repeat:'weekly',weekDays:'TH,FR,SA,SU',from:'2022-05-16'}},{start:'00:00',end:'07:59',recurring:{repeat:'weekly',weekDays:'MO',until:'2021-12-31'}},{start:'20:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'MO',until:'2021-12-31'}},{start:'00:00',end:'06:59',recurring:{repeat:'weekly',weekDays:'TU',until:'2021-12-31'}},{start:'19:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'TU',until:'2021-12-31'}},{start:'00:00',end:'05:59',recurring:{repeat:'weekly',weekDays:'WE',until:'2021-12-31'}},{start:'18:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'WE',until:'2021-12-31'}},{recurring:{repeat:'weekly',weekDays:'TH,FR,SA,SU',until:'2021-12-31'}},{start:'00:00',end:'08:59',recurring:{repeat:'weekly',weekDays:'TH',from:'2022-01-01',until:'2022-02-01'}},{start:'17:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'TH',from:'2022-01-01',until:'2022-02-01'}},{recurring:{repeat:'weekly',weekDays:'MO,TU,WE,FR,SA,SU',from:'2022-01-01',until:'2022-02-01'}},{start:'00:00',end:'07:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-02-02',until:'2022-04-30'}},{start:'20:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-02-02',until:'2022-04-30'}},{start:'00:00',end:'06:59',recurring:{repeat:'weekly',weekDays:'TU',from:'2022-02-02',until:'2022-04-30'}},{start:'19:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'TU',from:'2022-02-02',until:'2022-04-30'}},{start:'00:00',end:'05:59',recurring:{repeat:'weekly',weekDays:'WE',from:'2022-02-02',until:'2022-04-30'}},{start:'18:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'WE',from:'2022-02-02',until:'2022-04-30'}},{recurring:{repeat:'weekly',weekDays:'TH,FR,SA,SU',from:'2022-02-02',until:'2022-04-30'}},{start:'2022-05-01T12:00',end:'2022-05-01T23:59'},{start:'2022-05-15T00:00',end:'2022-05-15T10:00'},{start:'2022-05-02',end:'2022-05-14'}",
			$actualValue
		);

		$pickupOpeningHoursService = new \Sofa1\Mobiscroll\v5\Mobiscroll5Service();
		$pickupOpeningHoursService->AddBusinessHours([$bh1, $bh2, $bh3, $bh4]);
		$pickupOpeningHoursService->AddPickupBusinessHours($poh);
		$pickupOpeningHoursService->AddDeliveryBusinessHours($doh);
		$actualValue = $pickupOpeningHoursService->RenderPickupBusinessHours();

		$this->assertEquals(
			"{recurring:{repeat:'weekly',weekDays:'SU,MO,TU,WE,TH,FR,SA',from:'2022-02-01'}},{recurring:{repeat:'weekly',weekDays:'SU,MO,TU,WE,TH,FR,SA',until:'2022-01-01'}},{start:'00:00',end:'08:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-01-01',until:'2022-02-01'}},{start:'17:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-01-01',until:'2022-02-01'}},{recurring:{repeat:'weekly',weekDays:'TU,WE,TH,FR,SA,SU',from:'2022-01-01',until:'2022-02-01'}}",
			$actualValue
		);

		$deliveryOpeningHoursService = new \Sofa1\Mobiscroll\v5\Mobiscroll5Service();
		$deliveryOpeningHoursService->AddBusinessHours([$bh1, $bh2, $bh3, $bh4]);
		$deliveryOpeningHoursService->AddDeliveryBusinessHours($doh);
		$deliveryOpeningHoursService->AddBusinessHolidays($holidays);
		$actualValue = $deliveryOpeningHoursService->RenderDeliveryBusinessHours();

		$this->assertEquals(
			"{recurring:{repeat:'weekly',weekDays:'SU,MO,TU,WE,TH,FR,SA',from:'2022-02-01'}},{recurring:{repeat:'weekly',weekDays:'SU,MO,TU,WE,TH,FR,SA',until:'2022-01-01'}},{start:'00:00',end:'08:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-01-01',until:'2022-02-01'}},{start:'17:01',end:'23:59',recurring:{repeat:'weekly',weekDays:'MO',from:'2022-01-01',until:'2022-02-01'}},{recurring:{repeat:'weekly',weekDays:'TU,WE,TH,FR,SA,SU',from:'2022-01-01',until:'2022-02-01'}}",
			$actualValue
		);
	}

}
