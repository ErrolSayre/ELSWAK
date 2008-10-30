<?php
require_once('ELSWebAppKit/JSON/Translator.php');
$translator = new ELSWebAppKit_JSON_Translator();

// create a complex array
echo '<h1>Building Multi-dimensional array</h1>'.LF;
$array = array
(
	'name' => array
	(
		'first' => 'Errol',
		'last' => 'Sayre'
	),
	'phone' => array
	(
		'Primary' => '662-915-6525'
	),
	'enabled' => true,
	'disabled' => false
);
echo '<h1>JSON encode of array</h1>'.LF;
echo $translator->encode($array).LF;

// create a new object
echo '<h1>Loading Person</h1>'.LF;
require_once('DataGeneral/Person/MySQL/StoreCoordinator.php');
$person = DataGeneral_Person_MySQL_StoreCoordinator::load(1, 'complete');
$person->enabled = true;
$person->disabled = false;
print_r_html($person);

echo '<h1>JSON encode of person</h1>'.LF;
$jsonPerson = $translator->encode($person);
echo $jsonPerson.LF;

echo '<h1>JSON decode of JSON encoded person</h1>'.LF;
$objPerson = $translator->decode($jsonPerson);
print_r_html($objPerson);
echo $objPerson->enabled? 'true': 'false';
echo $objPerson->disabled? 'true': 'false';
?>