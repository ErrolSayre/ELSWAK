<?php
/**
 * Add key and value validation to arrays
 *
 * Override parent methods to ensure the validation methods are called.
 *
 * @author Errol Sayre
 * @package ELSWAK\Collections
 */
class ELSWAK_Validated_Array
	extends ELSWAK_Array {



	/*
	 * Override the parent method
	 *
	 * In the parent class, this method would choose to add or set based on
	 * the key being null or already existent. To simplify the behavior for
	 * subclasses, this class makes that distinction while validating the
	 * key.
	 *
	 * @return ELSWAK_Array reference to this instance
	 */
	public function add($value, $key = null) {
		// first validate the item (subclasses will override the method)
		$value = $this->validateOrTransformItemForInclusion($value);
		
		// now validate the key (subclasses will override the method)
		$key = $this->validateOrTransformKeyForSetting($key, true);
		
		// now set the value for the key
		return $this->setValueForKey($value, $key);
	}

	/**
	 * Override the parent method
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return ELSWAK_Array self
	 */
	public function setValueForKey($value, $key) {
		// first validate the item (subclasses will override the method)
		$value = $this->validateOrTransformItemForInclusion($value);
		
		// now validate the key (subclasses will override the method)
		$key = $this->validateOrTransformKeyForSetting($key, false);
		
		// actually set the value
		$this->store[$key] = $value;
		return $this;
	}



	/**
	 * Validate a key for setting
	 *
	 * Like validating an item for inclusion, this method is setup so that
	 * subclasses can override this method to reformat or reject keys to
	 * maintain its desired integrity. For example, a dictionary class may
	 * wish to enforce that all keys be strings or a pure array class may
	 * wish to enforce sequential, numeric keys.
	 *
	 * Subclasses may wish to throw an exception but this particular method
	 * does not.
	 *
	 * @param mixed $key
	 * @param boolean $forAddition
	 * @return mixed $key
	 */
	public function validateOrTransformKeyForSetting($key, $forAddition = false) {
		// determine if the key is to be used for adding a new item
		if ($forAddition) {
			if ($key === null || $key === false || $this->hasValueForKey($key)) {
				// pick the next sequential key
				return $this->nextSequentialKey();
			}
		}
		return $key;
	}

	/**
	 * Pick the next sequential key
	 *
	 * Generally this will be a numeric value, but subclasses may wish to
	 * override this behavior to pick some other value.
	 *
	 * This particular method should mirror the behavior of PHP when
	 * setting a value with an empty array reference like:
	 *     $var[] = value;
	 *
	 * @return integer
	 */
	public function nextSequentialKey() {
		// since 0 is a special case, test for it specifically
		if ($this->hasValueForKey(0)) {
			return max(array_keys($this->store)) + 1;
		}
		return 0;
	}



	/**
	 * Validate an item for inclusion
	 *
	 * This method is being setup here so that subclasses can validate an
	 * item before storing it in the collection.
	 *
	 * The gist of this method is to check the value against criteria,
	 * modify it if appropriate, and return it for placement, or throw an
	 * exception if the value cannot be made acceptable.
	 *
	 * Since validateItem had previously been used for a different purpose
	 * in ELSWAK Array, I am refraining from using that method name at this
	 * time. Perhaps at a later point I will add it as an alias.
	 *
	 * @param mixed $item
	 * @return mixed $item
	 */
	public function validateOrTransformItemForInclusion($item) {
		return $item;
	}
}