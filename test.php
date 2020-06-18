<?php

use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

require_once "vendor/autoload.php";

$converter = new Sofa1MobiscrollConverter();
$mockDataBusinessHours = json_decode("[{\"StationId\":200,\"Day\":\"Monday\",\"From\":\"17:30:00\",\"To\":\"22:00:00\",\"IsOpen\":true},{\"StationId\":200,\"Day\":\"Tuesday\",\"From\":\"09:00:00\",\"To\":\"22:00:00\",\"IsOpen\":true},{\"StationId\":200,\"Day\":\"Thursday\",\"From\":\"09:00:00\",\"To\":\"22:00:00\",\"IsOpen\":true},{\"StationId\":200,\"Day\":\"Monday\",\"From\":\"08:00:00\",\"To\":\"12:15:00\",\"IsOpen\":true},{\"StationId\":200,\"Day\":\"Wednesday\",\"From\":\"09:00:00\",\"To\":\"20:00:00\",\"IsOpen\":true},{\"StationId\":200,\"Day\":\"Friday\",\"From\":\"13:00:00\",\"To\":\"20:00:00\",\"IsOpen\":true}]");
$converter->AddBusinessHours($mockDataBusinessHours);

var_dump($converter->ToString());
