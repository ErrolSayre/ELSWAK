<?php
/*
	ELSWebAppKit HTML Response
*/
require_once('ELSWebAppKit/HTML/Document.php');
require_once('ELSWebAppKit/HTTP/Response.php');
class ELSWebAppKit_HTML_Response
	extends ELSWebAppKit_HTTP_Response
{
	// the DOM document
	protected $document;
	
	public function __construct(ELSWebAppKit_HTML_Document $document = null)
	{
		parent::__construct();
		
		$this->setDocument
		(
			($document !== null)?
				$document:
				new ELSWebAppKit_HTML_Document()
		);
	}
	public function document()
	{
		return $this->document;
	}
	public function setDocument(ELSWebAppKit_HTML_Document $document)
	{
		$this->document = $document;
		return $this;
	}
	public function addContent($content)
	{
		return $this->document->addContent($content);
	}
	public function addMessage($message)
	{
		return $this->document->addMessage($message);
	}
	public function status()
	{
		// this is a placeholder method to provide error free interchangeability with the JSON response until the HTML document has a means to support a status
		return true;
	}
	public function setStatus($status)
	{
		// this is a placeholder method
		return $this;
	}
	public function payload()
	{
		// this is a placeholder method
		return true;
	}
	public function setPayload($payload)
	{
		// this is a placeholder method
		return $this->document->addMessage($this->document->debugDumpVariable($payload, 'Payload'));
	}
	public function sendBody()
	{
		// determine if this is a redirect
		if (!$this->isRedirect())
		{
			// dump the document content
			echo $this->document;
		}
	}
}