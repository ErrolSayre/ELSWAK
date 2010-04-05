<?php
// include some files
require_once('DummyConnection.php');
require_once('ELSWAK/MySQL/Insert.php');
require_once('ELSWAK/MySQL/Literal.php');
require_once('ELSWAK/MySQL/String.php');

// set up some data
$grahamCracker = new ELSWAK_MySQL_Database('GrahamCracker');
$proposals = new ELSWAK_MySQL_Table('proposals', $grahamCracker);

// create a new insert query
echo '<h1>Create a new insert query</h1>'.LF;
$insert = new ELSWAK_MySQL_Insert
(
	$proposals,
	array
	(
		new ELSWAK_MySQL_Field_Value
		(
			new ELSWAK_MySQL_Field
			(
				'PROPOSAL_ID',
				$proposals,
				'int'
			),
			new ELSWAK_MySQL_Literal(10002)
		),
		new ELSWAK_MySQL_Field_Value
		(
			new ELSWAK_MySQL_Field
			(
				'proposal_title',
				$proposals,
				'varchar (255)'
			),
			new ELSWAK_MySQL_String("Here's the title for this great, fantastic, dealio", $db)
		)
	)
);
print_r_html($insert->sql(''));

// test some of the methods
echo '<h1>Change a field value pair</h1>'.LF;
$insert->fieldValueForFieldName('PROPOSAL_ID')->setValue(new ELSWAK_MySQL_Literal(2388));
print_r_html($insert->sql(''));

echo '<h1>Add a field value pair</h1>'.LF;
$insert->addFieldValue
(
	new ELSWAK_MySQL_Field_Value
	(
		new ELSWAK_MySQL_Field
		(
			'date_submitted',
			$proposals,
			'datetime'
		),
		new ELSWAK_MySQL_String('2004-06-05 23:56:35', $db)
	)
);
print_r_html($insert->sql(''));

echo '<h1>Remove a field value pair</h1>'.LF;
$insert->removeFieldValueForFieldName('PROPOSAL_ID');
print_r_html($insert->sql(''));

echo '<h1>Default Query Format</h1>'.LF;
print_r_html($insert->sql(''));
echo '<h1>field</h1>'.LF;
print_r_html($insert->sql('field'));
echo '<h1>table.field</h1>'.LF;
print_r_html($insert->sql('table.field'));
echo '<h1>database.table.field</h1>'.LF;
print_r_html($insert->sql('database.table.field'));
?>