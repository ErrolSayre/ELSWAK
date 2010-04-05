<?php
require_once('ELSWAK/MySQL/Field.php');
require_once('ELSWAK/MySQL/Conditional.php');
require_once('ELSWAK/MySQL/Conditional/Group.php');
require_once('ELSWAK/MySQL/Operator.php');
require_once('ELSWAK/MySQL/Literal.php');
$className = 'ELSWAK_MySQL_Conditional_Group';
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			array
			(
				new ELSWAK_MySQL_Conditional
				(
					new ELSWAK_MySQL_Field
					(
						'PROPOSAL_ID',
						new ELSWAK_MySQL_Table
						(
							'proposals',
							new ELSWAK_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'int'
					),
					new ELSWAK_MySQL_Operator('IS NOT'),
					new ELSWAK_MySQL_Literal('NULL')
				),
				new ELSWAK_MySQL_Conditional
				(
					new ELSWAK_MySQL_Field
					(
						'proposal_title',
						new ELSWAK_MySQL_Table
						(
							'proposals',
							new ELSWAK_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'string'
					),
					new ELSWAK_MySQL_Operator('LIKE'),
					new ELSWAK_MySQL_Literal('%yourmom%')
				),
				new ELSWAK_MySQL_Conditional
				(
					new ELSWAK_MySQL_Field
					(
						'award_amount',
						new ELSWAK_MySQL_Table
						(
							'awards',
							new ELSWAK_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'double'
					),
					new ELSWAK_MySQL_Operator('>'),
					new ELSWAK_MySQL_Literal('0.00')
				),
				new ELSWAK_MySQL_Conditional_Group
				(
					array
					(
						new ELSWAK_MySQL_Conditional
						(
							new ELSWAK_MySQL_Field
							(
								'date_start',
								new ELSWAK_MySQL_Table
								(
									'proposals',
									new ELSWAK_MySQL_Database
									(
										'MyDatabase'
									)
								),
								'date'
							),
							new ELSWAK_MySQL_Operator('<'),
							new ELSWAK_MySQL_Literal('2008-08-01 00:00:00')
						),
						new ELSWAK_MySQL_Conditional
						(
							new ELSWAK_MySQL_Field
							(
								'date_end',
								new ELSWAK_MySQL_Table
								(
									'proposals',
									new ELSWAK_MySQL_Database
									(
										'MyDatabase'
									)
								),
								'string'
							),
							new ELSWAK_MySQL_Operator('>'),
							new ELSWAK_MySQL_Literal('2008-08-01 00:00:00')
						),
					),
					new ELSWAK_MySQL_Conjunction('OR')
				)
			),
			new ELSWAK_MySQL_Conjunction('AND')
		),
		'additional methods' => array
		(
			'conditionCount' => null,
			'sql' => 'database.table.field'
		)
	),
);
$classMethods = array
(
	'conditionForKey' => 0,
	'removeConditionForKey' => 0,
	'conditionCount' => null,
	'sql' => null
);
require_once('ELSWAK/Utilities/ClassTester.php');
?>