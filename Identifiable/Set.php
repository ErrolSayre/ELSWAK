<?php
/**
 * Collect list of unqiue identifiables
 *
 */
class ELSWAK_Identifiable_Set
	extends ELSWAK_Identifiable_Array {



	/**
	 * Override the parent method
	 *
	 * Ensure items are only added once by utilizing the identifier index.
	 *
	 */
	public function add($value, $key = null) {
		// validate the item (subclasses will override the method)
		$value = $this->validateOrTransformItemForInclusion($value);
		
		// validate the key (subclasses will override the method)
		$key = $this->validateOrTransformKeyForSetting($key, true);
		
		// validate the uniqueness of the value
		if ($this->keyForIdentifier($value->identifier()) !== false) {
			return $this->handleDuplicateItem($value, $key);
		}
		
		// set the value for the key
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
		// validate the item (subclasses will override the method)
		$value = $this->validateOrTransformItemForInclusion($value);
		
		// validate the key (subclasses will override the method)
		$key = $this->validateOrTransformKeyForSetting($key, false);
		
		// validate the uniqueness of the value
		if ($this->keyForIdentifier($value->identifier()) !== false) {
			return $this->handleDuplicateItem($value, $key);
		}
		
		// actually set the value
		$this->store[$key] = $value;
		return $this;
	}



	/**
	 * Insert a value at index
	 *
	 * This method needs to be overriden to ensure it utilizes the
	 * validation methods.
	 *
	 * @param mixed $value
	 * @param integer|false $index
	 * @return ELSWAK_Array self
	 */
	public function insert($value, $index = false) {
		// if the index is false, simply append the value
		if ($index === false) {
			return $this->add($value);
		}
		
		// validate the item (subclasses will override the method)
		$value = $this->validateOrTransformItemForInclusion($value);
		
		// since this method deals in purely numeric positions, no key validation is done
		
		// validate the uniqueness of the value
		if ($this->keyForIdentifier($value->identifier()) !== false) {
			return $this->handleDuplicateItem($value);
		}
		
		// splice the value in at the array index
		// wrap the value in an array to ensure an array value remains that way
		array_splice($this->store, $index, 0, array($value));
		return $this;
	}



	/**
	 * Handle duplicate item
	 *
	 * Since this is a protected method, we can generally assume the value
	 * has been properly vetted.
	 *
	 * By default, the set will totally ignore duplicate items. Subclasses
	 * may desire to override this behavior by throwing an exception or
	 * replacing the extant item.
	 *
	 * Since we know this method only exists in collections that already
	 * require ELSWAK_Identifiable items, we can place that restriction on
	 * this method trusting that subclasses will meet the same requirement.
	 *
	 * This will provide an inadvertant safe-guard to ensure
	 * validateOrTransformItemForInclusion was properly overidden.
	 *
	 * @param ELSWAK_Identifiable $value
	 * @param mixed|null $key
	 * @return ELSWAK_Identifiable_Set
	 */
	protected function handleDuplicateItem(ELSWAK_Identifiable $value, $key = null) {
		return $this;
	}
}