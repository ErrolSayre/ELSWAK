<?php
/*
	ELSWebAppKit XML Translator
	
	This class translates iterable objects into XML representations.
*/
class ELSWebAppKit_XML_Translator
{
	public static function encode(DOMDocument $document, $item, $tagName = null)
	{
		// determine if we have a tag name provided
		if ($tagName == null)
		{
			if (is_object($item))
			{
				if (method_exists($item, 'classXMLTagName'))
				{
					$tagName = $item->classXMLTagName();
				}
				else
				{
					$tagName = get_class($item);
				}
			}
			else
			{
				$tagName = 'item';
			}
		}
		
		// create a container for this object
		$container = $document->createElement($tagName);
		
		// recursively add the contents of this object to the container
		self::recursiveEncode($document, $item, $container);
		// return the finished encoding
		return $container;
	}
	public static function recursiveEncode(DOMDocument $document, $item, DOMElement $container)
	{
		if (is_object($item) || is_array($item))
		{
			// loop over the properties
			foreach ($item as $key => $value)
			{
				// append this property to the container
				if (is_int($key))
				{
					$key = 'item';
				}
				$element = $container->appendChild($document->createElement($key));
				
				// add the contents of this property to the new element
				self::recursiveEncode($document, $value, $element);
			}
		}
		else
		{
			// append this value to the container
			$container->appendChild($document->createTextNode($item));
		}
	}
}
?>