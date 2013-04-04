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

	/**
	 * Stringify the value
	 *
	 * Despite the fact that we have a static method to do this same task,
	 * perform this logic locally to avoid a redundant parsing of the
	 * boolean value.
	 *
	 * @param string $trueLabel
	 * @param string $falseLabel
	 * @return string
	 */
	public function label($trueLabel = 'yes', $falseLabel = 'no') {
		if ($this->value) {
			return $trueLabel;
		}
		return $falseLabel;
	}



	public function __toString() {
		return $this->label();
	}



	/**
	 * Generic setter
	 *
	 * Allow any attempts to set a value on this object to succeed. I'm not
	 * sure why I added this, but it's probably mostly to allow setting the
	 * value as a property rather than a method to match the form of
	 * ELSWAK_Object.
	 *
	 * @param string $property
	 * @param mixed $value
	 * @return ELSWAK_Boolean self
	 */
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



	/**
	 * Booleanify a value
	 *
	 * Convert values to booleans in a sensible manner. PHP construes any
	 * non-null value as true but we want to rework that behavior to
	 * support logical translations of various values into booleans. For
	 * example, a string value of 'false' should translate to a boolean
	 * value of false but by default in PHP this is not the case.
	 *
	 * We could potentially check for values that are already specifically
	 * booleans, however this would only save two comparisons in that case
	 * while instead costing one additional comparison in all other cases.
	 *
	 * @mixed $value
	 * @return boolean
	 */
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



	/**
	 * Stringify a boolean
	 *
	 * In order to ensure this method behaves as expected, we must first
	 * translate the provided value to a boolean.
	 *
	 * As opposed to the behavior of the instance method, this method opts
	 * to utilize the terms TRUE and FALSE. This is a purely arbitrary
	 * choice that has more to do with the particular use-case that
	 * instigated this method's addition.
	 *
	 * @param string $trueLabel
	 * @param string $falseLabel
	 * @return string
	 */
	public static function booleanAsLabel($value, $trueLabel = 'TRUE', $falseLabel = 'FALSE') {
		// ensure the value has been "booleanized"
		if (self::valueAsBoolean($value)) {
			return $trueLabel;
		}
		return $falseLabel;
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