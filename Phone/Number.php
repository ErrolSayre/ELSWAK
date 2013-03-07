<?php
/*
	ELSWAK Phone Number
	
	This class parses phone numbers. Please note that phone numbers must match either the 10 digit or 7 digit profiles.
	
	Letters are converted to numbers according to the standard phone keypad. The parse does take into account the possibility that a 9 immediately follows the last 4 digits of the number and assumes that this is an "x" that has been converted to a 9. For this reason you should always add a delimiter if your extension begins with a 9, though most internal phone systems don't allow extensions to begin with 9 since it is commonly used as the external access prefix.
*/
class ELSWAK_Phone_Number
	extends ELSWAK_Settable {
	const TEN_DIGIT_REGEX_BASE = '/\+?(1?)[\s\-\.]?[\(]?([[2-9]\d{2}]?)[\)]?[\s\-\.]?([[2-9]\d{2}]?)[\s\-\.]?(\d{4})[9\s\-\.]?(\d*)/';
	const TEN_DIGIT_REGEX = '/^\+?(1?)[\s\-\.]?[\(]?([[2-9]\d{2}]?)[\)]?[\s\-\.]?([[2-9]\d{2}]?)[\s\-\.]?(\d{4})[9\s\-\.]?(\d*)$/';
	const SEVEN_DIGIT_REGEX_BASE = '/([[2-9]\d{2}]?)[\s\-\.]?(\d{4})[9\s\-\.]?(\d*)/';
	const SEVEN_DIGIT_REGEX = '/^([[2-9]\d{2}]?)[\s\-\.]?(\d{4})[9\s\-\.]?(\d*)$/';
	
	protected $countryCode;
	protected $areaCode;
	protected $localPrefix;
	protected $localSuffix;
	protected $extension;
	
	public function __construct($number = null) {
		$this->setNumber($number);
	}
	public function number() {
		$number = '';
		if ($this->countryCode != '')
			$number = $this->countryCode.' ';
		if ($this->areaCode != '')
			$number .= '('.$this->areaCode.') ';
		if ($this->localPrefix != '')
			$number .= $this->localPrefix.'-';
		if ($this->localSuffix != '')
			$number .= $this->localSuffix;
		if ($this->extension != '')
			$number .= ' '.$this->extension;
		
		return $number;
	}
	public function setNumber($number) {
		// reset the number blank
		$this->countryCode	= '';
		$this->areaCode		= '';
		$this->localPrefix	= '';
		$this->localSuffix	= '';
		$this->extension	= '';
		
		// replace letters with their number counter parts
		$number = $this->translateLettersToNumbers($number);
		
		// determine if the number is a 10 digit number
		if (preg_match(self::TEN_DIGIT_REGEX, $number, $matches) == 1) {
			// this is a 10+ digit number
			$this->countryCode	= $matches[1]?	$matches[1]:	$matches[2]? 1:	'';
			$this->areaCode		= $matches[2];
			$this->localPrefix	= $matches[3];
			$this->localSuffix	= $matches[4];
			$this->extension	= $matches[5];
		} else if (preg_match(self::SEVEN_DIGIT_REGEX, $number, $matches) == 1) {
			// this is a 7 digit number
			$this->countryCode	= '';
			$this->areaCode		= '';
			$this->localPrefix	= $matches[1];
			$this->localSuffix	= $matches[2];
			$this->extension	= $matches[3];
		} else {
			// try to fit international numbers into the components
			// process string right to left placing numbers into the components based on US formatting
			$number = preg_replace('/[^0-9]/', '', $number);
			$length = strlen($number);
			for ($offset = 1; $offset <= $length; ++$offset) {
				if ($offset < 5) {
					$this->localSuffix = $number[$length - $offset].$this->localSuffix;
				} else if ($offset < 8) {
					$this->localPrefix = $number[$length - $offset].$this->localPrefix;
				} else if ($offset < 11) {
					$this->areaCode = $number[$length - $offset].$this->areaCode;
				} else {
					$this->countryCode = $number[$length - $offset].$this->countryCode;
				}
			}
		}
		return $this;
	}
	public static function translateLettersToNumbers($phoneNumber) {
		return preg_replace('/[AaBbCc]/', 2, preg_replace('/[DdEeFf]/', 3, preg_replace('/[GgHhIi]/', 4, preg_replace('/[JjKkLl]/', 5, preg_replace('/[MmNnOo]/', 6, preg_replace('/[PpQqRrSs]/', 7, preg_replace('/[TtUuVv]/', 8, preg_replace('/[WwXxYyZz]/', 9, preg_replace('/\s/', '', $phoneNumber)))))))));
	}
	public static function verify($phoneNumber) {
		if (self::verifyTenDigit($phoneNumber) || self::verifySevenDigit($phoneNumber))
			return true;
		return false;
	}
	public static function verifyTenDigit($phoneNumber) {
		if (preg_match(self::TEN_DIGIT_REGEX, $phoneNumber) == 1)
			return true;
		return false;
	}
	public static function verifySevenDigit($phoneNumber) {
		if (preg_match(self::SEVEN_DIGIT_REGEX, $phoneNumber) == 1)
			return true;
		return false;
	}
	public function __toString() {
		return $this->number();
	}
}