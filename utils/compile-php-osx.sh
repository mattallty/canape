#!/bin/bash

PHP_URL="http://downloads.php.net/stas/php-5.4.0RC8.tar.gz"
PHP_VERSION="5.4.0RC8"
CURL_URL="http://curl.haxx.se/download/curl-7.24.0.tar.gz"
CURL_VERSION="7.24.0"

WORK_DIR="/tmp/work_dir"

if [ -d "$WORK_DIR" ]; then 
	rm -Rf $WORK_DIR
fi

mkdir $WORK_DIR; cd $WORK_DIR

curl -O "$CURL_URL";
curl -O "$PHP_URL";
tar -xzf "php-$PHP_VERSION.tar.gz"
tar -xzf "curl-$CURL_VERSION.tar.gz"

cd "php-$PHP_VERSION"
./configure --enable-cli --enable-sigchild --with-openssl --with-zlib --with-bz2 --with-curl --disable-fileinfo --enable-pcntl --enable-sockets --enable-sysvmsg --enable-sysvsem --enable-sysvshm --disable-tokenizer --disable-xml --disable-xmlreader --disable-xmlwriter --enable-zip --enable-static --without-pear --enable-zend-signals --enable-shared=no


