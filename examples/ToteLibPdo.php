<?php
declare(strict_types=1);
// use PDO not recommended, use Dibi

// replace by real path to vendor/autoload.php
require_once __DIR__ . "/../vendor/autoload.php";

// initialize library, load environment variable from __DIR__ (replace by real path to .env file)
$toteLib = new \Mepatek\ToteLib\ToteLibFacade(__DIR__);

// get PDO object
$pdo = $toteLib->getPdo();

// run query
$pdo->query("SELECT * FROM table");