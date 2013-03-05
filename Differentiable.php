<?php
/**
 * ELSWAK Differentiable
 *
 * Interface for objects that can be compared for differences.
 *
 * @package ELSWAK\Interfaces
 */
interface ELSWAK_Differentiable {
	/**
	 * Compare two differentiable objects
	 *
	 * Since this method is expected to be subclassed, the comparison
	 * object must be validated within the method.
	 *
	 * @param mixed $compare
	 * @return ELSWAK_Collection_Differences|ELSWAK_Object_Differences
	 */
	public function differences($compare);
}