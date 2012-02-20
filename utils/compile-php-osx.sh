#!/bin/bash
PHP_URL="http://downloads.php.net/stas/php-5.4.0RC8.tar.gz"
PHP_VERSION="php-5.4.0RC8"

CURL_URL="http://curl.haxx.se/download/curl-7.24.0.tar.gz"
CURL_VERSION="curl-7.24.0"

ZLIB_URL="http://zlib.net/zlib-1.2.6.tar.gz"
ZLIB_VERSION="zlib-1.2.6"

WORK_DIR="/tmp/work_dir"




if [ ! -d "$WORK_DIR" ]; then 
	mkdir $WORK_DIR;
fi

cd $WORK_DIR


# Zlib
if [ ! -f "$WORK_DIR/php-$PHP_VERSION.tar.gz" ]; then
	curl -O "$PHP_URL";
fi

#curl -O "$CURL_URL";
#tar -xzf "$CURL_VERSION.tar.gz"

#.......................................................
#
#						PHP
#
#.......................................................
if [ -d "$WORK_DIR/php-$PHP_VERSION" ]; then 
	rm -Rf "$WORK_DIR/$PHP_VERSION"
fi

if [ ! -f "$WORK_DIR/$PHP_VERSION.tar.gz" ]; then
	curl -O "$PHP_URL";
fi	

if [ ! -d "$WORK_DIR/$PHP_VERSION" ]; then
	tar -xzf "$PHP_VERSION.tar.gz"
fi	
	
cd "php-$PHP_VERSION"
./configure --enable-cli --enable-sigchild --without-iconv --with-libxml-dir=/usr/lib/ --with-zlib --with-zlib-dir=/usr/lib/libz.dylib --with-bz2=/usr/lib/libbz2.1.0.dylib --with-curl=/usr/lib/ --disable-fileinfo --enable-pcntl --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --disable-tokenizer --disable-xml --disable-xmlreader --disable-xmlwriter --enable-zip --enable-static --without-pear --enable-zend-signals --enable-shared=no --disable-cgi --disable-debug --enable-inline-optimization

sed -e "s/\$(ZEND_EXTRA_LIBS) -o \$(SAPI_CLI_PATH)/\$(ZEND_EXTRA_LIBS) -all-static -o \$(SAPI_CLI_PATH)/" Makefile > Makefile.new

mv Makefile Makefile.old
mv Makefile.new Makefile

make

otool -L sapi/cli/php
