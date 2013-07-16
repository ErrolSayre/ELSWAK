<?php
/*
	ELSWAK HTTP Response
	
	This class was derived from the Zend Controller Response Abstract in order to provide some of the low level HTTP response items (such as headers) to the HTML, XML, and JSON response objects. It also serves as a common ancestor for those classes.
	If you need the advanced features this class is lacking, it is recommended that you use the Zend class.
*/

require dirname(dirname(__FILE__)).'/StandardConstants.php';

class ELSWAK_HTTP_Response {



	/**
	 * Application Base URL
	 *
	 * Specify a URL for the application this response belongs to.
	 *
	 * @type ELSWAK_HTTP_URL
	 */
	protected $baseURL;
	
	protected $headers = array();
	protected $status = '';
	protected $messages = array();
	protected $body = array();
	protected $statusCode = 200;
	protected $isRedirect = false;
	protected $isModified = true;
	protected $debugRedirects = false;



	public function __construct($url = null) {
		$this->setBaseURL($url);
		
		// setup frame blocking by default, requiring the developer to change this behavior
		$this->setFramePermission();
	}



	/**
	 * Set the baseURL
	 *
	 * This property is not nullable so do not allow it to be unset.
	 *
	 * @param ELSWAK_HTTP_URL|mixed A value to set or parse
	 */
	public function setBaseURL($value) {
		if (!($value instanceof ELSWAK_HTTP_URL)) {
			if ($value) {
				$value = ELSWAK_URI_Factory::uriForString($value);
			}
			if (!($value instanceof ELSWAK_HTTP_URL)) {
				$value = ELSWAK_URI_Factory::applicationURLFromServerGlobal();
			}
			if (!($value instanceof ELSWAK_HTTP_URL)) {
				$value = new ELSWAK_HTTP_URL;
			}
		}
		if ($value instanceof ELSWAK_HTTP_URL) {
			$this->baseURL = $value;
		}
		return $this;
	}
	/**
	 * Provide baseURL while protecting it
	 *
	 * Clone the baseURL before handing it off to prevent unintended
	 * mangling.
	 *
	 * @return ELSWAK_HTTP_URL
	 */
	public function baseURL() {
		return clone $this->baseURL;
	}



	public function serverURI() {
		return $this->baseURL->serverURI();
	}
	public function applicationPath() {
		return $this->baseURL->path();
	}
	public function overrideApplicationPath($path) {
		$this->baseURL->path = pathinfo($path, PATHINFO_DIRNAME).'/';
		return $this;
	}
	public function applicationURI() {
		return $this->baseURL->uri();
	}



	public function setMessages(array $messages) {
		foreach ($messages as $message) {
			$this->addMessage($message);
		}
		return $this;
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
	public function content() {
		// string together the various bits of content
		return implode('', $this->body);
	}
	public function setContent($content = null, $key = null, $type = null) {
		// overwrite all existing content and replace with the supplied content
		$this->body = array();
		return $this->addContent($content, $key, $type);
	}
	public function add($content, $key = null, $type = null) {
		return $this->addContent($content, $key, $type);
	}
	public function addContent($content, $key = null, $type = null) {
		// append content to the body or set/overwrite the value of a given key if provided
		if ($key != null)
			return $this->setContentForKey($content, $key, $type);
		else
			$this->body[] = $this->filterContentByType($content, $type);
		return $this;
	}
	public function set($key, $content = null, $type = null) {
		return $this->setContentForKey($content, $key, $type);
	}
	public function setContentForKey($content, $key, $type = null) {
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
	
	
	
// !Header Methods
	public function headers($delimeter = CRLF) {
		return implode($delimiter, $this->headers);
	}
	public function clearHeaders() {
		$this->headers = array(
			$this->statusLine(),
		);
		return $this;
	}
	public function header($name) {
		if (isset($this->headers[$name]))
			return $this->headers[$name];
		return false;
	}
	/**
	 * Set a header
	 *
	 * Currently this class only supports one value per header (which is
	 * problematic only for minor cases) so the $replace parameter is only
	 * here to ultimately provide it to the PHP header method.
	 *
	 * In fact, I had never needed to send multiples of the same header
	 * before and so hadn't noticed until writing specific unit tests
	 * for this class. I will add support for these someday.
	 *
	 * @param string $name
	 * @param mixed|null $value
	 * @param boolean $replace
	 */
	public function setHeader($name, $value = null, $replace = true) {
		if ($value !== null) {
			$this->headers[$name] = array(
				'name' => str_replace(CRLF, LF, $name),
				'value' => str_replace(CRLF, LF, $value),
				'replace' => (bool) $replace
			);
		} else {
			unset($this->headers[$name]);
		}
		return $this;
	}
	
	
	
	/**
	 * Return custom headers for this class
	 *
	 * Utilize custom headers to transmit items such as messages and statuses.
	 *
	 * @return array
	 */
	public function elswakCustomHeaders() {
		$headers = array();
		if (!empty($this->status)) {
			$headers[] = array(
				   'name' => 'ELSWAK-Status',
				  'value' => $this->status,
				'replace' => true,
			);
		}
		if (count($this->messages) > 0) {
			$headers[] = array(
				   'name' => 'ELSWAK-Messages',
				  'value' => $this->messages('|'),
				'replace' => true,
			);
		}
		return $headers;
	}
	
	
	
// !Redirection Methods
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
	
	
	
// !Modification Flag Methods
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
	
	
	
// !HTTP Status Methods
	public function statusLine() {
		return 'HTTP/1.1 '.$this->statusCode.self::reasonPhraseForStatusCode($this->statusCode);
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
		if ($set)
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
		
		// output the content if modified from the indicators sent by the client
		if ($this->isModified) {
			$this->sendContent();
		}
		return $this;
	}



	/**
	 * Return debug output
	 *
	 * Collect the headers, and content and return them in a string as they
	 * would have been sent to the user. Obviously this can't include any
	 * headers set external to PHP or even this class but if this response
	 * is the only thing producing output on a given request, the output of
	 * this function should be identical to what the client receives.
	 *
	 * @return string
	 */
	public function debugOutput() {
		$output = array();
		
		// process through the headers like the sendHeaders method
		$output[] = $this->statusLine();
		
		// process the headers
		foreach ($this->headers as $header) {
			// unset the redirect if applicable
			if ($this->debugRedirects && strtolower($header['name']) == 'location') {
				// add a message about this if there isn't already one
				$message = 'Redirect to '.$header['value'];
				if (!in_array($message, $this->messages)) {
					$this->addMessage($message);
				}
			} else {
				$output[] = $header['name'].': '.$header['value'];
			}
		}
		
		// process the ELSWAK headers
		foreach ($this->elswakCustomHeaders() as $header) {
			$output[] = $header['name'].': '.$header['value'];
		}

		// include the content if modified from the indicators sent by the client
		if ($this->isModified) {
			return
				implode(CRLF, $output).CRLF.
				CRLF.
				$this->content();
		}
		
		// send the finished headers only
		return
			implode(CRLF, $output).CRLF.
			CRLF;
	}
	
	
	
	protected function canSendHeaders() {
		// check to see if the headers have been sent
		$headersSent = headers_sent($file, $line);
		
		// if the headers have been sent, throw and exception
		if ($headersSent) {
			throw new Exception('Unable to send headers. Headers already sent in ' . $file . ' on line ' . $line);
		}
		return !$headersSent;
	}
	protected function sendHeaders() {
		// collect the headers and output them one at a time
		// send the status line
		header($this->statusLine());
		
		// process the headers
		foreach ($this->headers as $header) {
			// unset the redirect if applicable
			if ($this->debugRedirects && strtolower($header['name']) == 'location') {
				// add a message about this if there isn't already one
				$message = 'Redirect to '.$header['value'];
				if (!in_array($message, $this->messages)) {
					$this->addMessage($message);
				}
			} else {
				header($header['name'].': '.$header['value'], $header['replace']);
			}
		}
		
		// process the ELSWAK headers
		foreach ($this->elswakCustomHeaders() as $header) {
			header($header['name'].': '.$header['value'], $header['replace']);
		}
		
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
