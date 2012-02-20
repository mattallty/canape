<?php
$short_options = array(
	"p:", // php executable
);
$long_options = array(
	"php-exe:", // required
);

$opts = getopt(implode("", $short_options), $long_options);

if(!isset($opts['php-exe']) || empty($opts['php-exe'])) {
	die('Could no find PHP executable');
}

// include main lib
$canape_dir = realpath(dirname(__FILE__)."/..");
include($canape_dir.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."canape.lib.php");

// initialize Canape
Canape::getInstance();

Canape_Http_Server::getInstance()->start();
?>