<?php
// test the Database Field Value class
require_once('../DummyConnection.php');
include('ELSWAK/MySQL/Field.php');
include('ELSWAK/MySQL/String.php');
include('ELSWAK/MySQL/Literal.php');
$className = 'ELSWAK_MySQL_Field_Value';
$classPath = 'ELSWAK/MySQL/Field/Value.php';

// set test data
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			'field' => new ELSWAK_MySQL_Field
			(
				'field_name',
				new ELSWAK_MySQL_Table
				(
					'table_name',
					new ELSWAK_MySQL_Database('TheDatabase')
				),
				'int(13) unsigned'
			),
			'value' => new ELSWAK_MySQL_String('here’s something cool for everyone.', $db)
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
			'field' => new ELSWAK_MySQL_Field
			(
				'field_name',
				new ELSWAK_MySQL_Table
				(
					'table_name',
					new ELSWAK_MySQL_Database('TheDatabase')
				),
				'int(13) unsigned'
			),
			'value' => new ELSWAK_MySQL_String('stuff is really cool in here;select * from users;I wish things were better.', $db)
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
			'field' => new ELSWAK_MySQL_Field
			(
				'mark_down_goodnews',
				new ELSWAK_MySQL_Table
				(
					'prices',
					new ELSWAK_MySQL_Database('Store')
				),
				'enum(\'yes\',\'no\')'
			),
			'value' => new ELSWAK_MySQL_Literal(19)
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
include('ELSWAK/Utilities/ClassTester.php');
?>
