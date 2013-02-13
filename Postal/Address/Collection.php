<?php
/**
 * Collect a listing of postal addresses
 *
 * @author Errol Sayre
 * @package ELSWAK\Collections
 */
class ELSWAK_Postal_Address_Collection
	extends ELSWAK_Contact_Collection {

	/**
	 * Override parent setter
	 *
	 * Override to ensure all items added are postal addresses.
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return ELSWAK_Contact_Collection self
	 */
	public function setValueForKey($value, $key) {
		if ($value) {
			if (!($value instanceof ELSWAK_Postal_Address)) {
				$value = ELSWAK_Postal_Address::parseAddress($value);
			}
			$this->store[$key] = $value;
		} else {
			$this->removeValueForKey($key);
		}
		return $this;
	}
}