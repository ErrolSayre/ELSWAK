<?php
/**
 * Wrap a primitive in an object.
 *
 * Mostly this class exists for the "parsing" functions, in order to
 * pull the methods out of the settable class.
 * @package ELSWAK
 */
class ELSWAK_Integer {
	protected $value;
	
	public function __construct($value = 0) {
		$this->setValue($value);
	}
	
	public function setValue($value) {
		$this->value = $this->integerForValue($value);
	}
	
	public function __toString() {
		return $this->value;
	}
	
	public static function integerForValue($value) {
		return intval($value);
	}
	public static function positiveIntegerForValue($value) {
		$int = self::integerForValue($value);
		if ($int >= 0) {
			return $int;
		}
		return 0;
	}
}