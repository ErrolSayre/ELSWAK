<?php
/**
 * Exception for HTTP URLs
 * @package ELSWAK
 */
class ELSWAK_HTTP_URL_Exception extends ELSWAK_Exception {}

/**
 * Exception for invalid scheme type
 * @package ELSWAK
 */
class ELSWAK_HTTP_URL_InvalidScheme_Exception extends ELSWAK_HTTP_URL_Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if (!$message) {
			$message = 'Scheme must be either blank or one of "http" or "https".';
		}
		return parent::__construct($message, $code, $previous);
	}
}

/**
 * HTTP URLs are identical to authoritative URLs with the exception that they will validate the scheme as emtpy, "http", or "https".
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_HTTP_URL
	extends ELSWAK_Authoritative_URL {
	
	public function setScheme($scheme) {
		// ensure the scheme is an acceptable type
		$scheme = strtolower($scheme);
		if ($scheme == null || $scheme == 'http' || $scheme == 'https') {
			$this->scheme = $scheme;
			return $this;
		}
		throw new ELSWAK_HTTP_URL_InvalidScheme_Exception;
	}
	public function isSecure() {
		if ( $this->scheme == 'https' ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Override to support server relative URLs
	 * @param boolean $relative
	 * @return string
	 */
	public function uri($relative = false) {
		// reassemble the various components as a complete URI
		$uri = '';
		if (!$relative) {
			if ($this->scheme) {
				$uri = $this->scheme().':';
			}
			if ($this->hasAuthority()) {
				$uri .= '//'.$this->authority();
			}
		}
		if ($this->hasPath()) {
			$uri .= $this->path();
		}
		if ($this->hasQuery()) {
			$uri .= '?'.$this->query();
		}
		if ($this->hasFragment()) {
			$uri .= '#'.$this->fragment();
		}
		return $uri;
	}
	/**
	 * Override to support server relative URLs
	 * @param boolean $relative
	 * @return string
	 */
	public function url($relative = false) {
		return $this->uri($relative);
	}
}
