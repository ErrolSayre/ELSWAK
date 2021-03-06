<?php
/*
	ELSWAK Address HTML Form Writer
*/
require_once('ELSWAK/HTML/Document.php');
require_once('ELSWAK/Postal/Address.php');
class ELSWAK_Postal_Address_HTML_FormWriter
{
	public static function standardForm(ELSWAK_HTML_Response $response, ELSWAK_Postal_Address $address = null, $inputNamePrefix = '', $inputIdPrefix = '')
	{
		// create a default object if necessary
		if ($address === null)
		{
			$address = new ELSWAK_Postal_Address();
		}
		
		// set up the prefixes
		if ($inputIdPrefix == '')
		{
			$inputIdPrefix = $inputNamePrefix;
		}
	}
	public static function standardFields(ELSWAK_HTML_Response $response, ELSWAK_Postal_Address $address, $inputNamePrefix, $inputIdPrefix)
	{
		// create a new fieldset element set to contain the fields
		$container = $response->document()->createElement('div');
		
		// add a form field for the lines
		$field = $container->appendChild
		(
			$response->document()->createFormField
			(
				'Lines',
				$response->document()->createTextInput
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
			$response->document()->createTextInput
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
			$response->document()->createTextInput
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
			$response->document()->createTextInput
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