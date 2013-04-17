<?php
//!Stub Constants
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 0);
}



class ELSWAK_JSON_Response
	extends ELSWAK_HTTP_Response {
	
	protected $jsonEncodeOptions;



	public function __construct($prettyJson = false) {
		$this->setContentType('text/javascript');
		$this->setStatus('OK');
		$this->setPrettyJSON($prettyJson);
	}



	public function setJSONEncodeOptions($value) {
		$this->jsonEncodeOptions = (integer) $value;
	}
	public function setPrettyJSON($value) {
		if (ELSWAK_Boolean::valueAsBoolean($value)) {
			$this->jsonEncodeOptions = JSON_PRETTY_PRINT;
		}
		return $this;
	}



	protected function filterContentByType($content, $type) {
		// determine if the content is a string
		if (is_string($content) && strtolower($type) == 'json') {
			// determine if the content is valid JSON
			if (json_decode($content, true) !== null) {
				return $content;
			}
		}
		// since the content is not an acceptable JSON string, encode it
		return json_encode($content, $this->jsonEncodeOptions);
	}
	public function content() {
		// construct the json representation of this response
		$json = '';
		$json .= '"status":"'.$this->status.'",';
		if (count($this->messages))
			$json .= '"messages":'.json_encode($this->messages, $this->jsonEncodeOptions).',';
		else
			$json .= '"messages":null,';
		if (count($this->body)) {
			$body = '';
			$unnamed = array();
			foreach ($this->body as $key => $value) {
				if (is_integer($key)) {
					$unnamed[] = $value;
				} else {
					$body .= '"'.$key.'":'.$value.',';
				}
			}
			if (count($unnamed) > 0) {
				$body .= '"unnamed":[';
				foreach ($unnamed as $value) {
					$body .= $value.',';
				}
				$body .= '],';
			}
			$json .= '"body":{'.substr($body, 0, -1).'}';
		} else {
			$json .= '"body":null';
		}
		// return the finished json
		return '{'.$json.'}';
	}
	public function sendCustomHeaders() {
		// override this method since the custom headers are included in the JSON response
		return $this;
	}
	public function __toString() {
		return $this->content();
	}
}
