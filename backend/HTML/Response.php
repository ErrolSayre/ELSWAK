<?php
/*
	ELSWebAppKit HTML Response
	
	This class is an extension of the Zend Controller Response Abstract that has been targeted at producing an (X)HTML based response utilizing the PHP DOM extension.
*/
require_once('ELSWebAppKit/HTML/Document.php');
require_once('Zend/Controller/Response/Abstract.php');
class ELSWebAppKit_HTML_Response
	extends Zend_Controller_Response_Abstract
{
	// the DOM document
	protected $document;
	protected $namedBodyElementIndex;
	
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
	
	// in order to properly output our response, we need to collect all the extraneous bits of the "body" of the response and append them to our DOM before outputting the DOM
	// override the sendResponse method to collect the body content into the DOM document's "console".
	public function sendResponse()
	{
		// send the headers
		$this->sendHeaders();
		
		// determine if the document has a console
		if (($console = $this->document->locateElementById('PageConsole')) == null)
		{
			// create a page console
			$console = $this->document->body()->appendChild($this->document->createElement('div'));
			$console->setAttribute('id', 'PageConsole');
		}
		
		// remove any existing content in the console
		while ($console->hasChildNodes())
		{
			$console->removeChild($console->firstChild);
		}
		
		// determine if the client has requested exceptions to be rendered in the output
		if ($this->isException() && $this->renderExceptions())
		{
			foreach ($this->getException() as $e)
			{
				// create a new container for this exception
				$console->appendChild($this->document->createElement('div', $e->__toString()));
			}
		}
		
		// collect the body output and add it to our console as well
		foreach ($this->_body as $content)
		{
			// create a new container for this content
			$console->appendChild($this->document->createElement('div', $content));
		}
	}
}