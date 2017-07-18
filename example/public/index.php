<?php
$curDir = dirname(dirname(__FILE__));
$baseDir = dirname($curDir);
require_once $baseDir . '/vendor/autoload.php';
use Smiler\Autumn\Bootstrap;

Bootstrap::run($curDir . '/conf/conf.json', $curDir . '/apis');

