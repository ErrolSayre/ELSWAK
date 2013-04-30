<?php
/**
 * Create an interface to ensure that an object is "gettable".
 */
interface ELSWAK_Gettable {



//!Interface instance methods
	/**
	 * Get a property gently (no exceptions/errors)
	 *
	 * @param string $property
	 * @return mixed|null
	 */
	public function get($property);



	/**
	 * Provide a string representation of this object.
	 *
	 * @return string
	 */
	public function __toString();
}