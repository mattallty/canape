<?php
class Canape_Http_Request {
	
	private $_properties = array();
	
	public function __construct($raw_request) {
		if(!empty($raw_request) && is_string($raw_request)) {
			$raw_request = trim($raw_request);
			$lines = explode("\r\n", $raw_request);
			$first_line = array_shift($lines);
			if(!preg_match("#(GET|POST) (.*) HTTP/([0-9\.]+)#", $first_line, $regs)) {
				return;
			}
			$this->_properties['httpMethod'] = $regs[1];
			$this->_properties['path'] = $regs[2];
			$this->_properties['httpVersion'] = $regs[3];
			foreach($lines as $line) {
				list($http_var, $http_val) = explode(":", $line, 2);
				$this->_properties[str_replace('-', '', lcfirst($http_var))] = trim($http_val);	
			}
			if(isset($this->_properties['cookie'])) {
				$this->_decodeCookie();
			}
		}		
	}
	
	private function _decodeCookie() {
		$a = explode("; ", $this->_properties['cookie']);
		foreach($a as $raw_val) {
			$props = explode('=', $raw_val, 2);
			$_COOKIE[$props[0]] = rawurldecode($props[1]);
		}
		$this->_properties['cookies'] = $_COOKIE;
		unset($this->_properties['cookie']);
	}
	
	public function __get($varname) {
		return (isset($this->_properties[$varname])) ? $this->_properties[$varname] : null;
	}
	
	public function __toString() {
		return json_encode($this->_properties);
	}
	
	public function getLog() {
		return $this->_properties['httpMethod']." ".$this->_properties['path'];
	}
	
}
?>