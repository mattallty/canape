<?php
class Canape_Logger_File implements ICanape_Logger {
	
	private $_fp;
	private $_log_to_stdout;
	
	public function __construct() {
		$this->_fp = @fopen(Canape::getPath('logs', 'canape.log'), 'a');
		$this->_log_to_stdout = (bool)Canape_Config::load('canape')->logger_stdout;		
	}
	
	public function __destruct() {
		@fclose($this->_fp);
	}
	
	public function log($str, $level = LOG_INFO) {
		@fwrite($this->_fp, $str."\n");
		if($this->_log_to_stdout) {
			fwrite(STDOUT, $str."\n");		
		}
	}
}
?>