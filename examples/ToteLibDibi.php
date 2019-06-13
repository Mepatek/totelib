<?php
declare(strict_types=1);

// replace by real path to vendor/autoload.php
require_once __DIR__ . "/../vendor/autoload.php";

// initialize library, load environment variable from __DIR__ (replace by real path to .env file)
$toteLib = new \Mepatek\ToteLib\ToteLibFacade(__DIR__);

// get Dibi\Database object with
$databaseInfo = $toteLib->getDatabaseInfo();

// get array of Dibi\Table object
$tables = $toteLib->getTables();

// get array of table names (string)
$tables = $toteLib->getTableNames();

// get Dibi\Result from TOTE database WHERE name start with 'likeNameString'
$result = $toteLib->query("SELECT id, name FROM TableName WHERE name LIKE %like~", "likeNameString");

// get all rows from result
$all = $result->fetchAll();

// iterate row by row
foreach ($result as $row) {
    echo $row->id;
    echo $row->name;
}

