# PiMusic

## Preparing

- Get composer & run `composer install`
- nginx, gearman server, supervisor
- php, php-curl, php-gearman
- Copy `config/local.php_sample` to `config/local.php`

## Unit testing

Run all test cases:

	./phpunit.sh

Run single test caces:

	./phpunit.sh tests/SampleTest.php

Code coverage is generated at `var/coverage.txt`

## Manual testing

	curl -XPOST http://<host>/ --data "text=http://www.nhaccuatui.com/bai-hat/say-you-do-tien-tien.u29bozMlBM4b.html"

## Notes

Pi is running on `PHP 5.4`
