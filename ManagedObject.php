<?php
abstract class ELSWAK_ManagedObject
	extends ELSWAK_Settable {
	
	abstract public function label();
	abstract public function uri();

	abstract public static function modelLabel();
	public static function modelPlural() {
		return static::modelLabel().'s';
	}
	public static function modelClassLabel() {
		return strtolower(static::modelLabel());
	}
}