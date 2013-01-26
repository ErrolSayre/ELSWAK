<?php
/**
 * ELSWAK Boolean
 *
 * @author Errol Sayre
 */

/**
 * Represent a boolean
 * @package ELSWAK
 */
class ELSWAK_Boolean {
	protected $value;
	
	public function __construct($value = null) {
		$this->setValue($value);
	}
	public function setValue($value) {
		$this->value = $this->valueAsBoolean($value);
		return $this;
	}
	public function value() {
		return $this->value;
	}
	public function label($trueLabel = 'yes', $falseLabel = 'no') {
		if ($this->value) {
			return $trueLabel;
		}
		return $falseLabel;
	}
	public function __toString() {
		return $this->label();
	}
	public function __set($property, $value) {
		$this->setValue($value);
		return $this;
	}
	public function __get($property) {
		if (strtolower($property) == 'label') {
			return $this->label();
		}
		return $this->value;
	}



	public static function valueAsBoolean($value) {
		// look for potential string matches
		if (is_string($value)) {
			$value = strtolower(trim($value));
			if (in_array($value, self::acceptableTrueValues())) {
				return true;
			} else {
				return false;
			}
		}
		// look for a boolean masquerading as a number
		if (is_numeric($value)) {
			if ($value > 0) {
				return true;
			} else {
				return false;
			}
		}
		// perform one last casting using PHP's rules
		if ($value) {
			return true;
		}
		return false;
	}
	public static function acceptableTrueValues() {
		// these values should be listed in order of decreasing likelihood to match in order to minimize comparisons
		return array(
			'yes',
			'y',
			'true',
			'x', // SAP style boolean
		);
	}
	public static function acceptableFalseValues() {
		// please note that in the case of valueAsBoolean and valueAsNullBoolean ALL strings that are not null or true matches are considered false
		// these values should be listed in order of decreasing likelihood to match in order to minimize comparisons
		return array(
			'no',
			'n',
			' ', // SAP style boolean
			'',
			'none',
			'not',
		);
	}
}