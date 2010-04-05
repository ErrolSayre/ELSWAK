<?php
require_once('DummyConnection.php');
require_once('ELSWAK/MySQL/Field.php');
require_once('ELSWAK/MySQL/Conditional.php');
require_once('ELSWAK/MySQL/Operator.php');
require_once('ELSWAK/MySQL/String.php');
$className = 'ELSWAK_MySQL_Conditional';
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			new ELSWAK_MySQL_Field
			(
				'field',
				new ELSWAK_MySQL_Table
				(
					'table',
					new ELSWAK_MySQL_Database
					(
						'database'
					)
				),
				'int'
			),
			new ELSWAK_MySQL_Operator('IS NOT'),
			new ELSWAK_MySQL_Literal('NULL')
		),
		'additional methods' => array
		(
			'sql' => 'database.table.field'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			new ELSWAK_MySQL_Field
			(
				'PROPOSAL_ID',
				new ELSWAK_MySQL_Table
				(
					'proposals',
					new ELSWAK_MySQL_Database
					(
						'GrahamCracker'
					)
				),
				'int'
			),
			new ELSWAK_MySQL_Operator('='),
			new ELSWAK_MySQL_Field
			(
				'PROPOSAL_ID',
				new ELSWAK_MySQL_Table
				(
					'proposal_investigators',
					new ELSWAK_MySQL_Database
					(
						'GrahamCracker'
					)
				),
				'int'
			)
		),
		'additional methods' => array
		(
			'sql' => 'database.table.field'
		)
	),
	array
	(
		'constructor parameters' => array
		(
			new ELSWAK_MySQL_Field
			(
				'proposal_title',
				new ELSWAK_MySQL_Table
				(
					'proposals',
					new ELSWAK_MySQL_Database
					(
						'GrahamCracker'
					)
				),
				'int'
			),
			new ELSWAK_MySQL_Operator('LIKE'),
			new ELSWAK_MySQL_String
			(
				'%catfish%',
				$db
			)
		),
		'additional methods' => array
		(
			'sql' => 'database.table.field'
		)
	),
);
$classMethods = array
(
	'sql' => null
);
require_once('ELSWAK/Utilities/ClassTester.php');
?>