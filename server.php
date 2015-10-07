<?php
use Configuration\JsonConfigurationReader;
use Log\ReportLevel;

require_once 'bootstrap.php';

ReportLevel::setGlobalReportLevel(ReportLevel::$ALL);

$configurationReader = new JsonConfigurationReader();
$config = $configurationReader->getConfiguration("config.json");

$server = new Server($config);
$server->start();

// sorry jasper