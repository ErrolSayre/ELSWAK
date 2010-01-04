<?php
require_once('ELSWebAppKit/MySQL/Field.php');
require_once('ELSWebAppKit/MySQL/Conditional.php');
require_once('ELSWebAppKit/MySQL/Conditional/Group.php');
require_once('ELSWebAppKit/MySQL/Operator.php');
require_once('ELSWebAppKit/MySQL/Literal.php');
$className = 'ELSWebAppKit_MySQL_Conditional_Group';
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			array
			(
				new ELSWebAppKit_MySQL_Conditional
				(
					new ELSWebAppKit_MySQL_Field
					(
						'PROPOSAL_ID',
						new ELSWebAppKit_MySQL_Table
						(
							'proposals',
							new ELSWebAppKit_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'int'
					),
					new ELSWebAppKit_MySQL_Operator('IS NOT'),
					new ELSWebAppKit_MySQL_Literal('NULL')
				),
				new ELSWebAppKit_MySQL_Conditional
				(
					new ELSWebAppKit_MySQL_Field
					(
						'proposal_title',
						new ELSWebAppKit_MySQL_Table
						(
							'proposals',
							new ELSWebAppKit_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'string'
					),
					new ELSWebAppKit_MySQL_Operator('LIKE'),
					new ELSWebAppKit_MySQL_Literal('%yourmom%')
				),
				new ELSWebAppKit_MySQL_Conditional
				(
					new ELSWebAppKit_MySQL_Field
					(
						'award_amount',
						new ELSWebAppKit_MySQL_Table
						(
							'awards',
							new ELSWebAppKit_MySQL_Database
							(
								'MyDatabase'
							)
						),
						'double'
					),
					new ELSWebAppKit_MySQL_Operator('>'),
					new ELSWebAppKit_MySQL_Literal('0.00')
				),
				new ELSWebAppKit_MySQL_Conditional_Group
				(
					array
					(
						new ELSWebAppKit_MySQL_Conditional
						(
							new ELSWebAppKit_MySQL_Field
							(
								'date_start',
								new ELSWebAppKit_MySQL_Table
								(
									'proposals',
									new ELSWebAppKit_MySQL_Database
									(
										'MyDatabase'
									)
								),
								'date'
							),
							new ELSWebAppKit_MySQL_Operator('<'),
							new ELSWebAppKit_MySQL_Literal('2008-08-01 00:00:00')
						),
						new ELSWebAppKit_MySQL_Conditional
						(
							new ELSWebAppKit_MySQL_Field
							(
								'date_end',
								new ELSWebAppKit_MySQL_Table
								(
									'proposals',
									new ELSWebAppKit_MySQL_Database
									(
										'MyDatabase'
									)
								),
								'string'
							),
							new ELSWebAppKit_MySQL_Operator('>'),
							new ELSWebAppKit_MySQL_Literal('2008-08-01 00:00:00')
						),
					),
					new ELSWebAppKit_MySQL_Conjunction('OR')
				)
			),
			new ELSWebAppKit_MySQL_Conjunction('AND')
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
require_once('ELSWebAppKit/Utilities/ClassTester.php');
?>