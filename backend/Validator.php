<?php
/*
	ELSWebAppKit Validator
	
	This class provides validating methods for various datatypes, among those collected from disparate classes.
*/
require_once 'ELSWebAppKit/Email/Address.php';
require_once 'ELSWebAppKit/Phone/Number.php';
class ELSWebAppKit_Validator
{
	public static function integer($value)
	{
		if ((string) intval($value) == (string) $value)
			return true;
		return false;
	}
	public static function positiveInteger($value)
	{
		if (self::integer($value))
			if (intval($value) >= 0)
				return true;
		return false;
	}
	public static function databaseId($value)
	{
		return self::positiveInteger($value);
	}
	public static function timestamp($value)
	{
		return self::integer($value);
	}
	public static function emailAddress($value)
	{
		return ELSWebAppKit_Email_Address::verify($value);
	}
	public static function phoneNumber($value)
	{
		return ELSWebAppKit_Phone_Number::verify($value);
	}
}