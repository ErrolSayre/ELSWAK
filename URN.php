<?php
/**
 * Base class for URNs.
 *
 * URNs are essentially identical to URIs (being a subset) with the various logical constraints and the practical difference that they offer a urn() method.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_URN
	extends ELSWAK_URI {
	
	protected $pathComponents;
	
	/**
	 * Override to redirect to path
	 * @param string $string hierarchy string
	 * @return ELSWAK_URN self
	 */
	public function setHierarchy($string) {
		return $this->setPath($string);
	}
	public function hierarchy() {
		return $this->path();
	}
	
	
	/**
	 * Override the set path method to break the string into components.
	 * @return ELSWAK_URN self
	 */
	public function setPath($path) {
		return $this->setPathComponents(explode(':', $path));
	}
	public function path() {
		return implode(':', $this->pathComponents());
	}
	public function hasPath() {
		return count($this->pathComponents()) > 0;
	}
	public function setPathComponents(array $pathComponents = null) {
		if (is_array($pathComponents)) {
			$this->pathComponents = array_values($pathComponents);
		} else {
			$this->pathComponents = array();
		}
		return $this;
	}
	public function pathComponents() {
		if (!is_array($this->pathComponents)) {
			$this->pathComponents = array();
		}
		return $this->pathComponents;
	}
	
	/**
	 * Alias uri()
	 * @return string
	 */
	public function urn() {
		return $this->uri();
	}
}