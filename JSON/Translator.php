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
			$input = self::arrayForObject($input);
		
		return json_encode($input);
	}
	public static function decode($input)
	{
		return json_decode($input);
	}
	public static function arrayForObject($object)
	{
		$result = serialize($object);
		$result = preg_replace('/O:\d+:".+?"/', 'a', $result);
		if (preg_match_all('/s:\d+:"\\0.+?\\0(.+?)"/', $result, $matches, PREG_SET_ORDER))
			foreach ($matches as $match)
				$result = str_replace($match[0], 's:'.strlen($match[1]).':"'.$match[1].'"', $result);
		return @unserialize($result);
	}
}
?>