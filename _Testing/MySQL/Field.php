<?php
// test the Database Field
include('ELSWebAppKit/MySQL/Table.php');
$className = 'ELSWebAppKit_MySQL_Field';
$classPath = 'ELSWebAppKit/MySQL/Field.php';

// set test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'name' => 'proposal_account',
			'table' => new ELSWebAppKit_MySQL_Table('table_name', new ELSWebAppKit_MySQL_Database('TheDatabase')),
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
			'table' => new ELSWebAppKit_MySQL_Table('table_name', new ELSWebAppKit_MySQL_Database('TheDatabase')),
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
			'table' => new ELSWebAppKit_MySQL_Table('prices', new ELSWebAppKit_MySQL_Database('Store')),
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
