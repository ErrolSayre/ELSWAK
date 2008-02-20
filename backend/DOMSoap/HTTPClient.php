<?php
/*
	ELSWebAppKit DOMSoap HTTP Client
	
	This class was built to provide a very simple client for consuming soap services. It accepts a DOMDocument as it's payload (which should be a properly formed SOAP request) and can handle basic HTTP authentication. It will return the response XML as a string.
*/

// setup helpful constants
if (!defined('LF')) define('LF', "\n");
if (!defined('CRLF')) define('CRLF', "\r\n");

class ELSWebAppKit_DOMSoap_HTTPClient
{
	protected $host;
	protected $port;
	protected $resource;
	protected $authentication;
	protected $username;
	protected $password;
	protected $nameSpaceUri;
	
	function __construct($host, $port, $resource, $nameSpaceUri = 'http://localhost.localdomain/', $useSSL = false, $authentication = 'none', $username = null, $password = null)
	{
		$this->host = $host;
		$this->port = intval($port);
		$this->resource = $resource;
		$this->nameSpaceUri = $nameSpaceUri;
		if (strtolower($authentication) == 'basic')
		{
			$this->authentication = 'Basic';
			$this->username = $username;
			$this->password = $password;
		}
		$this->useSSL = ($useSSL)?	true:	false;
	}
	function makeRequest($soapXML)
	{
		// set up the response
		$responseXML = '';
		
		// determine if we should use ssl
		$connectHost = $this->host;
		if ($this->useSSL)
		{
			$connectHost = 'ssl://'.$this->host;
		}
		
		// open a connection to the remote location and send the data
		if (($httpConnection = fsockopen($connectHost, $this->port, $errorNumber, $errorString, 30)) !== false)
		{
			// set up the request content
			$requestContent = 'POST '.$this->resource.' HTTP/1.1'.CRLF;
			$requestContent .= 'Host: '.$this->host.CRLF;
			
			// add authentication if needed
			if ($this->authentication == 'Basic')
			{
				$requestContent .= 'Authorization: Basic '.base64_encode($this->username.':'.$this->password).CRLF;
			}
			
			// add the metadata
			$requestContent .= 'Content-Type: text/xml; charset="UTF-8"'.CRLF;
			$requestContent .= 'Content-Length: '.strlen($soapXML).CRLF;
			
			// add the soap headers
			$requestContent .= 'SOAPAction: '.$this->nameSpaceUri.CRLF;
			
			// add the soap content
			$requestContent .= CRLF.$soapXML;
			fputs($httpConnection, $requestContent, strlen($requestContent));
			
			// read the response content
			$responseContent = '';
			// suppress php warnings using an output buffer
			ob_start();
			while (!feof($httpConnection))
			{
				$responseContent .= fgets($httpConnection, 100);
			}
			// turn off the output buffer
			ob_end_clean();
			
			// close the connection
			fclose($httpConnection);
			
			// extract the XML from the response
			$responseXML = substr($responseContent, strpos($responseContent, CRLF.CRLF));
		}
		else
		{
			throw new Exception('Request Failed: '.$errorNumber.': '.$errorString);
		}
		
		// return our result
		return $responseXML;
	}
}
?>