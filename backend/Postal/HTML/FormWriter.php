<?php
/*
	ELSWebAppKit Address HTML Form Writer
*/
require_once('ELSWebAppKit/HTML/Document.php');
require_once('ELSWebAppKit/Postal/Address.php');
class ELSWebAppKit_Postal_Address_HTML_FormWriter
{
	public static function standardForm(ELSWebAppKit_HTML_Document $document, ELSWebAppKit_Postal_Address $address = null, $inputNamePrefix = '', $inputIdPrefix = '')
	{
		// create a default object if necessary
		if ($address === null)
		{
			$address = new ELSWebAppKit_Postal_Address();
		}
		
		// set up the prefixes
		if ($inputIdPrefix == '')
		{
			$inputIdPrefix = $inputNamePrefix;
		}
	}
	public static function standardFields(ELSWebAppKit_HTML_Document $document, ELSWebAppKit_Postal_Address $address, $inputNamePrefix, $inputIdPrefix)
	{
		// create a new fieldset element set to contain the fields
		$container = $document->createElement('div');
		
		// add a form field for the lines
		$field = $container->appendChild
		(
			$document->createFormField
			(
				'Lines',
				$document->createTextInput
				(
					$inputNamePrefix.'[lines][1]',
					$address->line(1),
					$inputIdPrefix.'Line1',
					25,
					50
				)
			)
		);
		$field->appendChild
		(
			$document->createTextInput
			(
				$inputNamePrefix.'[lines][2]',
				$address->line(2),
				$inputIdPrefix.'Line2',
				25,
				50
			)
		);
		$field->appendChild
		(
			$document->createTextInput
			(
				$inputNamePrefix.'[lines][3]',
				$address->line(3),
				$inputIdPrefix.'Line3',
				25,
				50
			)
		);
		$field->appendChild
		(
			$document->createTextInput
			(
				$inputNamePrefix.'[lines][4]',
				$address->line(4),
				$inputIdPrefix.'Line4',
				25,
				50
			)
		);
		
		// return the finished view
		return $container;
	}
}
?>