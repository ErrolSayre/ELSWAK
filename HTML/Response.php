<?php
/*
	ELSWAK HTML Response
*/
class ELSWAK_HTML_Response
	extends ELSWAK_HTTP_Response {
	protected $sendHtml = false;
	public function __construct(ELSWAK_HTML_Document $document = null) {
		parent::__construct();
		
		$this->setDocument(
			$document != null?
				$document:
				new ELSWAK_HTML_Document
		);
	}
	public function document() {
		return $this->body;
	}
	public function setDocument(ELSWAK_HTML_Document $document) {
		$this->body = $document;
		return $this;
	}
	public function sendHtml($value = true) {
		if ($value)
			$this->sendHtml = true;
		else
			$this->sendHtml = false;
		return $this;
	}
	public function messages($delimiter = null) {
		return $this->body->messages($delimiter = null);
	}
	public function addMessage($message) {
		$this->body->addMessage($message);
		return $this;
	}
	public function content() {
		if ($this->sendHtml)
			return $this->body->saveHTML();
		return (string) $this->body;
	}
	public function setContent($content = null, $key = null, $type = null) {
		// pass the call on to the document object
		$this->body->setContent($content, $key, $type);
		return $this;
	}
	public function addContent($content, $key = null, $type = null) {
		// pass the call on to the document object
		$this->body->addContent($content, $key, $type);
		return $this;
	}
	public function setContentForKey($key, $content, $type = null) {
		// pass the call on to the document object
		$this->body->setContentForKey($key, $content, $type);
		return $this;
	}
	public function sendCustomHeaders() {
		// override this method since the custom headers are handled within the document
		return $this;
	}
	public function __call($method, array $arguments = null) {
/*
	This magic method is implemented to provide a way for this object to be used as an alias for its (DOMDocument) body member.
*/
		// determine if the method is a method from the document
		if (method_exists($this->body, $method))
			return call_user_func_array(array($this->body, $method), $arguments);
		throw new Exception('Unable to handle method call. “'.$method.'” does not exist in '.__CLASS__.' nor '.get_class($this->body));
	}
}