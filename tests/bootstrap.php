<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('eMapper', __DIR__);
$loader->add('Acme',    __DIR__);

//set default timezone
date_default_timezone_set('America/New_York');
