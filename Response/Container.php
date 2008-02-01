<?php
/*
	ELSWebAppKit HTML Response Container
	
	This class is an extension of the Zend Controller Response Abstract that has been targeted at producing an (X)HTML based response utilizing the PHP DOM extension.
*/
require_once('ELSWebAppKit/HTML/Document.php');
require_once('Zend/Controller/Response/Abstract.php');
class ELSWebAppKit_HTML_Response_Container
	extends Zend_Controller_Response_Abstract
{
	// the DOM document
	protected $document;
	
	public function __construct(ELSWebAppKit_HTML_Document $document = null)
	{
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
	public function getDocument()
	{
		return $this->document();
	}
	public function setDocument(ELSWebAppKit_HTML_Document $document)
	{
		$this->document = $document;
		return $this;
	}
	public function setBody($content, $name = null)
	{
		// remove the existing content in the body tag of the html dom and replace it with the DOM tree provided
		while ($this->document->body()->hasChildNodes())
		{
			$this->document->body()->removeChild($this->
		}
	}
}