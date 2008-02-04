<?php
require_once('DummyConnection.php');
require_once('ELSWebAppKit/MySQL/Field.php');
require_once('ELSWebAppKit/MySQL/Conditional.php');
require_once('ELSWebAppKit/MySQL/Operator.php');
require_once('ELSWebAppKit/MySQL/String.php');
$className = 'ELSWebAppKit_MySQL_Conditional';
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			new ELSWebAppKit_MySQL_Field
			(
				'field',
				new ELSWebAppKit_MySQL_Table
				(
					'table',
					new ELSWebAppKit_MySQL_Database
					(
						'database'
					)
				),
				'int'
			),
			new ELSWebAppKit_MySQL_Operator('IS NOT'),
			new ELSWebAppKit_MySQL_Literal('NULL')
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
			new ELSWebAppKit_MySQL_Field
			(
				'PROPOSAL_ID',
				new ELSWebAppKit_MySQL_Table
				(
					'proposals',
					new ELSWebAppKit_MySQL_Database
					(
						'GrahamCracker'
					)
				),
				'int'
			),
			new ELSWebAppKit_MySQL_Operator('='),
			new ELSWebAppKit_MySQL_Field
			(
				'PROPOSAL_ID',
				new ELSWebAppKit_MySQL_Table
				(
					'proposal_investigators',
					new ELSWebAppKit_MySQL_Database
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
			new ELSWebAppKit_MySQL_Field
			(
				'proposal_title',
				new ELSWebAppKit_MySQL_Table
				(
					'proposals',
					new ELSWebAppKit_MySQL_Database
					(
						'GrahamCracker'
					)
				),
				'int'
			),
			new ELSWebAppKit_MySQL_Operator('LIKE'),
			new ELSWebAppKit_MySQL_String
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
require_once('ELSWebAppKit/Utilities/ClassTester.php');
?>