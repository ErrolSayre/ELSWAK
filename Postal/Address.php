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
		if (is_array($line1)) {
			$this->import($line1);
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
		$address = '';
		foreach ($this->lines as $line) {
			if ($line != null) {
				$address .= $line.$lineSeparator;
			}
		}
		
		// add the city, state zip as best fits
		if (($this->city != '')		&& ($this->state != '')	&& ($this->postal != '')) {
			$address .= $this->city.', '.$this->state.' '.$this->postal.$lineSeparator;
		} else if (($this->city != '')	&& ($this->postal != '')) {
			$address .= $this->city.' '.$this->postal.$lineSeparator;
		} else if ($this->city != '') {
			$address .= $this->city.$lineSeparator;
		}
		if ($this->country != '') {
			$address .= $this->country.$lineSeparator;
		}
		
		// remove the trailing line separator
		$address = substr($address, 0, -1 * strlen($lineSeparator));
		
		// trim off the excess whitespace and send the address back
		return trim($address);
	}
	public function setAddress($line1, $line2, $city, $state, $postal, $country) {
		// reset the lines array
		$this->lines = null;
		$this->addLine($line1);
		$this->addLine($line2);
		
		// set the other fields appropriately
		$this->setCity($city);
		$this->setState($state);
		$this->setPostal($postal);
		$this->setCountry($country);
		return $this;
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
		if (isset($this->lines[$line - 1])) {
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
		if ($line != '') {
			$this->lines[] = $line;
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