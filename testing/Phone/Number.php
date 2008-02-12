<?php
// test the ELSWebAppKit Phone Number class
$className = 'ELSWebAppKit_Phone_Number';
$classPath = 'ELSWebAppKit/Phone/Number.php';

// specify some test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'number' => '662-211-8988x25'
		),
		'property values' => array
		(
			'number' => 'email@domain.tld'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'number' => 'emailiscool'
		),
		'property values' => array
		(
			'number' => 'WE BUY HOUSES'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'number' => '(662) 911-2583'
		),
		'property values' => array
		(
			'number' => '(662) 913.6328'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'number' => '1.800.859.3289'
		),
		'property values' => array
		(
			'number' => '1-800-FIX-CRAP'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'number' => '859.3289.123444'
		),
		'property values' => array
		(
			'number' => '38809939399'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'number' => '258-1696'
		),
		'property values' => array
		(
			'number' => '555-5555'
		)
	)
);

// include the class tester
include('ELSWebAppKit/Utilities/ClassTester.php');
?>