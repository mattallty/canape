#!/bin/bash
SYS_DIR="$( cd "$( dirname "$0" )" && pwd )"
CANAPE_DIR=${SYS_DIR%/*}
BIN_DIR=$CANAPE_DIR/bin
PHP_EXE=$(which php)

#
# If PHP is not installed, use the embeded version
#
if [ "$PHP_EXE" = "" ]; then
	PHP_EXE=$BIN_DIR/php
fi

#
# If no pcntl support found, use embeded version of PHP
#
PCNTL_SUPPORT=`$PHP_EXE -m | grep pcntl | wc -l`
if [ $PCNTL_SUPPORT = "0" ]; then
	PHP_EXE="$BIN_DIR/php"
fi
	
$PHP_EXE -f $SYS_DIR/launch.php -- --php-exe=$PHP_EXE