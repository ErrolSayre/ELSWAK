<?php
/**
 * Wrap the PHP DateTime class.
 *
 * This class provides getters for pseudo-properties of a date, primarily to
 * support simple access to predetermined formats.
 * @package ELSWAK
 */
class ELSWAK_Date
	extends DateTime
	implements ELSWAK_Gettable {



	/**
	 * Predefined date format for MySQL DATETIME
	 */
	const DATETIME = 'Y-m-d H:i:s';

	/**
	 * Custom "full date" format
	 *
	 * This format covers a common use-case I've encountered. An alternative might
	 * be to define such constants in the consuming application but anyone using
	 * these classes likely appreciates the defaults I pick.
	 */
	const FULL = 'l, F j, Y';

	/**
	 * Custom "full time" format
	 *
	 * This format covers a common use-case I've encountered.
	 */
	const FULLTIME = 'g:i:s a';



	/**
	 * Generic getter
	 *
	 * Allow getting predefined formats as values.
	 *
	 * @param string $property
	 * @return string
	 */
	public function get($property) {
		// determine if the property is a recognized format
		// uppercase the argument
		$format = strtoupper($property);
		if (in_array($format, $this->recognizedFormats())) {
			return $this->format(constant('ELSWAK_Date::'.$format));
		}
		return null;
	}
	/**
	 * Alias the getter
	 *
	 * Allow “getting” of particular formats as though they are properties of the
	 * object.
	 *
	 * @param string $property
	 * @return string
	 */
	public function __get($property) {
		return $this->get($property);
	}



	/**
	 * Return a string
	 *
	 * Return the date in a human and machine readible value.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->format(self::ISO8601);
	}
	 



//!Static methods
	/**
	 * Recognized pre-defined formats
	 */
	public function recognizedFormats() {
		return array(
			'ATOM',
			'COOKIE',
			'DATETIME',
			'FULL',
			'FULLTIME',
			'ISO8601',
			'RFC822',
			'RFC850',
			'RFC1036',
			'RFC1123',
			'RFC2822',
			'RFC3339',
			'RSS',
			'W3C',
		);
	}
}