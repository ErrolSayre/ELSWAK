<?php
//!Stub Constants
if (!defined('JSON_PRETTY_PRINT')) {
	define('JSON_PRETTY_PRINT', 0);
}



//!Related Exception Classes
/**
 * @package ELSWAK\HTTP
 */
class ELSWAK_JSON_ResponseException extends ELSWAK_Exception {}



class ELSWAK_JSON_Response
	extends ELSWAK_HTTP_Response {
	
	protected $jsonEncodeOptions;



	public function __construct(array $content = null, $prettyJson = false) {
		$this->setContentType('application/json');
		$this->setStatus('OK');
		if ($content) {
			$this->setBody($content);
		}
		$this->setPrettyJSON($prettyJson);
	}



	/**
	 * Set the body
	 *
	 * Override the parent, allowing the body property to be set to an
	 * array.
	 */
	public function setBody(array $body) {
		$this->body = $body;
		return $this;
	}
	public function body() {
		return $this->body;
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



	/**
	 * Filter content
	 *
	 * Whereas before this method ensured that the locally stored
	 * representation was pre-encoded JSON strings, we now want to keep
	 * references opting to do the JSON encoding when sending the content.
	 * To ensure backward compatibility, we'll inverse this method to now
	 * make sure items are decoded if possible.
	 */
	protected function filterContentByType($content, $type) {
		// determine if the content is a string
		if (is_string($content) && strtolower($type) == 'json') {
			// determine if the content is valid JSON
			if (($value = json_decode($content, true)) !== null) {
				return $value;
			}
		}
		// since the content is not an acceptable JSON string, encode it at output
		return $content;
	}
	public function content() {
		$content = array(
			  'status' => $this->status,
			'messages' => null,
			    'body' => null,
		);
		
		if (count($this->messages)) {
			$content['messages'] = $this->messages;
		}
		
		if (count($this->body)) {
			$body    = array();
			$unnamed = array();
			
			foreach ($this->body as $key => $value) {
				if (is_integer($key)) {
					$unnamed[] = $value;
				} else {
					$body[$key] = $value;
				}
			}
			if (count($unnamed)) {
				$body['unnamed'] = $unnamed;
			}
			$content['body'] = $body;
		}
		// try to encode the various contents to send on
		$data = json_encode( $content, $this->jsonEncodeOptions );
		if ( $data === false ) {
			throw new ELSWAK_JSON_ResponseException( 'Unable to encode content as JSON. ' . json_last_error_msg( ) );
		}
		return $data;
	}
	public function sendCustomHeaders() {
		// override this method since the custom headers are included in the JSON response
		return $this;
	}
	public function __toString() {
		return $this->content();
	}
}
