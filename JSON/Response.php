<?php
require_once('ELSWebAppKit/HTTP/Response.php');
require_once('ELSWebAppKit/JSON/Translator.php');
class ELSWebAppKit_JSON_Response
	extends ELSWebAppKit_HTTP_Response
{
	public function __construct()
	{
		$this->setContentType('text/javascript');
		$this->setStatus('OK');
	}
	protected function filterContentByType($content, $type)
	{
		if (is_string($content) && strtolower($type) == 'json')
			return json_decode($content, true);
		return $content;
	}
	public function content()
	{
		// construct the json representation of this response
		$json = '';
		$json .= '"status":"'.$this->status.'",';
		if (count($this->messages))
			$json .= '"messages":'.ELSWebAppKit_JSON_Translator::encode($this->messages).',';
		else
			$json .= '"messages":null,';
		if (count($this->body))
			$json .= '"body":'.ELSWebAppKit_JSON_Translator::encode($this->body);
		else
			$json .= '"body":null';
		
		// return the finished json
		return '{'.$json.'}';
	}
	public function sendCustomHeaders()
	{
		// override this method since the custom headers are included in the JSON response
		return $this;
	}
	public function __toString()
	{
		return $this->content();
	}
}
?>