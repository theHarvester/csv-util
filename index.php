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
use TheHarvester\CsvUtil\Command\SaveToMysqlCommand;

$console = new Application();

$console->add(new HeaderListCommand());
$console->add(new HeaderSummaryCommand());
$console->add(new SaveToMysqlCommand());

$console->run();
