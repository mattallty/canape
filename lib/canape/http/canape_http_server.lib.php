<?php
/**
 * Canape HTTP Server in pure PHP.
 * Use a singleton
 */
class Canape_Http_Server {
	
	/**
	 * Canape_Http_Server instance
	 * @static
	 * @access private
	 */
	private static $instance;
	/**
	 * Is running flag 
	 * @access private
	 */
	private $_running = false;
	/**
	 * Main server socket
	 * @access private
	 */
	private $_socket = false;
	/**
	 * @access private
	 */
	private $_config = array();

	/**
	 * Private constructor (singleton pattern)
	 * @private 
	 */
    private function __construct($config = array()) 
    {
    	$canape_dir = realpath(dirname(__FILE__)."/..");
		$config = Canape_Config::load('canape')->toArray();
		$this->_config = array_merge(array("hostname" => "localhost", "port" => 8899), $config);
		if(empty($this->_config['hostname'])) {
			throw new Canape_Exception('Error while initializing Canape HTTP server. Wrong hostname "'.$this->_config['hostname'].'"');
		}
		if(empty($this->_config['port'])) {
			throw new Canape_Exception('Error while initializing Canape HTTP server. Wrong port "'.$this->_config['port'].'"');
		}
    }
	/**
	 * Returns the Canape_Http_Server singleton instance
	 * 
	 * @static
	 * @access public
	 * @return Canape_Http_Server
	 */
    public static function getInstance() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }
	
	public function handleRequest($connection, $request) {
		var_dump($connection, $request);
	}
	
	public function start() 
	{
		if (($this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
			$err = 'Server cannot create socker. Error : '.socket_strerror($this->_socket);
			Canape::log($err);
			throw new Canape_Exception($err);
			exit();
		}
		if (($ret = socket_bind($this->_socket, $this->_config['hostname'], intval($this->_config['port']))) < 0) {
			$err = 'Failed to bind socket. Error : '.socket_strerror($ret);
			Canape::log($err);
			throw new Canape_Exception($err);
			exit();
		}
		if (($ret = socket_listen($this->_socket, 0)) < 0) {
			$err = 'Failed to listen to socket. Error : '.socket_strerror($ret);
			Canape::log($err);
			throw new Canape_Exception($err);
			exit();
		}
		Canape::log("Canape HTTP server listening on ".$this->_config['hostname'].":".$this->_config['port']);
		$listening = true;
		while ($listening) 
		{
			$conn = socket_accept($this->_socket);
			if ($conn < 0) {
				throw new Canape_Exception(__CLASS__.' error : '.socket_strerror($conn));
				exit();
			} else if ($conn === false) {
				usleep(10000);
			} else {
				@socket_getpeername($conn, $client_ip, $remote_port);
				$client_hostname = gethostbyaddr($client_ip);
				Canape::log(__CLASS__." : New connexion from peer $client_ip | hostname=$client_hostname | remote_port=$remote_port");
				$pid = pcntl_fork();
				if ($pid == -1) {
					throw new Canape_Exception(__CLASS__.' error : Cannot fork.');
					exit();
				} else if ($pid == 0) {
					$listening = false;
					socket_close($this->_socket);
					$request = '';
					while (substr($request, -4) !== "\r\n\r\n") {
						$request .= socket_read($conn, 1024);
						usleep(100);
					}
					
					$request = new Canape_Http_Request($request);
					Canape::log(__CLASS__." : Request from $client_hostname ($client_ip): ".$request->getLog());
					
					$code = 200;
					$body = '<center><h1>'.__CLASS__.'</h1></center>';
					$headers = array(
						'Connection' => 'close',
						'Content-type' => 'text/html',
						'Content-Length' => strlen($body)
					);
					$header = '';
					foreach ($headers as $k => $v) {
						$header .= $k.': '.$v."\r\n";
					}
					
					$response = new Canape_Http_Response('<h1>You !</h1>');
					socket_write($conn, $response);
					socket_close($conn);
					exit;
				} else {
					socket_close($conn);
				}
			}
			usleep(50000);
		}
	}
	
	public function __invoke() {
		var_dump(func_get_args());
	}

    public function __clone()
    {
    	throw new Canape_Exception('Cloning '.__CLASS__.' is not allowed.', 1);
    }
}
?>