<?php
/**
 * Base class for URLs/URNs.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_URI
	extends ELSWAK_Object {
	
	protected $scheme;
	protected $hierarchy;
	protected $queryComponents;
	protected $fragment;
	
	/**
	 * Set the query virtual property
	 *
	 * The query property doesn't actually exist but is rather a construct of the ELSWAK_Object's ability to map property access to accessor methods.
	 * @param string query string
	 * @param string key/value pair delimiter
	 * @return ELSWAK_URI self
	 */
	public function setQuery($string, $delimiter = '&') {
		// break the string into components
		$this->setQueryComponents();
		$couplets = explode($delimiter, $string);
		foreach ($couplets as $couplet) {
			list($key, $value) = explode('=', $couplet.'=');
			$this->queryComponents->set(urldecode($key), urldecode($value));
		}
		return $this;
	}
	public function query() {
		// reassemble the components into a query string
		return http_build_query($this->queryComponents()->store);
	}
	public function hasQuery() {
		return $this->queryComponents()->hasItems();
	}
	public function setQueryComponents(ELSWAK_Dictionary $list = null) {
		if (!($list instanceof ELSWAK_Dictionary)) {
			$list = new ELSWAK_Dictionary;
		}
		$this->queryComponents = $list;
		return $this;
	}
	public function queryComponents() {
		// utilize lazy instantiation
		if (!($this->queryComponents instanceof ELSWAK_Dictionary)) {
			$this->queryComponents = new ELSWAK_Dictionary;
		}
		return $this->queryComponents;
	}
	
	public function hasFragment() {
		if ($this->fragment) {
			return true;
		}
		return false;
	}
	public function uri() {
		// assemble the basic components
		$uri = '';
		if ($this->scheme) {
			$uri = $this->scheme().':';
		}
		$uri .= $this->hierarchy();
		if ($this->hasQuery()) {
			$uri .= '?'.$this->query();
		}
		if ($this->hasFragment()) {
			$uri .= '#'.$this->fragment();
		}
		return $uri;
	}
	public function __toString() {
		return $this->uri();
	}
}