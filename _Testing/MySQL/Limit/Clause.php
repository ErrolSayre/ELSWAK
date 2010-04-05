<?php
require_once('ELSWAK/MySQL/Limit/Clause.php');
$className = 'ELSWAK_MySQL_LimitClause';
$classData = array
(
	array
	(
		'constructor parameters' => array
		(
			12,
			null
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
			0,
			0
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
			123883,
			10000
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
			999
		),
		'additional methods' => array
		(
			'sql' => 'database.table.field'
		)
	)
);
$classMethods = array
(
	'sql' => null
);
require_once('ELSWAK/Utilities/ClassTester.php');
?>