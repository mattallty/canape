#!/usr/bin/env php
<?php
define('WORK_DIR', '/Users/matt/temp/work_dir');
define('BIN_DIR', '/Users/matt/temp/local');
define('SEP', DIRECTORY_SEPARATOR);

$modules = array(
	'curl' => array(
		'url' => 'http://curl.haxx.se/download/curl-7.24.0.tar.gz',
		'configure-options' => '--disable-shared --prefix='.BIN_DIR,
		'cflags' => '' 
	),
	'zlib' => array(
		'url' => 'http://zlib.net/zlib-1.2.6.tar.gz',
		'configure-options' => '--disable-shared --prefix='.BIN_DIR,
		'cflags' => '',
		'post-install' => "deleteZlibShared"
	),
	/*
	'libxml' => array(
		'url' => 'ftp://xmlsoft.org/libxml2/libxml2-2.7.8.tar.gz',
		'configure-options' => '--disable-shared --enable-static --without-debug --without-modules --without-legacy --without-python--prefix='.BIN_DIR,
		'cflags' => ''
	),*/
	'php' => array(
		'url' => 'http://downloads.php.net/stas/php-5.4.0RC8.tar.gz',
		'cflags' => "",
		'configure-options' => 	'--prefix='.BIN_DIR.' '.
								'--enable-cli '.
								'--enable-sigchild '.
								'--without-iconv '.
								'--with-libxml-dir=/usr/lib/ '.
								'--with-zlib '.
								'--with-zlib-dir='.BIN_DIR.SEP.' '.
								'--with-curl='.BIN_DIR.SEP.' '.
								'--disable-fileinfo '.
								'--disable-phar '.
								'--enable-pcntl '.
								'--enable-sockets '.
								'--enable-sysvmsg '.
								'--enable-sysvsem '.
								'--enable-sysvshm '.
								'--disable-pdo '. 
								'--disable-tokenizer '. 
								'--disable-xml '.
								'--disable-xmlreader '. 
								'--disable-xmlwriter '.
								'--enable-zip '.
								'--enable-static '. 
								'--without-pear '.
								'--enable-zend-signals '. 
								'--enable-shared=no '.
								'--disable-cgi '.
								'--disable-debug '.
								'--enable-inline-optimization',
								'--disable-dependency-tracking',
	 
	)
);

function replacePathsClabback($a) {
	global $modules;
	$pathinfo = pathinfo(basename($modules[$a[1]]['url']));
	if(substr($pathinfo['filename'], -4) == ".tar") {
		$pathinfo['filename'] = substr($pathinfo['filename'], 0, -4);
	}
	return $pathinfo['filename'];  
}

foreach($modules as $code => $module) {
	$modules[$code]['configure-options'] = preg_replace_callback("/%%([a-z]+)::([a-z]+)%%/" , "replacePathsClabback" , $module['configure-options']);
	$modules[$code]['cflags'] = preg_replace_callback("/%%([a-z]+)::([a-z]+)%%/" , "replacePathsClabback" , $module['cflags']);
}

if(!is_dir(WORK_DIR)) {
	echo "Making dir ".WORK_DIR."\n";
	mkdir(WORK_DIR);
}
if(!is_dir(BIN_DIR)) {
	echo "Making dir ".BIN_DIR."\n";
	mkdir(BIN_DIR);
}

function deleteZlibShared() {
	unlink(BIN_DIR.SEP."libz.1.2.6.dylib");
	unlink(BIN_DIR.SEP."libz.1.dylib");
	unlink(BIN_DIR.SEP."libz.dylib");
}

foreach($modules as $code => $module) 
{
	chdir(WORK_DIR);
	$pathinfo = pathinfo(basename($module['url']));
	
	if(!file_exists(WORK_DIR.SEP.$pathinfo['basename'])) {
		echo "Downloading $code...\n";
		exec("curl --silent -O {$module['url']}");
	}
	if(substr($pathinfo['filename'], -4) == ".tar") {
		$pathinfo['filename'] = substr($pathinfo['filename'], 0, -4);
	}
	if(is_dir(WORK_DIR.SEP.$pathinfo['filename'])) {
		echo "Cleaning ".WORK_DIR.SEP.$pathinfo['filename']."...\n";
		exec("rm -Rf ".WORK_DIR.SEP.$pathinfo['filename']);
	}
	echo "Untar ".WORK_DIR.SEP.$pathinfo['basename']."...\n";
	exec("tar -xzf ".WORK_DIR.SEP.$pathinfo['basename']);
	chdir(WORK_DIR.SEP.$pathinfo['filename']);
	echo "Configuring $code with ".$module['configure-options']."\n";
//	echo shell_exec($module['cflags']." ./configure ".$module['configure-options']);
	exec("./configure ".$module['configure-options']);
	//echo implode("\n", $out);
	
	if($code === "php") {
		file_put_contents(WORK_DIR.SEP.$pathinfo['filename'].SEP."Makefile", str_replace(
			'$(ZEND_EXTRA_LIBS) -o $(SAPI_CLI_PATH)', 
			'$(ZEND_EXTRA_LIBS) -all-static -o $(SAPI_CLI_PATH)',
			file_get_contents(WORK_DIR.SEP.$pathinfo['filename'].SEP."Makefile")
		));
	}

	echo "Making $code...\n";
	
	if($code === "php") {
		//exit;
	}
	
	echo shell_exec("make");
	echo shell_exec("make install");
	echo "Done with $code !\n\n";
	
	
	if(isset($module['post-install'])) {
		call_user_func("deleteZlibShared");
	}
	
	
	if($code === "php") {
		echo shell_exec("otool -L ".BIN_DIR."/bin/php");
	}
}
?>