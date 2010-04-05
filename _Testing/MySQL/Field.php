<?php
// test the Database Field
include('ELSWAK/MySQL/Table.php');
$className = 'ELSWAK_MySQL_Field';
$classPath = 'ELSWAK/MySQL/Field.php';

// set test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'name' => 'proposal_account',
			'table' => new ELSWAK_MySQL_Table('table_name', new ELSWAK_MySQL_Database('TheDatabase')),
			'mysqlType' => 'int(13) unsigned'
		),
		'additional methods' => array
		(
			'sql' => null
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'name' => 'proposal_number',
			'table' => new ELSWAK_MySQL_Table('table_name', new ELSWAK_MySQL_Database('TheDatabase')),
			'mysqlType' => 'int(4) unsigne'
		),
		'additional methods' => array
		(
			'sql' => null
		)
	),
	array
	(
		'constructor parameters' => array
		(
			'name' => 'mark_down_goodnews',
			'table' => new ELSWAK_MySQL_Table('prices', new ELSWAK_MySQL_Database('Store')),
			'mysqlType' => 'enum(\'yes\',\'no\')'
		),
		'additional methods' => array
		(
			'sql' => null
		)
	),
);
$classMethods = array
(
	'prettyName' => null,
	'name' => null,
	'sql' => 'database.table.field',
);
// include the class tester
include('Area51/ClassTester.php');
?>
