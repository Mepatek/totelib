<?php
declare(strict_types=1);

// replace by real path to vendor/autoload.php
require_once __DIR__ . "/../vendor/autoload.php";

// create empty adminer.css if not exist
touch(__DIR__ . '/adminer.css');
// initialize library, load environment variable from __DIR__ (replace by real path to .env file)
$toteLib = new \Mepatek\ToteLib\ToteLibFacade(__DIR__);

//redirect to adminer
require_once __DIR__ . "/../src/ToteAdminer.php";
exit();
