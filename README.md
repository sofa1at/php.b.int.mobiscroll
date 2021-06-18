## install the mobiscroll via composer
```json
{
  "require": {
    "sofa1at/php.b.int.mobiscroll": "^1"
  },
  "config": {
    "github-oauth": {
      "github.com": "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"
    }
  },
  "repositories": [
    {
      "type": "vcs",
      "url": "git@github.com:sofa1at/php.b.int.mobiscroll.git"
    }
  ]
}
```

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

#phpunit tests
Test if the output strings ar equal to the mock data strings.

Folder tests

TestClass InterpreterTest (interpretertest.php)

````php
// timeSettings with Holidays 
testTimeSettingsAndBusinessHolidays();
// timeSettings only
testTimeSettings();
// businessHours only
testBusinessHours();
// businessHours with Holidays
testBusinessHoursAndBusinessHolidays();
````
