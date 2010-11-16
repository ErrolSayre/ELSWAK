<?php
// test the Database Model
$className = 'ELSWAK_MySQL_Database';
$classPath = 'ELSWAK/MySQL/Database.php';

// set test data
$classData = array(
	array(
		'constructor parameters' => array(
			'GrahamCracker',
			'research.olemiss.edu',
			'gcuser',
			'gcpasswords are great'
		),
		'additional methods' => array(
			'prettyName' => null,
			'name' => null
		)
	),
	array(
		'constructor parameters' => array(
			'iCookie',
			'localhost',
			'icookieman',
			''
		),
	),
);
$classMethods = array(
	'prettyName' => null,
	'name' => null
);

// include the class tester
require_once 'ELSWAK/Utilities/ClassTester.php';