<?php
class Canape_Config {
	
	private static $loaded_files = array();
	
	private function __construct() {}
	
	public static function load($code) {
		$file = Canape::getPath('config').DIRECTORY_SEPARATOR.$code.".cfg.php";
		if(in_array($file, self::$loaded_files)) {
			return self::$loaded_files[$file];
		}
		return (self::$loaded_files[$file] = new Canape_Config_File($file));
	}
}

class Canape_Config_File implements ArrayAccess{
	
	private $_filepath;
	private $_properties = array();
	
	static $loaded_configs = array();
	
	public function __construct($file) {
		$this->_filepath = $file;
		if(file_exists($this->_filepath)) {
			$this->_properties = include($this->_filepath);
		}
	}

	public function __set($varname, $value) {
		$this->_properties[$varname] = $value;
	}
	
	public function __get($varname) {
		return (isset($this->_properties[$varname])) ? $this->_properties[$varname] : null;
	}
	
	public function __isset($varname) {
		return isset($this->_properties[$varname]);
	}
	
	public function getProperties() {
		$a = func_get_args();
		return array_map(array($this, '__get'), $a);
	}
	
	public function toArray() {
		return $this->_properties;
	}
	
	public static function load($code) {
		$file = Canape::getPath('config').DIRECTORY_SEPARATOR.$code.".cfg.php";
		return new self($file);
	}
	
	public function save() {
		
	}
	
	 public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->_properties[] = $value;
        } else {
            $this->_properties[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->_properties[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->_properties[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->_properties[$offset]) ? $this->_properties[$offset] : null;
    }
}
?>