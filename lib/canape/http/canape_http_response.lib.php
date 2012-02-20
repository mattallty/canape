<?php
class Canape_Http_Response {
	
	private $_code = 200;
	private $_body = "";
	private $_cookies = array();
	private $_headers = array("Connection" => "close", 'Content-type' => 'text/html');
	
	public function __construct($body, $headers=array(), $code=200) 
	{
		$this->_body = $body;
		$this->_code = $code;
		if(is_array($headers)) {
			$this->_headers = array_merge($this->_headers, $headers);
		}
	}
	
	/**
	 * Set a cookie that will be sent in the HTTP response
	 * 
	 * @see http://en.wikipedia.org/wiki/HTTP_cookie#Setting_a_cookie
	 */
	public static function setCookie($name, $value, $expire = 0, $path = "", $domain = "", $secure = false, $httponly = false) 
	{
		$cookie = $name.'='.rawurlencode($value);
		if($expire) 		
			$cookie .= "; Expires=".date(DATE_COOKIE, $expire);
		if(!empty($path)) 	
			$cookie .= "; Path=".$path;
		if(!empty($domain)) 	
			$cookie .= "; Domain=".$domain;
		if($secure) 	
			$cookie .= "; Secure";
		if($httponly) 	
			$cookie .= "; HttpOnly";
		
		$this->_cookies[] = $cookie;			
	}
	
	public function __toString() 
	{
		// add content length to headers
		$this->_headers['Content-Length'] = strlen($this->_body);
		// generate headers string
		$headers = '';
		foreach ($this->_headers as $k => $v) {
			$headers .= $k.': '.$v."\r\n";
		}
		// generate cookies here as it allows us to call Canape_Http_Response::setCookie
		// even before calling Canape_Http_Response's constructor
		if(count($this->_cookies)) {
			foreach($this->_cookies as $cookie) {
				$headers .= 'Set-Cookie: '.$v."\r\n";
			}
		}
		// generate proper HTTP Response
		$response = implode("\r\n", array(
			'HTTP/1.1 '.$this->_code,
			$headers,
			$this->_body
		));
		return $response;
	}
	
}
?>