<?php
/*
	ELSWebAppKit HTTP Response
	
	This class was derived from the Zend Controller Response Abstract in order to provide some of the low level HTTP response items (such as headers) to the HTML, XML, and JSON response objects. It also serves as a common ancestor for those classes.
	If you need the advanced features this class is lacking, it is recommended that you use the Zend class.
*/
class ELSWebAppKit_HTTP_Response {
	protected $serverUri;
	protected $applicationPath;
	protected $headers = array();
	protected $status = '';
	protected $messages = array();
	protected $body = array();
	protected $responseCode = 200;
	protected $isRedirect = false;
	protected $isModified = true;
	protected $debugRedirects = false;
	
	public function __construct() {
		// setup the server uri
		$this->serverUri = ((isset($_SERVER['HTTPS']))? 'https://': 'http://').$_SERVER['HTTP_HOST'];
		
		// setup the application uri
		$this->applicationPath = dirname($_SERVER['PHP_SELF']);
	}
	public function serverUri() {
		return $this->serverUri;
	}
	public function applicationPath() {
		return $this->applicationPath;
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
		if ($content !== null) {
			if ($key !== null)
				$this->body[$key] = $this->filterContentByType($content, $type);
			else
				$this->body[] = $this->filterContentByType($content, $type);
		}
		return $this;
	}
	public function addContent($content, $key = null, $type = null) {
		// append content to the body or set/overwrite the value of a given key if provided
		if ($key !== null)
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
	public function setRedirect($url, $code = 307) {
		// since this method will predominately be used for redirecting users to authenticate, make the default code 307
		// set the location header using the given url
		$this->setHeader('Location', $url, true)
			->setResponseCode($code);
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
			$this->setResponseCode(304);
			$this->isModified = false;
		}
		else {
			if ($this->responseCode == 304)
				$this->setResponseCode(200);
			$this->isModified = true;
		}
		return $this;
	}
	public function responseCode() {
		return $this->responseCode;
	}
	public function setResponseCode($code) {
		if (!is_int($code) || (100 > $code) || (599 < $code))
			throw new Exception('Invalid HTTP response code.');
		
		// determine if this code corresponds to a redirect
		if ((300 <= $code) && (307 >= $code))
			$this->isRedirect = true;
		else
			$this->isRedirect = false;
		$this->responseCode = $code;
		return $this;
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
		// send the header code
		header('HTTP/1.1 '.$this->responseCode);
		
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
			header('ELSWebAppKit-Status: '.$this->status, true);
		if (count($this->messages) > 0)
			header('ELSWebAppKit-Messages: '.$this->messages('|'), true);
		return $this;
	}
	public function sendContent() {
		echo $this->content();
		return $this;
	}
}
