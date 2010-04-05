<?php
// test the ELSWAK Postal Address class
$className = 'ELSWAK_Postal_Address';
$classPath = 'ELSWAK/Postal/Address.php';

$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'line1' => '30 Sorority Row',
			'line2' => null,
			'city' => 'University',
			'state' => 'MS',
			'postal' => '38677',
			'country' => 'U.S.A'
		),
		'additional methods' => array
		(
			'address' => null,
			'setCountry' => ''
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'line1' => 'Office of Research',
			'line2' => '125 Old Chemistry',
			'city' => 'University',
			'state' => 'MS',
			'postal' => '38677'
		)
	),
);

$classMethods = array
(
	'address' => null
);

// include the class tester
include('ELSWAK/Utilities/ClassTester.php');
