<?php
/**
 * ELSWAK Identifiable
 *
 * Interface for objects that provide an identifier.
 *
 * @package ELSWAK\Interfaces
 */
interface ELSWAK_Identifiable {
	/**
	 * Return an identifier
	 *
	 * @return string
	 */
	public function identifier();
}