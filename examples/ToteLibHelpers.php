<?php
declare(strict_types=1);

// replace by real path to vendor/autoload.php
require_once __DIR__ . "/../vendor/autoload.php";

// initialize library, load environment variable from __DIR__ (replace by real path to .env file)
$toteLib = new \Mepatek\ToteLib\ToteLibFacade(__DIR__);

// get Dibi result with all data from 'table'
$result = $toteLib->getTableResult("table");

// get Nette Html element table with all data from 'table'
$htmlTable = $toteLib->getFullTableAsHtmlTable("table");
// and show it
echo $htmlTable;

// get Nette Html element table with id and name data from 'table' where date = current date
$htmlTable = $toteLib->getQueryResultAsHtmlTable('SELECT id, name FROM table WHERE date = %d', new DateTime());
// and show it
echo $htmlTable;