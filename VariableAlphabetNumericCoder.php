<?php
/*
	ELSWAK Variable Alphabet Numeric Coder
	
	This class provides a standardized mechanism for encoding integers into shorter strings or shifting a value from decimal to an arbitrary base produced using an alphabet.
	
	If no alphabet is provided, the default base 56 alphabet is used.
	
	I haven't performed extensive tests, however it seems the reliability of the encoding procedure comes into question with integer values greater than 52 bits.
*/
class ELSWAK_VariableAlphabetNumericCoder {
	const DEFAULT_ALPHABET = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
	protected $alphabet;
	protected $alphabetToValueIndex;
	protected $base;
	
	public function __construct($alphabetString = null) {
		$this->setAlphabet($alphabetString);
	}
	public function setAlphabet($alphabetString = null) {
		if ($alphabetString == null) {
			$alphabetString = self::DEFAULT_ALPHABET;
		}
		// ensure a proper string is provided
		if (is_string($alphabetString)) {
			// break the string into an array of characters to produce a value translation matrix
			$this->alphabet = str_split($alphabetString);
			
			// flip the alphabet to produce a mapping of letters to numeric values
			$this->alphabetToValueIndex = array_flip($this->alphabet);
			
			// determine the base provided by this alphabet
			$this->base = sizeof($this->alphabet);
			
			return $this;
		}
		throw new ELSWAK_VariableAlphabetNumericCoder_InvalidAlphabet_Exception('Unabel to produce alphabet. Non-string input provided.');
	}
	public function encode($number) {
		// do not attempt to encode an integer value less than 1
		$integer = intval($number);
		if ($integer < 1) {
			throw new ELSWAK_VariableAlphabetNumericCoder_InvalidInput_Exception('Unable to encode value. Non-positive integer values are not supported.');
		}
		
		// setup a collection bin for the encoded values
		$codedCharacters = array();
		
		// reduce the number by the base until the integer is null while encoding the remainder
		while ($integer) {
			$remainder = $integer % $this->base;
			$integer = (int) ($integer / $this->base);
			$codedCharacters[] = $this->alphabet[$remainder];
		}
		
		// make a string out of the characters by reversing the values as collected
		return implode(array_reverse($codedCharacters));
	}
	function decode($string) {
		if (is_string($string)) {
			// determine the maximum power used in this base (reduce the length by one)
			$maxPower = strlen($string) - 1;
			
			// create an integer to hold the decoded value
			$integer = 0;
			
			// break the string into characters to represent the value of each power
			$characters = str_split($string);
			
			// process the chracters converting their index position into their decimal value
			$index = 0;
			foreach ($characters as $character) {
				$power = $maxPower - $index;
				if (isset($this->alphabetToValueIndex[$character])) {
					if (($placeValue = pow($this->base, $power)) !== false) {
						$integer += $this->alphabetToValueIndex[$character] * $placeValue;
					} else {
						throw new ELSWAK_VariableAlphabetNumericCoder_Exception('Unable to decode “'.$string.'”. Unable to calculate necessary place value.');
					}
				} else {
					// since the character doesn't exist in the alphabet, throw an exception
					throw new ELSWAK_VariableAlphabetNumericCoder_InvalidInput_Exception('Unable to decode value. Encoded string includes characters not found in translation alphabet.');
				}
				++$index;
			}
			return $integer;
		}
		throw new ELSWAK_VariableAlphabetNumericCoder_InvalidInput_Exception('Unable to decode value. Only proper string values are supported.');
	}
}
class ELSWAK_VariableAlphabetNumericCoder_Exception extends ELSWAK_Exception {}
class ELSWAK_VariableAlphabetNumericCoder_InvalidInput_Exception extends ELSWAK_VariableAlphabetNumericCoder_Exception {}
class ELSWAK_VariableAlphabetNumericCoder_InvalidAlphabet_Exception extends ELSWAK_VariableAlphabetNumericCoder_Exception {}