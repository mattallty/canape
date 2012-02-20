<?php
$canape_dir = realpath(dirname(__FILE__)."/..");
include $canape_dir.'/lib/canape.lib.php';

$o = Canape_Config::load("canape");
Canape_Http_Server::getInstance()->start();
var_dump($o);

?>