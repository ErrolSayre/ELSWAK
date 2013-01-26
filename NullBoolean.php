<?php
/**
 * ELSWAK Null Boolean
 *
 * @author Errol Sayre
 */

/**
 * Represent a boolean with the option of being unset
 * @package ELSWAK
 */
class ELSWAK_NullBoolean
	extends ELSWAK_Boolean {

	public function setValue($value) {
		$this->value = $this->valueAsNullBoolean($value);
		return $this;
	}
	public function label($trueLabel = 'yes', $falseLabel = 'no', $nullLabel = 'n/a') {
		if ($this->value) {
			return $trueLabel;
		} elseif ($this->value === false) {
			return $falseLabel;
		}
		return $nullLabel;
	}
	
	public static function valueAsNullBoolean($value) {
		// determine if a value is specifically null, otherwise cast it as a boolean
		if ($value === null) {
			return null;
		}
		// look for potential string matches
		if (is_string($value)) {
			$compare = strtolower($value);
			if (in_array($compare, self::acceptableNullValues())) {
				return null;
			}
		}
		// use the number line to as a false, null, true scale
		if (is_numeric($value)) {
			if ($value > 0) {
				return true;
			} else if ($value < 0) {
				return false;
			} else {
				return null;
			}
		}
		return self::valueAsBoolean($value);
	}
	public static function acceptableNullValues() {
		// these values should be listed in order of decreasing likelihood to match in order to minimize comparisons
		return array(
			'null',
			'pending',
			'p',
			'',
			'nil',
			'none',
		);
	}
}