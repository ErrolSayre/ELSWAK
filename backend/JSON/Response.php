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
	protected $members = array
	(
		'status',
		'messages',
		'payload'
	);
	
	public function __construct($status = 'OK', array $messages = null, $payload = null)
	{
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
	}
	public function messages()
	{
		return implode(LF, $this->messages);
	}
	public function addMessage($message)
	{
		$this->messages[] = trim($message);
	}
	public function payload()
	{
		return $this->payload;
	}
	public function setPayload($payload)
	{
		$this->payload = $payload;
	}
	public function sendBody()
	{
		echo ELSWebAppKit_JSON_Translator::encode($this);
	}
	public function __toString()
	{
		return ELSWebAppKit_JSON_Translator::encode($this);
	}
}
?>