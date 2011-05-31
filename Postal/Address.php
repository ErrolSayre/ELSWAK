<?php
/*
	ELSWAK Postal Address
*/
class ELSWAK_Postal_Address
	extends ELSWAK_Settable {
	protected $lines;
	protected $city;
	protected $state;
	protected $postal;
	protected $country;
	
	public function __construct($line1 = '', $line2 = '', $city = '', $state = '', $postal = '', $country = '') {
		if ($line1 && !$line2 && !$city && !$state && !$postal && !$country) {
			if (is_array($line1)) {
				$this->import($line1);
			} else {
				$this->parseAddress($line1);
			}
		} else {
			$this->setAddress($line1, $line2, $city, $state, $postal, $country);
		}
	}
	public function address($format = 'plain-text') {
		// determine the line separator
		$lineSeparator = LF;
		$format = strtolower($format);
		if ($format == 'single-line') {
			$lineSeparator = ', ';
		} else if ($format == 'html') {
			$lineSeparator = BR;
		}
		
		// assemble the address line by line
		$address = array();
		if (is_array($this->lines)) {
			foreach ($this->lines as $line) {
				if ($line) {
					$address[] = $line;
				}
			}
		}
		
		// add the city, state zip as best fits
		$cityStateZip = $this->cityStateZipLine($format);
		if ($cityStateZip) {
			$address[] = $cityStateZip;
		}
		
		// add the country if set
		if ($this->country) {
			$address[] = $this->country;
		}
		
		// return the address as requested
		if ($format == 'array') {
			return $address;
		}
		return implode($lineSeparator, $address);
	}
	public function cityStateZipLine() {
		if ( $this->city && $this->state && $this->postal ) {
			return $this->city.', '.$this->state.' '.$this->postal;
		} else if ( $this->city && $this->postal ) {
			return $this->city.' '.$this->postal;
		} else if ( $this->city && $this->state ) {
			return $this->city.', '.$this->state;
		} else if ( $this->city ) {
			return $this->city;
		}
		return '';
	}
	public function setAddress($line1, $line2, $city, $state, $postal, $country) {
		// reset the lines array
		$this->lines = array();
		$this->addLine($line1);
		$this->addLine($line2);
		
		// set the other fields appropriately
		$this->setCity($city);
		$this->setState($state);
		$this->setPostal($postal);
		$this->setCountry($country);
		return $this;
	}
	public function parseAddress($address) {
		// determine if this address is line formatted (as on an envelope)
		if (strpos($address, LF) !== false) {
			$this->parseLineFormattedAddress($address);
		} else if (strpos($address, ' $ ')) {
			// this is likely a UM LDAP encoded address, replace the delimiter with a line feed
			$this->parseLineFormattedAddress(str_replace(' $ ', LF, $address));
		}
		return $this;
	}
	public function parseLineFormattedAddress($address) {
		// reset the address
		$this->setAddress(null, null, null, null, null, null);
		
		// break the lines out
		$lines = explode(LF, $address);
		foreach ($lines as $line) {
			// determine if this line matches a likely city/state/zip line
			$matches = array();
			if (preg_match("/([a-zA-Z]+)\s*\,\s*([a-zA-Z]+)\s+([0-9-]+)/", $line, $matches) > 0) {
				$this->setCity($matches[1]);
				$this->setState($matches[2]);
				$this->setPostal($matches[3]);
			} else {
				$this->addLine($line);
			}
		}
	}
	public function import($data) {
		$this->_import($data);
		if (is_array($data)) {
			if (array_key_exists('line1', $data)) {
				$this->setLine1($data['line1']);
			}
			if (array_key_exists('line2', $data)) {
				$this->setLine2($data['line2']);
			}
			if (array_key_exists('zip', $data)) {
				$this->setPostal($data['zip']);
			}
		}
		return $this;
	}
	public function line($line) {
		if (array_key_exists($line - 1, $this->lines)) {
			return $this->lines[$line - 1];
		}
		return '';
	}
	protected function setLines() {}
	public function setLine1($value) {
		$this->lines[0] = strval($value);
		return $this;
	}
	public function setLine2($value) {
		$this->lines[1] = strval($value);
		return $this;
	}
	public function addLine($line) {
		if ($line) {
			$this->lines[] = strval($line);
		}
	}
	public function lineCount() {
		return count($this->lines);
	}
	public function lines() {
		return $this->lines;
	}
	public function city() {
		return $this->city;
	}
	public function setCity($city) {
		$this->city = $city;
		return $this;
	}
	public function state() {
		return $this->state;
	}
	public function setState($state) {
		$this->state = $state;
		return $this;
	}
	public function postal() {
		return $this->postal;
	}
	public function setPostal($postal) {
		$this->postal = $postal;
		return $this;
	}
	public function country() {
		return $this->country;
	}
	public function setCountry($country) {
		$this->country = $country;
		return $this;
	}
	public function __toString() {
		return $this->address('single-line');
	}
	public static function states() {
		return array(
			'AL' => 'Alabama',
			'AK' => 'Alaska',
			'AZ' => 'Arizona',
			'AR' => 'Arkansas',
			'CA' => 'California',
			'CO' => 'Colorado',
			'CT' => 'Connecticut',
			'DE' => 'Delaware',
			'DC' => 'District Of Columbia',
			'FL' => 'Florida',
			'GA' => 'Georgia',
			'HI' => 'Hawaii',
			'ID' => 'Idaho',
			'IL' => 'Illinois',
			'IN' => 'Indiana',
			'IA' => 'Iowa',
			'KS' => 'Kansas',
			'KY' => 'Kentucky',
			'LA' => 'Louisiana',
			'ME' => 'Maine',
			'MD' => 'Maryland',
			'MA' => 'Massachusetts',
			'MI' => 'Michigan',
			'MN' => 'Minnesota',
			'MS' => 'Mississippi',
			'MO' => 'Missouri',
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',
			'OK' => 'Oklahoma',
			'OR' => 'Oregon',
			'PA' => 'Pennsylvania',
			'RI' => 'Rhode Island',
			'SC' => 'South Carolina',
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',
			'TX' => 'Texas',
			'UT' => 'Utah',
			'VT' => 'Vermont',
			'VA' => 'Virginia',
			'WA' => 'Washington',
			'WV' => 'West Virginia',
			'WI' => 'Wisconsin',
			'WY' => 'Wyoming',
		);
	}
}