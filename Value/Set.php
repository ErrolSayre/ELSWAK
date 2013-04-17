<?php
/**
 * Collect list of unqiue values
 *
 */
class ELSWAK_Value_Set
	extends ELSWAK_Validated_Array {



	/**
	 * Overridde the parent to ensure unique values
	 *
	 * Set a key/value pair after they have been properlly validated.
	 *
	 * This method mostly isolates this process out from the validation
	 * process to enable more easily overriding final checks on the value.
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return ELSWAK_Value_Set self
	 */
	protected function setValidatedValueForValidatedKey($value, $key) {
		if ($this->isItemUnique($value, $key)) {
			$this->store[$key] = $value;
			return $this;
		}
		return $this->handleDuplicateItem($value, $key);
	}



	/**
	 * Overridde the parent to ensure unique values
	 *
	 * Splice the value
	 *
	 * This protected method extracts the actual splicing process from the
	 * insert method while preventing bypass.
	 *
	 * @param mixed $value
	 * @param integer|false $index
	 * @return ELSWAK_Value_Set self
	 */
	protected function insertValidatedValueAtPosition($value, $index) {
		if ($this->isItemUnique($value)) {
			// splice the value in at the array index
			// wrap the value in an array to ensure an array value remains that way
			array_splice($this->store, $index, 0, array($value));
			return $this;
		}
		return $this->handleDuplicateItem($value);
	}	 



	/**
	 * Determine if an item is unique
	 *
	 * This method should be overridden by subclasses to customize the
	 * mechanism for determining uniqueness.
	 *
	 * This class refrains from throwing exceptions when items are not
	 * unique, the easiest way to rectify this is to throw the exception
	 * from this method within subclasses.
	 *
	 * @param mixed $value
	 * @param mixed $key
	 * @return boolean
	 */
	public function isItemUnique($value, $key = null) {
		return $this->keyForValue($value) === false;
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
	 * @param mixed $value
	 * @param mixed|null $key
	 * @return ELSWAK_Identifiable_Set
	 */
	protected function handleDuplicateItem($value, $key = null) {
		return $this;
	}
}