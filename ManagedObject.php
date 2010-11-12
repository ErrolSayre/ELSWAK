<?php
abstract class ELSWAK_ManagedObject
	extends ELSWAK_Settable {
	
	abstract public function label();
	abstract public function uri();

	abstract public static function modelLabel();
	abstract public static function modelPlural();
}