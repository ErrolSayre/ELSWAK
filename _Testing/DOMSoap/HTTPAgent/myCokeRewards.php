<?php
/*
	This script uses the MyCokeRewards web service to check balances and submit codes using the ELSWebAppKit DOMSoap HTTPClient.
*/

// construct a new dom document to match our mycokerewards request
$document = new DOMDocument();
$document->loadXML('<?xml version="1.0"?>'.LF.'<methodCall></methodCall>');

// add the method name
if (isset($_POST['code']) && ($_POST['code'] != 'Cap Code') && ($_POST['code'] != ''))
{
	$document->documentElement->appendChild($document->createElement('methodName', 'points.enterCode'));
}
else
{
	$document->documentElement->appendChild($document->createElement('methodName', 'points.pointsBalance'));
}

// add the parameter list to the struct
$struct = $document->documentElement->appendChild($document->createElement('params'))->appendChild($document->createElement('param'))->appendChild($document->createElement('value'))->appendChild($document->createElement('struct'));

// add a member for each item required
if (isset($_POST['email']))
{
	// email
	$member = $struct->appendChild($document->createElement('member'));
	$member->appendChild($document->createElement('name', 'emailAddress'));
	$member->appendChild($document->createElement('value'))->appendChild($document->createElement('string', $_POST['email']));
	
	if (isset($_POST['password']))
	{
		// password
		$member = $struct->appendChild($document->createElement('member'));
		$member->appendChild($document->createElement('name', 'password'));
		$member->appendChild($document->createElement('value'))->appendChild($document->createElement('string', $_POST['password']));
		
		if (isset($_POST['username']))
		{
			// screenname
			$member = $struct->appendChild($document->createElement('member'));
			$member->appendChild($document->createElement('name', 'screenName'));
			$member->appendChild($document->createElement('value'))->appendChild($document->createElement('string', $_POST['username']));
			
			// cap code (if provided)
			if (isset($_POST['code']))
			{
				$member = $struct->appendChild($document->createElement('member'));
				$member->appendChild($document->createElement('name', 'capCode'));
				$member->appendChild($document->createElement('value'))->appendChild($document->createElement('string', $_POST['code']));
			}
			
			// version
			$member = $struct->appendChild($document->createElement('member'));
			$member->appendChild($document->createElement('name', 'VERSION'));
			$member->appendChild($document->createElement('value'))->appendChild($document->createElement('string', '3.0'));
			
			// issue this command to the webservice
			require_once('ELSWebAppKit/DOMSoap/HTTPClient.php');
			$soapClient = new ELSWebAppKit_DOMSoap_HTTPClient('secure.mycokerewards.com', 443, '/xmlrpc', null, true);
			echo $soapClient->makeRequest($document->saveXML());
		}
	}
}
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input type="text" name="email" value="email login" />
	<input type="text" name="username" value="my coke rewards screen name" />
	<input type="password" name="password" value="password"/>
	<input type="code" name="code" value="Cap Code" />
	<input type="submit" value="GO" />
</form>