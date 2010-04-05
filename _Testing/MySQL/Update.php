<?php
/*
	MySQL Update Tester
*/
require_once('DummyConnection.php');
require_once('ELSWAK/MySQL/Update.php');
// try to create a new update query
echo '<h1>Creating new query with empty constructor</h1>';
$query = new ELSWAK_MySQL_Update();
print_r_html($query);

// add a table
echo '<h2>Adding table</h2>';
$proposals = new ELSWAK_MySQL_Table('proposals', new ELSWAK_MySQL_Database('GrahamCracker'));
$query->setTable($proposals);
print_r_html($query);

// add a field value
echo '<h2>Adding person id</h2>';
$query->addFieldValue(new ELSWAK_MySQL_Field_Value(new ELSWAK_MySQL_Field('PERSON_ID', $proposals, 'int(10)'), new ELSWAK_MySQL_Literal(25)));
print_r_html($query);

// add a field value
echo '<h2>Adding proposal title</h2>';
$query->addFieldValue(new ELSWAK_MySQL_Field_Value(new ELSWAK_MySQL_Field('project_title', $proposals, 'varchar(255)'), new ELSWAK_MySQL_String('Your mom has the ugliest research project evar!', $db)));
print_r_html($query);

// add a where clause
echo '<h2>Adding where clause</h2>';
$query->setWhereClause(new ELSWAK_MySQL_Where_Clause());
$query->whereClause()->addCondition(new ELSWAK_MySQL_Conditional(new ELSWAK_MySQL_Field('PERSON_ID', $proposals, 'int(10)'), new ELSWAK_MySQL_Operator('='), new ELSWAK_MySQL_Literal(25)));
$query->whereClause()->addCondition(new ELSWAK_MySQL_Conditional_Group(array(new ELSWAK_MySQL_Conditional(new ELSWAK_MySQL_Field('date_entered', $proposals, 'date'), new ELSWAK_MySQL_Operator('>'), new ELSWAK_MySQL_String('2005-06-25', $db)),new ELSWAK_MySQL_Conditional(new ELSWAK_MySQL_Field('date_entered', $proposals, 'date'), new ELSWAK_MySQL_Operator('<='), new ELSWAK_MySQL_String('2006-06-25', $db)))));
print_r_html($query);

// output the sql
echo '<h2>Query output</h2>';
print_r_html($query->sql());
?>