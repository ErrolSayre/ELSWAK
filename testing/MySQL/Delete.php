<?php
/*
	MySQL Delete Tester
*/
require_once('DummyConnection.php');
require_once('ELSWebAppKit/MySQL/Delete.php');
// try to create a new delete query
echo '<h1>Creating new query with empty constructor</h1>';
$query = new ELSWebAppKit_MySQL_Delete();
print_r_html($query);

// add a table
echo '<h2>Adding table</h2>';
$proposals = new ELSWebAppKit_MySQL_Table('proposals', new ELSWebAppKit_MySQL_Database('GrahamCracker'));
$query->setTable($proposals);
print_r_html($query);

// add a where clause
echo '<h2>Adding where clause</h2>';
$query->setWhereClause(new ELSWebAppKit_MySQL_Where_Clause());
$query->whereClause()->addCondition(new ELSWebAppKit_MySQL_Conditional(new ELSWebAppKit_MySQL_Field('PERSON_ID', $proposals, 'int(10)'), new ELSWebAppKit_MySQL_Operator('='), new ELSWebAppKit_MySQL_Literal(25)));
$query->whereClause()->addCondition(new ELSWebAppKit_MySQL_Conditional_Group(array(new ELSWebAppKit_MySQL_Conditional(new ELSWebAppKit_MySQL_Field('date_entered', $proposals, 'date'), new ELSWebAppKit_MySQL_Operator('>'), new ELSWebAppKit_MySQL_String('2005-06-25', $db)),new ELSWebAppKit_MySQL_Conditional(new ELSWebAppKit_MySQL_Field('date_entered', $proposals, 'date'), new ELSWebAppKit_MySQL_Operator('<='), new ELSWebAppKit_MySQL_String('2006-06-25', $db)))));
print_r_html($query);

// add an order clause
echo '<h2>Adding where clause</h2>';
$query->setOrderClause(new ELSWebAppKit_MySQL_Order_Clause());
$query->orderClause()->addOrdinal(new ELSWebAppKit_MySQL_Ordinal(new ELSWebAppKit_MySQL_Field('date_entered', $proposals, 'date')));

// output the sql
echo '<h2>Query output</h2>';
print_r_html($query->sql());
?>