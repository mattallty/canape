<?php
class Canape {
	
	private static $instance;
	private static $basedir;
	private static $_logger;
	
	private $_config;
	private $_hooks = array();

	/**
	 * Constructor
	 * 
	 * @return Canape
	 */
    private function __construct() 
    {
    	// order is important, as we have to set self::$basedir before registering autoload
    	self::$basedir = realpath(dirname(__FILE__)."/..");
		spl_autoload_register(array($this, 'autoload'));
		
		// load all interfaces
		$interfaces = glob(self::$basedir.DIRECTORY_SEPARATOR."lib/interfaces/*.itf.php");
		foreach ($interfaces as $itf_file) 
			include($itf_file);
		
		$this->_config = Canape_Config::load('canape');
		
		// set timezone
		if(isset($this->_config->timezone) && !empty($this->_config->timezone)) {
			date_default_timezone_set($this->_config->timezone);
		}
		
		// load logger
		self::$_logger = new $this->_config['logger_driver'];
		
		self::log('Canape started');
    }
	
	/**
	 * Returns Canape path or sub-paths
	 * 
	 * @param $part mixed part Can be a string to concatenate, eg 'config', 'plugins', 'sys', etc.
	 * It also can be an array of strings to concatenate, or you can call getPath() with multiple arguments.
	 * @example getPath('lib')
	 * @example getPath(array('lib', 'http'))
	 * @example getPath('lib', 'http')
	 * @return string Path 
	 */
	public static function getPath($part="") {
		if(empty($part)) {
			return self::$basedir;
		}else if(func_num_args() > 1) {
			$args = func_get_args();
		}else if(is_string($part)) {
			$args = array($part);			
		}
		return self::$basedir.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $args);
	}
	
	public static function log($str, $level = LOG_INFO) {
		$str = date('Y-m-d H:i:s').' '.$str;
		call_user_func_array(array(self::$_logger, 'log'), array($str, $level));
	}

    public static function getInstance() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
	
	public function __invoke() {
		var_dump(func_get_args());
	}

    public function __clone()
    {
    	throw new Canape_Exception('Cloning '.__CLASS__.' is not allowed.', 1);
    }
	
	private function autoload($class) {
		$class = strtolower($class);
		$parts = explode("_", $class);
		array_pop($parts);
		$path = self::getPath('lib').DIRECTORY_SEPARATOR.implode("/", $parts).DIRECTORY_SEPARATOR.$class.".lib.php";
		if(file_exists($path)) {
			include($path);
		}
	}
	
}

class Canape_Exception extends Exception {
	static $previous_exception = null;
	public function __construct($message, $code = 0, $http_code = 0) {
		parent::__construct($message, $code, self::$previous_exception);
		self::$previous_exception = $this;
	}
}
?>