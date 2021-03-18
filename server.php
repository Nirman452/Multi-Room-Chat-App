<?php
// require_once __DIR__ . "/../vendor/autoload.php";
require_once "vendor/autoload.php";

$port = 9911;
$server = new \Chat\BasicMultiRoomServer;

\Chat\BasicMultiRoomServer::run($server, $port);
