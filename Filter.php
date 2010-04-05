<?php
/*
	ELSWAK Filter
	
	This class provides filtering methods for various datatypes, among those collected from disparate classes.
*/
class ELSWAK_Filter
{
	public static function integer($value)
	{
		return intval($value);
	}
	public static function positiveInteger($value)
	{
		// return only values >= 0
		$value = intval($value);
		if ($value < 1)
			$value = 0;
		return $value;
	}
	public static function databaseId($value)
	{
		return self::positiveInteger($value);
	}
	public static function timestamp($value)
	{
		if (is_int($value))
			return $value;
		return strtotime($value);
	}
}