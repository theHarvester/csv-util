<?php

require(__DIR__ . "/vendor/autoload.php");

// A requirement for the league csv package
if (! ini_get("auto_detect_line_endings")) {
    ini_set("auto_detect_line_endings", '1');
}

use Symfony\Component\Console\Application;
use TheHarvester\CsvUtil\Command\CommitToDbCommand;
use TheHarvester\CsvUtil\Command\HeaderListCommand;
use TheHarvester\CsvUtil\Command\HeaderSummaryCommand;
use TheHarvester\CsvUtil\Command\PreviewCommand;
use TheHarvester\CsvUtil\Command\SaveToMysqlCommand;
use TheHarvester\CsvUtil\Command\SelfUpdateCommand;

$console = new Application('CsvUtil', '@package_version@');

$console->add(new HeaderListCommand());
$console->add(new HeaderSummaryCommand());
$console->add(new SaveToMysqlCommand());
$console->add(new PreviewCommand());
$console->add(new SelfUpdateCommand());

$console->run();
