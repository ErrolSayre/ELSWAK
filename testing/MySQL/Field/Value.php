<?php
// test the Database Field Value class
require_once('../DummyConnection.php');
include('ELSWebAppKit/MySQL/Field.php');
include('ELSWebAppKit/MySQL/String.php');
include('ELSWebAppKit/MySQL/Literal.php');
$className = 'ELSWebAppKit_MySQL_Field_Value';
$classPath = 'ELSWebAppKit/MySQL/Field/Value.php';

// set test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'field' => new ELSWebAppKit_MySQL_Field
			(
				'field_name',
				new ELSWebAppKit_MySQL_Table
				(
					'table_name',
					new ELSWebAppKit_MySQL_Database('TheDatabase')
				),
				'int(13) unsigned'
			),
			'value' => new ELSWebAppKit_MySQL_String('here’s something cool for everyone.', $db)
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
			'field' => new ELSWebAppKit_MySQL_Field
			(
				'field_name',
				new ELSWebAppKit_MySQL_Table
				(
					'table_name',
					new ELSWebAppKit_MySQL_Database('TheDatabase')
				),
				'int(13) unsigned'
			),
			'value' => new ELSWebAppKit_MySQL_String('stuff is really cool in here;select * from users;I wish things were better.', $db)
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
			'field' => new ELSWebAppKit_MySQL_Field
			(
				'mark_down_goodnews',
				new ELSWebAppKit_MySQL_Table
				(
					'prices',
					new ELSWebAppKit_MySQL_Database('Store')
				),
				'enum(\'yes\',\'no\')'
			),
			'value' => new ELSWebAppKit_MySQL_Literal(19)
		),
		'additional methods' => array
		(
			'sql' => null
		)
	),
);
$classMethods = array
(
	'sql' => 'database.table.field'
);
// include the class tester
include('Area51/ClassTester.php');
?>
