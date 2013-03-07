<?php
/**
 * ELSWAK Contact Collection
 *
 * Codify a means of collecting contact information (of a particular
 * type) for various uses. For example a person may have several email
 * addresses, phone numbers, or postal addresses with each tagged for a
 * particular use such as home, work, office, personal, mobile, etc.
 *
 * This class provides a means for collecting one type of contact info
 * for these various uses. Generically it provides the home, work, and
 * office uses with substitution priorities for each. Specific
 * subclasses could be made for phones to specify mobile, personal, fax,
 * etc.
 *
 * @package ELSWAK\Collections
 */
class ELSWAK_Contact_Collection
	extends ELSWAK_Dictionary {



//!Class properties
	/**
	 * Memoize key substitutions
	 *
	 * @type array
	 */
	protected static $substitutions;



	/**
	 * Override parent setter
	 *
	 * Since we don't want to have any null values set, override the setter
	 * to make sure keys are removed.
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return ELSWAK_Contact_Collection self
	 */
	public function setValueForKey($value, $key) {
		if ($value) {
			$this->store[$key] = $value;
		} else {
			$this->removeValueForKey($key);
		}
		return $this;
	}



	/**
	 * Override parent getter
	 *
	 * Override in order to support getting the primary item as a property
	 * when not explicitly set.
	 *
	 * @param mixed $key
	 * @return mixed|null
	 */
	public function valueForKey($key) {
		if ($this->hasValueForKey($key)) {
			return $this->store[$key];
		} elseif ($key == 'primary') {
			return $this->primary();
		}
		return null;
	}

	/**
	 * Locate best option for primary
	 *
	 * Look for the best option to use as a primary contact value.
	 *
	 * The primary contact value is able to be set directly, but ideally
	 * would be used as a "virtual" property to reference whichever value
	 * best fits (following substitutions, etc.)
	 *
	 * @return mixed
	 */
	public function primary() {
		return $this->bestForUse('primary');
	}
	/**
	 * Locate best option for home
	 *
	 * Look for the best option to use as a home contact value.
	 * @return mixed
	 */
	public function bestForHome() {
		return $this->bestForUse('home');
	}

	/**
	 * Locate best option for work
	 *
	 * Look for the best option to use as a work contact value.
	 * @return mixed
	 */
	public function bestForWork() {
		return $this->bestForUse('work');
	}

	/**
	 * Locate best option for office
	 *
	 * Look for the best option to use as an office contact value.
	 * @return mixed
	 */
	public function bestForOffice() {
		return $this->bestForUse('office');
	}

	/**
	 * Locate best option for use
	 *
	 * Look for the best option to use for the particular situation.
	 *
	 * @param string $use
	 * @return mixed
	 */
	public function bestForUse($use) {
		// look for a value with the key
		if ($this->hasValueForKey($use)) {
			return $this->valueForKey($use);
		}
		
		// look through any known substitutions
		$seen = array($use);
		$substitutions = $this->substitutions();
		if (array_key_exists($use, $substitutions)) {
			$subs = $substitutions[$use];
			foreach ($subs as $sub) {
				$seen[$sub] = true;
				if ($this->hasValueForKey($sub)) {
					return $this->valueForKey($sub);
				}
			}
		}
		
		// look through the remaining keys
		$keys = $this->keys();
		foreach ($keys as $key) {
			if (!array_key_exists($key, $seen)) {
				return $this->valueForKey($key);
			}
		}
		
		// return the default value
		return null;
	}
	/**
	 * Set key substitution priorities
	 *
	 * @param array $substitutions 2 dimensional array of uses and their substitutes
	 */
	public static function setSubstitutions($substitutions) {
		static::$substitutions = $substitutions;
	}
	/**
	 * Substitution priorities
	 *
	 * Provide predetermined priorities for substituting values for a
	 * particular use.
	 *
	 * @return array
	 */
	public static function substitutions() {
		if (!is_array(static::$substitutions)) {
			static::$substitutions = array(
				'primary' => array(
					'office',
					'work',
					'home',
				),
				'home' => array(
					'office',
					'work',
				),
				'work' => array(
					'office',
					'home',
				),
				'office' => array(
					'work',
					'home',
				),
			);
		}
		return static::$substitutions;
	}
}