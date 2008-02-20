<?php
require_once('DataGeneral/JSON/Translator/Model.php');
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
	)
);
echo '<h1>JSON encode of array</h1>'.LF;
echo $translator->encode($array).LF;

// create a new object
echo '<h1>Loading Person</h1>'.LF;
require_once('DataGeneral/Person/MySQL/StoreCoordinator.php');
$person = DataGeneral_Person_Store_Coordinator::load(1, 'complete');
print_r_html($person);

echo '<h1>JSON encode of person</h1>'.LF;
$jsonPerson = $translator->encode($person);
echo $jsonPerson.LF;

echo '<h1>JSON decode of JSON encoded person</h1>'.LF;
$objPerson = $translator->decode($jsonPerson);
print_r_html($objPerson);
?>