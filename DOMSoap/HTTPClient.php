<?php
/*
	ELSWebAppKit DOMSoap HTTP Client
	
	This class was built to provide a very simple client for consuming soap services. It accepts a DOMDocument as it's payload (which should be a properly formed SOAP request) and can handle basic HTTP authentication. It will return the response XML as a string.
*/

// setup helpful constants
if (!defined('LF')) define('LF', "\n");
if (!defined('CRLF')) define('CRLF', "\r\n");

class ELSWebAppKit_DOMSoap_HTTPClient {
	protected $host;
	protected $port;
	protected $resource;
	protected $authentication;
	protected $username;
	protected $password;
	protected $namespaceUri;
	
	function __construct($host, $port, $resource, $namespaceUri = 'http://localhost.localdomain/', $useSSL = false, $authentication = 'none', $username = null, $password = null) {
		$this->host = $host;
		$this->port = intval($port);
		$this->resource = $resource;
		$this->namespaceUri = $namespaceUri;
		if (strtolower($authentication) == 'basic') {
			$this->authentication = 'Basic';
			$this->username = $username;
			$this->password = $password;
		}
		$this->useSSL = ($useSSL)?	true:	false;
	}
	public function uri() {
		return ($this->useSSL? 'https': 'http').'://'.$this->host.':'.$this->port.$this->resource;
	}
	public function namespaceUri() {
		return $this->namespaceUri;
	}
	function makeRequest($soapXML, $rawResponse = false) {
$transcript = 'Making request to '.$this->uri().LF.LF;
$sStart = microtime(true);
		// set up the response
		$responseContent = '';
		$responseXML = '';
		
		// determine if we should use ssl
		$connectHost = $this->host;
		if ($this->useSSL) {
			$connectHost = 'ssl://'.$this->host;
		}
		
		// open a connection to the remote location and send the data
		if (($httpConnection = fsockopen($connectHost, $this->port, $errorNumber, $errorString, 30)) !== false) {
			// set up the request content
			$requestContent = 'POST '.$this->resource.' HTTP/1.1'.CRLF;
			$requestContent .= 'Host: '.$this->host.CRLF;
			
			// add authentication if needed
			if ($this->authentication == 'Basic') {
				$requestContent .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password).CRLF;
			}
			
			// add the connection close instruction
			$requestContent .= 'Connection: close'.CRLF;
			
			// add the metadata
			$requestContent .= 'Content-Type: text/xml; charset="UTF-8"'.CRLF;
			$requestContent .= 'Content-Length: '.strlen($soapXML).CRLF;
			
			// add the soap headers
			$requestContent .= 'SOAPAction: '.$this->namespaceUri.CRLF;
			
			// add the soap content
			$requestContent .= CRLF.$soapXML;
			fputs($httpConnection, $requestContent, strlen($requestContent));
			
			// read the response content
			// suppress php warnings using an output buffer
			ob_start();
			while (!feof($httpConnection)) {
$start = microtime(true);
				$line = fgets($httpConnection);
$end = microtime(true);
$transcript .= formatSeconds($end - $start).formatLine($line);
				$responseContent .= $line;
			}
			// turn off the output buffer
			ob_end_clean();
			
			// close the connection
			fclose($httpConnection);
$sEnd = microtime(true);
$transcript .= LF.LF.'Completed transaction in '.($sEnd - $sStart).'s';
$transcript .= LF.LF.'Request:'.LF.$requestContent.LF;
error_log($transcript, 1, 'database@research.olemiss.edu');
			
			// extract the XML from the response
			$responseXML = substr($responseContent, strpos($responseContent, CRLF.CRLF) + strlen(CRLF.CRLF));
		} else {
			throw new Exception('Request Failed: '.$errorNumber.': '.$errorString);
		}
		
		// return our result
		if ($rawResponse)
			return $responseContent;
		return $responseXML;
	}
}
function formatSeconds($time) {
	if ($time < .001) {
		$time = sprintf('%.5f', $time * 1000).'ms: ';
	} else {
		$time = sprintf('%.5f', $time).'s : ';
	}
	return str_pad($time, 14, ' ', STR_PAD_LEFT);
}
function formatLine($line) {
	$line = str_replace(array(LF, CR, CRLF), '', $line);
	return $line.LF;
}