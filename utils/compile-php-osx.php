#!/usr/bin/env php
<?php
define('WORK_DIR', '/tmp/work_dir');
define('SEP', DIRECTORY_SEPARATOR);
$modules = array(
	'curl' => array(
		'url' => 'http://curl.haxx.se/download/curl-7.24.0.tar.gz'
	),
	'zlib' => array(
		'url' => 'http://zlib.net/zlib-1.2.6.tar.gz'
	),
	'php' => array(
		'url' => 'http://downloads.php.net/stas/php-5.4.0RC8.tar.gz'
	)
);

if(!is_dir(WORK_DIR)) {
	mkdir(WORK_DIR);
}
chdir(WORK_DIR);

foreach($modules as $code => $module) {
	$pathinfo = parse_url($module['url']);
	//if(!file_exists(WORK_DIR.SEP.)) {
		
	//}
}
?>