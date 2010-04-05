<?php
require_once('ELSWAK/MySQL/Select/Clause.php');

echo '<h1>Creating new Select Clause</h1>'.LF;
$clause = new ELSWAK_MySQL_Select_Clause();
print_r_html($clause->sql());

echo '<h1>Adding fields to clause</h1>'.LF;
$clause->addField(new ELSWAK_MySQL_Field('PROPOSAL_ID', new ELSWAK_MySQL_Table('proposals', new ELSWAK_MySQL_Database('GrahamCracker')), 'int'));
print_r_html($clause->sql());
$clause->addField(new ELSWAK_MySQL_Field('proposal_title', new ELSWAK_MySQL_Table('proposals', new ELSWAK_MySQL_Database('GrahamCracker')), 'int'));
print_r_html($clause->sql('database.table.field'));
print_r_html($clause);

echo '<h1>Removing field from external array</h1>'.LF;
$fields = $clause->fields();
unset($fields[0]);
echo '<h2>External Array</h2>'.LF;
print_r_html($fields);
echo '<h2>Object</h2>'.LF;
print_r_html($clause);

echo '<h1>Accessing first field</h1>'.LF;
print_r_html($clause->fieldForKey(0));

echo '<h1>Removing first field</h1>'.LF;
$clause->removeFieldForKey(0);
print_r_html($clause);

echo '<h1>Accessing first field</h1>'.LF;
print_r_html($clause->fieldForKey(0));
?>