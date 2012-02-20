<?php
class Canape_Logger_Syslog implements ICanape_Logger {
	public function log($str, $level = LOG_INFO) {
		syslog($level, $str);
	}
}
?>