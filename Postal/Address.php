<?php
/**
 * ELSWAK Postal Address
 *
 * @author Errol Sayre
 */



// utilize standard constants
require dirname(dirname(__FILE__)).'/StandardConstants.php';



/**
 * Represent a postal address
 *
 * @package ELSWAK\DataTypes
 */
class ELSWAK_Postal_Address
	extends ELSWAK_Object {




//!Class properties
	/**
	 * Memoize states
	 *
	 * Utilize a memoization to store the states listing to allow for
	 * external configuration. This is most helpful if there is a state
	 * lookup service or if a sub-class is made for other countries.
	 *
	 * @type ELSWAK_Array
	 */
	 protected static $states;




//!Instance properties
	/**
	 * Address lines
	 * @type ELSWAK_Array
	 */
	protected $lines;

	/**
	 * City
	 * @type string
	 */
	protected $city;

	/**
	 * State
	 * @type string
	 */
	protected $state;

	/**
	 * ZIP/Postal code
	 * @type string
	 */
	protected $postal;

	/**
	 * Country
	 * @type string
	 */
	protected $country;



	/**
	 * Create a new address
	 *
	 * Support importing content from the first parameter.
	 * @param string|array $line1 First line of the address or an array to import
	 * @param string $line2
	 * @param string $city
	 * @param string $state
	 * @param string $postal
	 * @param string $county
	 * @return ELSWAK_Postal_Address self
	 */
	public function __construct($line1 = '', $line2 = '', $city = '', $state = '', $postal = '', $country = '') {
		// ensure the lines property is set
		$this->setLines();

		// determine if $line1 is for import
		if (is_array($line1)) {
			$this->import($line1);
		} else {
			$this->setAddress($line1, $line2, $city, $state, $postal, $country);
		}
	}



	/**
	 * Set the address by components.
	 * @param string $line1
	 * @param string $line2
	 * @param string $city
	 * @param string $state
	 * @param string $postal
	 * @param string $county
	 * @return ELSWAK_Postal_Address self
	 */
	public function setAddress($line1, $line2, $city, $state, $postal, $country) {
		// reset the lines array
		$this->lines = new ELSWAK_Array;
		if ($line1) {
			$this->lines->add($line1);
		}
		if ($line2) {
			$this->lines->add($line2);
		}
		
		// set the other fields appropriately
		$this->setCity($city);
		$this->setState($state);
		$this->setPostal($postal);
		$this->setCountry($country);
		return $this;
	}
	/**
	 * Return a formatted address
	 * @param string $format
	 * @return string Formatted address
	 */
	public function address($format = 'plain-text') {
		// determine the line separator
		$lineSeparator = LF;
		$format = strtolower($format);
		if ($format == 'single-line') {
			$lineSeparator = ', ';
		}
		
		// assemble the address line by line
		$address = array();
		foreach ($this->lines as $line) {
			if ($line) {
				$address[] = $line;
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

	/**
	 * Build the City, State ZIP line
	 * @return string
	 */
	public function cityStateZipLine() {
		if ( $this->city && $this->state && $this->postal ) {
			return $this->city.', '.$this->state.' '.$this->postal;
		}
		elseif ( $this->city && $this->postal ) {
			return $this->city.' '.$this->postal;
		}
		elseif ( $this->city && $this->state ) {
			return $this->city.', '.$this->state;
		}
		elseif ( $this->city ) {
			return $this->city;
		}
		return '';
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



	public function setLines($value = null) {
		if (!$value instanceof ELSWAK_Array) {
			$value = new ELSWAK_Array($value);
		}
		$this->lines = $value;
		return $this;
	}

	/**
	 * Get array of lines
	 * @return array
	 */
	public function lines() {
		return $this->lines->store();
	}

	/**
	 * Get a copy of lines object
	 * @return ELSWAK_Array
	 */
	public function linesCopy() {
		return clone $this->lines;
	}

	/**
	 * Get a particular line
	 * @param integer $line
	 * @return string The corresponding line
	 */
	public function line($line) {
		if ($this->lines->hasValueForKey($line - 1)) {
			return $this->lines[$line - 1];
		}
		return '';
	}

	/**
	 * Set line 1
	 *
	 * @param string $value
	 * @return ELSWAK_Postal_Address self
	 */
	public function setLine1($value) {
		if ($value) {
			$this->lines[0] = strval($value);
		} elseif (isset($this->lines[0])) {
			unset($this->lines[0]);
		}
		return $this;
	}

	/**
	 * Alias line 1
	 * @return string
	 */
	public function line1() {
		return $this->line(1);
	}

	/**
	 * Set line 2
	 *
	 * @param string $value
	 * @return ELSWAK_Postal_Address self
	 */
	public function setLine2($value) {
		if ($value) {
			$this->lines[1] = strval($value);
		} elseif (isset($this->lines[1])) {
			unset($this->lines[1]);
		}
		return $this;
	}

	/**
	 * Alias line 2
	 * @return string
	 */
	public function line2() {
		return $this->line(2);
	}

	/**
	 * Add a line
	 *
	 * @param string $line
	 * @return ELSWAK_Postal_Address self
	 */
	public function addLine($line) {
		if ($line) {
			$this->lines->add(strval($line));
		}
		return $this;
	}

	/**
	 * Number of lines set
	 * @return integer
	 */
	public function lineCount() {
		return $this->lines->count();
	}



	/**
	 * Get postal code
	 * @return string
	 */
	public function postal() {
		return $this->postal;
	}

	/**
	 * Alias postal property
	 * @return string
	 */
	public function postalCode() {
		return $this->postal();
	}

	/**
	 * Alias postal property
	 * @return string
	 */
	public function zipCode() {
		return $this->postal();
	}



	/**
	 * Build formatted address
	 * @return string
	 */
	public function __toString() {
		return $this->address('single-line');
	}



//!Static methods
	/**
	 * Factory parser
	 *
	 * @param string $value
	 * @param ELSWAK_Postal_Address
	 */
	public static function parseAddress($value) {
		// determine if this address is line formatted (as on an envelope)
		if (strpos($value, LF) !== false) {
			return static::parseLineFormattedAddress($value);
		}
		return static::parseSpaceDelimitedAddress($value);
	}
	public static function parseLineFormattedAddress($value) {
		// create a new address
		$address = new ELSWAK_Postal_Address;
		
		// break the lines out
		$lines = explode(LF, $value);
		foreach ($lines as $line) {
			// determine if this line matches a likely city/state/zip line
			$matches = array();
			if (preg_match("/([a-zA-Z]+)\s*\,\s*([a-zA-Z]+)\s+([0-9-]+)/", $line, $matches) > 0) {
				$address->setCity($matches[1]);
				$address->setState($matches[2]);
				$address->setPostal($matches[3]);
			} else {
				$address->addLine($line);
			}
		}
		return $address;
	}
	public static function parseSpaceDelimitedAddress($value) {
		// create a new address
		$address = new ELSWAK_Postal_Address;
		
		// break the items out by spaces and collect them into lines
		$states = static::states();
		$pieces = explode(' ', $value.' ');
		$lines = array();
		foreach ($pieces as $piece) {
			if ($piece) {
				// determine if the value is likely a state
				if (
					!$address->state &&
					$states->parseItem($piece, false, false) != null
				) {
					$address->state = $piece;
					// the last piece (should have been added to lines...) is likely the city
					$address->city = array_pop($lines);
					
				// determine if the value is likely a zip code
				} elseif (
					!$address->postal && (
						(
							is_numeric($piece) &&
							$piece > 9999
						) ||
						strpos($piece, '-') == 5
					)
				) {
					$address->postal = $piece;
				} else {
					$lines[] = $piece;
				}
			}
		}
		// check for a city, if none, take the last item from the lines array
		if (!$address->city) {
			$address->city = array_pop($lines);
		}
		
		// take whatever remains in the lines array and join it back together as a line
		$address->addLine(implode(' ', $lines));
		
		// return the completed address
		return $address;
	}
	
	public static function setStates(ELSWAK_Array $states) {
		static::$states = $states;
	}
	public static function states() {
		if (!static::$states) {
			static::$states = new ELSWAK_Array(array(
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
			));
		}
		return static::$states;
	}
}