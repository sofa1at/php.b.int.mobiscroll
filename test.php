<?php

use Sofa1\Mobiscroll\Sofa1MobiscrollConverter;

require_once "vendor/autoload.php";
require_once 'tests/valid-mock-data.php';

$mobiHelper = new Sofa1MobiscrollConverter();
$mock = new ValidMockData();
$mobiHelper->AddBusinessHours($mock->businessHours());

echo $mobiHelper->ToString();
