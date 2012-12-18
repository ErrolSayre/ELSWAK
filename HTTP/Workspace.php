<?php
/*
	ELSWAK HTTP Workspace
	
	This class provides a common workspace to collect items for assembly into an HTTP response but could also represent an HTTP request. It is designed to collect body contents (discreet or multi-part), headers, and other meta-data for passing among controllers. Essentially a key value store, this class facilitates passing collected data rather than formatted output in order to be more efficient and flexible. This allows the work that goes into creating an HTML and equivalent JSON response to be cached once and later formatted appropriately for each output/response type.
	
	The data collected in this workspace is in turn passed to appropriate formatters (HTML writers, etc.) for placement into an HTTP Response or Request object, or to direct output without further buffering.
*/

class ELSWAK_HTTP_Workspace_Exception extends ELSWAK_Exception {}

class ELSWAK_HTTP_Workspace
	extends ELSWAK_Settable {

// !Instance Properties
	protected $headers;
	protected $content;
	protected $metadata;
	
	
	
// !Constructor
	public function __construct(array $content = null, array $headers = null) {
		$this->setHeaders($headers);
		$this->setContents($content);
		$this->setMetadata(null);
	}
	
	
	
// !Header Accessors
/*
	Note regarding arguments order.
	
	In the case of headers, it is sensible to place the key before the value as it mirrors the structure of headers (field-name ":" [ field-value ] from http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2).
	In the case of keyed content, however, it makes more sense to order as value, key since the key can optionally be omitted.
*/
	public function setHeaders(array $value = null) {
		// reset the headers dictionary only if the value is null
		if (!$this->headers || $value === null) {
			$this->headers = new ELSWAK_Dictionary;
		}
		// import any importable values
		$this->headers->import($value);
		return $this;
	}
	public function addHeader($key, $value) {
		$this->headers->add($value, $key);
		return $this;
	}
	public function setHeader($key, $value) {
		return $this->setHeaderForKey($value, $key);
	}
	public function setHeaderForKey($value, $key) {
		$this->headers->set($key, $value);
		return $this;
	}
	
	public function hasHeader($key) {
		return $this->headers->hasValueForKey($key);
	}
	public function header($key = null) {
		return $this->headers->get($key);
	}
	public function headerForKey($key) {
		return $this->headers->valueForKey($key);
	}
	public function removeHeader($key) {
		return $this->removeHeaderForKey($key);
	}
	public function removeHeaderForKey($key) {
		return $this->headers->remove($key);
	}
	
	

// !Content Accessors
	public function setContents(array $value = null) {
		// reset the contents dictionary only if the value is null
		if (!$this->content || $value === null) {
			$this->content = new ELSWAK_Dictionary(null, 'Content-');
		}
		// import any importable values
		$this->content->import($value);
		return $this;
	}
	public function addContent($value, $key = null) {
		$this->content->add($value, $key);
		return $this;
	}
	public function setContent($value, $key) {
		return $this->setContentForKey($value, $key);
	}
	public function setContentForKey($value, $key) {
		$this->content->set($key, $value);
		return $this;
	}
	
	public function hasContent($key = null) {
		if ($key === null) {
			return $this->content->hasItems();
		}
		return $this->content->hasValueForKey($key);
	}
	public function hasContentForKey($key) {
		return $this->headers->hasValueForKey($key);
	}
	public function content($key = null) {
		return $this->content->get($key);
	}
	public function contentForKey($key) {
		return $this->content->valueForKey($key);
	}
	public function removeContent($key) {
		return $this->removeContentForKey($key);
	}
	public function removeContentForKey($key) {
		return $this->content->remove($key);
	}
	
	
	
//!Metadata Accessors
/*
	The metadata attribute (at this time) is not as full-fledged as the other properties so it doesn't get the various alias methods.
*/
	public function setMetadata($value = null) {
		if (!$this->metadata || $value === null) {
			$this->metadata = new ELSWAK_Dictionary;
		}
		$this->metadata->import($value);
		return $this;
	}
}