<?php
class ELSWAK_XML_Document
	extends DOMDocument {
	protected $statusNode;
	protected $messagesNode;
	protected $payloadNode;
	
	public function __construct($templateFile = null) {
		// create the DOMDocument
		parent::__construct();
		
		// create the container element
		$container = $this->appendChild($this->createElement('ELSWAKResponse'));
		
		// setup references to the main elements
		$this->statusNode = $container->appendChild(createElement('status'));
		$this->messagesNode = $container->appendChild(createElement('messages'));
		$this->payloadNode = $container->appendChild(createElement('payload'));
	}
	public function type() {
		return 'xml';
	}
	public function output() {
		echo $this->saveXML();
	}
	public function status() {
		return $this->statusNode->textContent;
	}
	public function setStatus($status = 'OK') {
		// remove the current status
		while ($this->statusNode->hasChildNodes()) {
			$this->statusNode->removeChild($this->statusNode->firstChild);
		}
		$this->statusNode->appendChild($this->createTextNode($status));
	}
	public function addMessage($message) {
		if ($message instanceof DOMElement) {
			if ($message->tagName == 'message') {
				$this->messagesNode->appendChild($message);
			} else {
				$this->messagesNode->appendChild($this->createElement('message', $message));
			}
		} else {
			$this->messagesNode->appendChild($this->createElement('message', $message));
		}
	}
	public function clearMessages() {
		// remove the current messages
		while ($this->messagesNode->hasChildNodes()) {
			$this->messagesNode->removeChild($this->messagesNode->firstChild);
		}
	}
	public function payload() {
		return $this->payloadNode->firstChild;
	}
	public function setPayload(DOMElement $element) {
		// remove the current payload
		while ($this->payloadNode->hasChildNodes()) {
			$this->payloadNode->removeChild($this->payloadNode->firstChild);
		}
		$this->payloadNode->appendChild($element);
	}
}
