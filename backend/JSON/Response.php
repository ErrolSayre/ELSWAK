<?php
require_once('ELSWebAppKit/HTTP/Response.php');
require_once('ELSWebAppKit/JSON/Translator.php');
class ELSWebAppKit_JSON_Response
	extends ELSWebAppKit_HTTP_Response
{
	protected $status;
	protected $messages;
	protected $payload;
	
	// member listing for iterator methods
	protected $_iterables = array
	(
		'status',
		'messages',
		'payload'
	);
	
	public function __construct($status = 'OK', array $messages = null, $payload = null)
	{
		$this->setHeader('Content-Type', 'text/javascript', true);
		$this->setStatus($status);
		if ($messages != null)
		{
			foreach ($messages as $message)
			{
				$this->addMessage($message);
			}
		}
		$this->setPayload($payload);
	}
	public function type()
	{
		return 'json';
	}
	public function status()
	{
		return $this->status;
	}
	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}
	public function messages()
	{
		return implode(LF, $this->messages);
	}
	public function addMessage($message)
	{
		$this->messages[] = trim($message);
		return $this;
	}
	public function payload()
	{
		return $this->payload;
	}
	public function setPayload($payload)
	{
		$this->payload = $payload;
		return $this;
	}
	public function sendBody()
	{
		echo $this;
	}
	public function __toString()
	{
		// construct the json representation of this response
		$json = '';
		$json .= '"status":"'.$this->status.'",';
		$json .= '"messages":'.ELSWebAppKit_JSON_Translator::encode($this->messages).',';
		
		// determine if the payload is a string
		if (is_string($this->payload))
			$json .= '"payload":'.$this->payload;
		else
			$json .= '"payload":'.ELSWebAppKit_JSON_Translator::encode($this->payload);
		
		// return the finished json
		return '{'.$json.'}';
	}
}
?>