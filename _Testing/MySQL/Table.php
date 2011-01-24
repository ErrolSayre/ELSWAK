<?php
// test the Database Table
include('ELSWAK/MySQL/Database.php');
$className = 'ELSWAK_MySQL_Table';
$classPath = 'ELSWAK/MySQL/Table.php';

// set test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'name' => 'proposals',
			'database' => new ELSWAK_MySQL_Database('ProposalNator'),
			'primaryKey' => 'PROPOSAL_ID'
		),
	),
	array
	(
		'constructor parameters' => array
		(
			'name' => 'proposal_places',
			'database' => new ELSWAK_MySQL_Database('ProposalNator'),
			'primaryKey' => 'PROPOSAL_ID, PLACE_ID'
		),
		'additional methods' => array
		(
			'setPrimaryKey' => array
			(
				'PROPOSAL_ID',
				'place_name'
			)
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'name' => 'special_fields_long_times',
			'database' => new ELSWAK_MySQL_Database('ARG')
		)
	)
);
$classMethods = array
(
	'prettyName' => null,
	'name' => null
);
// include the class tester
include('ELSWAK/Utilities/ClassTester.php');
?>
