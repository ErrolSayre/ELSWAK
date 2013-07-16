<?php
/**
 * Wrap a value in a gettable object
 */
class ELSWAK_Gettable_Wrapper
	implements ELSWAK_Gettable {

	protected $value;
	
	public function __construct($value) {
		if (is_array($value)) {
			$value = implode(', ', $value);
		}
		elseif (is_object($value)) {
			if (method_exists($value, '__toString')) {
				$value = (string) $value;
			}
			else {
				$value = '';
			}
		}
		$this->value = (string) $value;
	}
	public function get($property = null) {
		return $this->value;
	}
	public function __toString() {
		return $this->value;
	}
}