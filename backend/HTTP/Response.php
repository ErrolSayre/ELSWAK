<?php
/*
	ELSWebAppKit HTTP Response
	
	This class was derived from the Zend Controller Response Abstract in order to provide some of the low level HTTP response items (such as headers) to the HTML, XML, and JSON response objects. It also serves as a common ancestor for those classes.
	If you need the advanced features this class is lacking, it is recommended that you use the Zend class.
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_HTTP_Response
	extends ELSWebAppKit_Iterable
{
	protected $serverUri;
	protected $applicationPath;
	protected $headers = array();
	protected $rawHeaders = array();
	protected $responseCode = 200;
	protected $isRedirect = false;
	protected $body = '';
	
	public function __construct()
	{
		// setup the server uri
		$this->serverUri = ((isset($_SERVER['HTTPS']))? 'https://': 'http://').$_SERVER['HTTP_HOST'];
		
		// setup the application uri
		$this->applicationPath = dirname($_SERVER['PHP_SELF']).'/';
	}
	public function serverUri()
	{
		return $this->serverUri;
	}
	public function applicationPath()
	{
		return $this->applicationPath;
	}
	public function applicationUri()
	{
		return $this->serverUri.$this->applicationPath;
	}
	public function headers()
	{
		return $this->headers;
	}
	public function setHeader($name, $value, $replace = false)
	{
		// check to see if we can send headers
		$this->canSendHeaders();
		
		// cleanup the inputs
		$name = (string) $name;
		$value = (string) $value;

		if ($replace)
		{
			// remove any current headers matching this name
			foreach ($this->headers as $key => $header)
			{
				if ($name == $header['name'])
				{
					unset($this->headers[$key]);
				}
			}
		}
		
		// add the header to the list
		$this->headers[] = array
		(
			'name'	=> $name,
			'value'   => $value,
			'replace' => $replace
		);
		return $this;
	}
	public function setRedirect($url, $code = 302)
	{
		// check to see if we can send headers
		$this->canSendHeaders();
		
		// set the location header using the given url
		$this->setHeader('Location', $url, true)
			->setResponseCode($code);
		return $this;
	}
	public function isRedirect()
	{
		return $this->isRedirect;
	}
	public function clearHeaders()
	{
		$this->headers = array();
		return $this;
	}
	public function rawHeaders()
	{
		return $this->rawHeaders;
	}
	public function setRawHeader($value)
	{
		$this->canSendHeaders();
		if ('Location' == substr($value, 0, 8))
		{
			$this->isRedirect = true;
		}
		$this->rawHeaders[] = (string) $value;
		return $this;
	}
	public function clearRawHeaders()
	{
		$this->rawHeaders = array();
		return $this;
	}
	public function clearAllHeaders()
	{
		return $this->clearHeaders()
					->clearRawHeaders();
	}
	public function responseCode()
	{
		return $this->responseCode;
	}
	public function setResponseCode($code)
	{
		if (!is_int($code) || (100 > $code) || (599 < $code))
		{
			throw new Exception('Invalid HTTP response code.');
		}
		
		// determine if this code corresponds to a redirect
		if ((300 <= $code) && (307 >= $code))
		{
			$this->isRedirect = true;
		}
		else
		{
			$this->isRedirect = false;
		}
		$this->responseCode = $code;
		return $this;
	}
	public function canSendHeaders()
	{
		// check to see if the headers have been sent
		$headersSent = headers_sent($file, $line);
		
		// if the headers have been sent, throw and exception
		if ($headersSent)
		{
			throw new Exception('Unable to send headers. Headers already sent in ' . $file . ' on line ' . $line);
		}
		return !$headersSent;
	}
	public function sendHeaders()
	{
		// Only check if we can send headers if we have headers to send
		if (count($this->rawHeaders) || count($this->headers) || (200 != $this->responseCode))
		{
			$this->canSendHeaders();
		}
		else if (200 == $this->responseCode)
		{
			return $this;
		}
		
		// make sure we only send the response code once
		$httpCodeSent = false;
		
		// process the raw headers
		foreach ($this->rawHeaders as $header)
		{
			if (!$httpCodeSent && $this->responseCode)
			{
				header($header, true, $this->responseCode);
				$httpCodeSent = true;
			}
			else
			{
				header($header);
			}
		}
		
		// process the named headers
		foreach ($this->headers as $header)
		{
			if (!$httpCodeSent && $this->responseCode)
			{
				header($header['name'].': '.$header['value'], $header['replace'], $this->responseCode);
				$httpCodeSent = true;
			}
			else
			{
				header($header['name']. ': '.$header['value'], $header['replace']);
			}
		}
		
		// determine if the response code was sent with another header
		if (!$httpCodeSent)
		{
			// send it
			header('HTTP/1.1 ' . $this->responseCode);
			$httpCodeSent = true;
		}
		return $this;
	}
	public function addContent($content)
	{
		$this->body .= $content;
		return $this;
	}
	public function addMessage($message)
	{
		$this->body .= $message;
		return $this;
	}
	public function sendBody()
	{
		echo $this->body;
		return $this;
	}
	public function send()
	{
		// send the headers
		$this->sendHeaders();
		
		// output the body
		$this->sendBody();
		return $this;
	}
}
