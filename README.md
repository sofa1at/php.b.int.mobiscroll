# php-mobiscrollinterpreter

This repository holds all interfaces/classes/traits related to PSR-3.

composer require sofa1at/mobiscrollinterpreter

````php
$converter = new Sofa1MobiscrollConverter();

// add businesshours
$converter->AddBusinessHours($mockDataBusinessHours);

// add timesettings
$converter->AddTimeSetting($mockTimeSetting);

// receive json
$converter->ToString();
````
