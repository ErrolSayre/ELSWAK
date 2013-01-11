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