<?php
/*
	DataGeneral JSON Translator
	
	This class provides translation from models that implement Iterator to JSON encoded objects. It wraps the PHP JSON functions (json_encode & json_decode) within a class that provides some useful helper functions designed at migrating protected member models to open member models that the JSON extension recognizes.
*/
class ELSWebAppKit_JSON_Translator
{
	public static function encode($input)
	{
		if (is_object($input) || is_array($input))
		{
			$input = self::arrayForObject($input);
		}
		
		return json_encode($input);
	}
	public static function decode($input)
	{
		return json_decode($input);
	}
	public static function arrayForObject($object)
	{
		$result = array();
		$references = array();
	
		// loop over elements / properties
		foreach ($object as $key => $value)
		{
			// recursively convert objects
			if (is_object($value) || is_array($value))
			{
				// but prevent cycles by caching object references
				if (!in_array($value, $references))
				{
					$result[$key] = self::arrayForObject($value);
					$references[] = $value;
				}
			}
			else
			{
				// this is not an object or array
				// save the value to the result unmodified
				$result[$key] = $value;
			}
		}
		return $result;
	}
}
?>