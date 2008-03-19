<?php
/*
	ELSWebAppKit Postal Address HTML Display Writer
*/
require_once('ELSWebAppKit/HTML/Document.php');
require_once('ELSWebAppKit/Postal/Address.php');
class ELSWebAppKit_Postal_Address_HTML_DisplayWriter
{
	public static function standardView(ELSWebAppKit_HTML_Response $response, ELSWebAppKit_Postal_Address $address)
	{
		return self::multipleLineView($response, $address);
	}
	public static function inlineView(ELSWebAppKit_HTML_Response $response, ELSWebAppKit_Postal_Address $address)
	{
		// grab the normal view
		$view = self::multipleLineView($response, $address);
		
		// set the address class to inline, css will take care of the rest
		$view->setAttribute('class', 'inline');
		
		// return the finished view
		return $view;
	}
	public static function multipleLineView(ELSWebAppKit_HTML_Response $response, ELSWebAppKit_Postal_Address $address)
	{
		// create a new address element to contain the view
		$viewContainer = $response->document()->createElement('address');
		
		// add the lines
		foreach ($address->lines() as $line)
		{
			// create a new container for this line
			$viewContainer->appendChild($response->document()->createElement('div', $line))->setAttribute('class', 'line');
		}
		
		// add the city state and zip
		$viewContainer->appendChild($response->document()->createElement('div', $address->city()))->setAttribute('class', 'city');
		$viewContainer->appendChild($response->document()->createElement('div', $address->state()))->setAttribute('class', 'state');
		$viewContainer->appendChild($response->document()->createElement('div', $address->postal()))->setAttribute('class', 'postal');
		
		// add the country
		$viewContainer->appendChild($response->document()->createElement('div', $address->country()))->setAttribute('class', 'country');
		
		// return the finished view
		return $viewContainer;
	}
}
?>