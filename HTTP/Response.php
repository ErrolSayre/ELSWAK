<?php
/*
	ELSWAK HTTP Response
	
	This class was derived from the Zend Controller Response Abstract in order to provide some of the low level HTTP response items (such as headers) to the HTML, XML, and JSON response objects. It also serves as a common ancestor for those classes.
	If you need the advanced features this class is lacking, it is recommended that you use the Zend class.
*/

require '../StandardConstants.php';

class ELSWAK_HTTP_Response {
	protected $serverUri;
	protected $applicationPath;
	protected $headers = array();
	protected $status = '';
	protected $messages = array();
	protected $body = array();
	protected $statusCode = 200;
	protected $isRedirect = false;
	protected $isModified = true;
	protected $debugRedirects = false;
	
	
	
	public function __construct() {
		// setup the server uri
		$this->serverUri = ((isset($_SERVER['HTTPS']))? 'https://': 'http://').$_SERVER['HTTP_HOST'];
		
		// setup the application uri
		$this->applicationPath = dirname($_SERVER['PHP_SELF']);
		
		// setup frame blocking by default, requiring the user to change this behavior
		$this->setFramePermission();
	}
	public function serverUri() {
		return $this->serverUri;
	}
	public function applicationPath() {
		return $this->applicationPath;
	}
	public function overrideApplicationPath($path) {
		$this->applicationPath = pathinfo($path, PATHINFO_DIRNAME);
	}
	public function applicationUri() {
		return $this->serverUri.$this->applicationPath;
	}
	public function messages($delimiter = '') {
		return implode($delimiter, $this->messages);
	}
	public function addMessage($message) {
		// append the message to our list of messages
		$this->messages[] = str_replace(CRLF, LF, $message);
		return $this;
	}
	public function status() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = str_replace(CRLF, LF, $status);
		return $this;
	}
	public function content($delimiter = '') {
		// string together the various bits of content
		return implode($delimiter, $this->body);
	}
	public function setContent($content = null, $key = null, $type = null) {
		// overwrite all existing content and replace with the supplied content
		$this->body = array();
		return $this->addContent($content, $key, $type);
	}
	public function addContent($content, $key = null, $type = null) {
		// append content to the body or set/overwrite the value of a given key if provided
		if ($key != null)
			return $this->setContentForKey($key, $content, $type);
		else
			$this->body[] = $this->filterContentByType($content, $type);
		return $this;
	}
	public function setContentForKey($key, $content, $type = null) {
		// set or unset a value for a given key
		if ($content !== null)
			$this->body[$key] = $this->filterContentByType($content, $type);
		else
			unset($this->body[$key]);
		return $this;
	}
	protected function filterContentByType($content, $type) {
		return $content;
	}
	public function setFramePermission($type = null) {
		$type = strtolower($type);
		if ($type == 'allowed') {
			$this->setHeader('X-Frame-Options');
		} else if ($type == 'local' || $type == 'sameorigin') {
			$this->setHeader('X-Frame-Options', 'SAMEORIGIN');
		} else {
			$this->setHeader('X-Frame-Options', 'DENY');
		}
		return $this;
	}
	public function headers($delimeter = CRLF) {
		return implode($delimiter, $this->headers);
	}
	public function clearHeaders() {
		$this->headers = array();
		return $this;
	}
	public function header($name) {
		if (isset($this->headers[$name]))
			return $this->headers[$name];
		return false;
	}
	public function setHeader($name, $value = null, $replace = true) {
		if ($value !== null)
			$this->headers[$name] = array(
				'name' => str_replace(CRLF, LF, $name),
				'value' => str_replace(CRLF, LF, $value),
				'replace' => (bool) $replace
			);
		else
			unset($this->headers[$name]);
		return $this;
	}
	public function isRedirect() {
		return $this->isRedirect;
	}
	public function setRedirect($url, $code = 303) {
		// set the location header using the given url
		$this->setHeader('Location', $url, true)
			->setStatusCode($code);
		return $this;
	}
	public function unsetRedirect() {
		return $this->setHeader('Location');
	}
	public function setDebugRedirects($value = true) {
		$this->debugRedirects = false;
		if ($value) {
			$this->debugRedirects = true;
		}
		return $this;
	}
	public function isModified() {
		return $this->isModified;
	}
	public function setIsModified($modified = true) {
		if (!$modified) {
			$this->setStatusCode(304);
			$this->isModified = false;
		} else {
			if ($this->statusCode == 304)
				$this->setStatusCode(200);
			$this->isModified = true;
		}
		return $this;
	}
	public function statusCode() {
		return $this->statusCode;
	}
	public function setStatusCode($code) {
		if (!is_int($code) || ($code < 100) || ($code > 599))
			throw new Exception('Invalid HTTP response code.');
		
		// determine if this code corresponds to a redirect
		if (($code <= 300) && ($code >= 307))
			$this->isRedirect = true;
		else
			$this->isRedirect = false;
		$this->statusCode = $code;
		return $this;
	}
	public function responseCode() {
		return $this->statusCode();
	}
	public function setResponseCode($code) {
		return $this->setStatusCode($code);
	}
	public function setContentType($type = 'text/html', $set = 'utf-8') {
		if (!empty($set))
			$this->setHeader('Content-Type', $type.'; charset='.$set, true);
		else
			$this->setHeader('Content-Type', $type, true);
	}
	public function setExpires($time) {
		$this->setHeader('Expires', date('r', $time), true);
	}
	public function generateETag() {
		// use the md5 sum of the content to produce an ETag for this repsonse
		$this->setHeader('ETag', md5($this->content()), true);
	}
	public function utilizeCache() {
		// generate an entity tag for the current response
		$this->generateETag();
		
		// look for a provided entity tag from the client
		$headers = apache_request_headers();
		$etag = '';
		if (!empty($headers['If-None-Match']))
			$etag = str_replace('-gzip', '', $headers['If-None-Match']);
		
		if ($etag == $this->header('ETag'))
			// report that this response has not changed
			$this->setHeader('ETag', $this->header('ETag').'-gzip', true)
				->setIsModified(false);
	}
	public function send() {
		// verify that headers have not been sent
		$this->canSendHeaders();
		
		// output the headers
		$this->sendHeaders();
		
		// output the custom headers if
		$this->sendCustomHeaders();
		
		// output the content if modified from the indicators sent by the client
		if ($this->isModified) {
			$this->sendContent();
		}
		return $this;
	}
	protected function canSendHeaders() {
		// check to see if the headers have been sent
		$headersSent = headers_sent($file, $line);
		
		// if the headers have been sent, throw and exception
		if ($headersSent)
			throw new Exception('Unable to send headers. Headers already sent in ' . $file . ' on line ' . $line);
		return !$headersSent;
	}
	protected function sendHeaders() {
		// send the status line
		header('HTTP/1.1 '.$this->statusCode.self::reasonPhraseForStatusCode($this->statusCode));
		
		// process the headers
		foreach ($this->headers as $header) {
			// unset the redirect if applicable
			if ($this->debugRedirects == true && strtolower($header['name']) == 'location') {
				$this->addMessage('Redirect to '.$header['value']);
			} else {
				header($header['name'].': '.$header['value'], $header['replace']);
			}
		}
		return $this;
	}
	protected function sendCustomHeaders() {
		// send custom headers created by this content type
		if (!empty($this->status))
			header('ELSWAK-Status: '.$this->status, true);
		if (count($this->messages) > 0)
			header('ELSWAK-Messages: '.$this->messages('|'), true);
		return $this;
	}
	public function sendContent() {
		echo $this->content();
		return $this;
	}
	public static function reasonPhraseForStatusCode($code) {
		$phrases = self::reasonPhrases();
		if (array_key_exists($code, $phrases)) {
			return ' '.$phrases[$code];
		}
		return '';
	}
	public static function reasonPhrases() {
		return array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Time-out',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Large',
			415 => 'Unsupported Media Type',
			416 => 'Requested range not satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Time-out',
			505 => 'HTTP Version not supported',
		);
	}
}
