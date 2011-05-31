<?php
// test the ELSWAK Phone Number class
$className = 'ELSWAK_Phone_Number';
$classPath = 'ELSWAK/Phone/Number.php';

// specify some test data
$classData = array (
	array (
		'constructor parameters' => array (
			'number' => '662-211-8988x25'
		),
		'property values' => array (
			'number' => 'email@domain.tld'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => 'emailiscool'
		),
		'property values' => array (
			'number' => 'WE BUY HOUSES'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '(662) 911-2583'
		),
		'property values' => array (
			'number' => '(662) 913.6328'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '1.800.859.3289'
		),
		'property values' => array (
			'number' => '1-800-FIX-CRAP'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '859.3289.123444'
		),
		'property values' => array (
			'number' => '38809939399'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '258-1696'
		),
		'property values' => array (
			'number' => '555-5555'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '+81816694337992'
		),
		'property values' => array (
			'number' => '+582123935115'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '+116019827807'
		),
		'property values' => array (
			'number' => '+18690491'
		)
	),
	array (
		'constructor parameters' => array (
			'number' => '+116625344232'
		),
		'property values' => array (
			'number' => '+116625882323'
		)
	),
);

// include the class tester
include 'ELSWAK/Utilities/ClassTester.php';
