<?php
/**
 * Generic base URL class.
 *
 * URLs are essentially identical to URIs (being a subset) with the various logical constraints and the practical difference that they offer a url() method.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_URL
	extends ELSWAK_URI {
	
	/**
	 * Alias uri()
	 * @return string
	 */
	public function url() {
		return $this->uri();
	}
}