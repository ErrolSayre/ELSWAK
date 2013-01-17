<?php
/**
 * Represent a mailto link as components.
 * @author Errol Sayre
 * @package ELSWAK
 */
class ELSWAK_Email_URL
	extends ELSWAK_URL {
	
	protected $user;
	protected $host;
	
	public function __construct($import = null) {
		parent::__construct($import);
		$this->setScheme();
	}
	/**
	 * Force the scheme to be mailto regardless of selection.
	 */
	public function setScheme($value = null) {
		$this->scheme = 'mailto';
		return $this;
	}
	public function hierarchy() {
		return $this->user.'@'.$this->host;
	}
	public function setPath($path) {
		list($user, $host) = explode('@', $path.'@');
		$this->user = $user;
		$this->host = $host;
		return $this;
	}
	public function path() {
		return $this->hierarchy();
	}
	public function setAddress($address) {
		return $this->setPath($address);
	}
	public function address() {
		return $this->hierarchy();
	}
}