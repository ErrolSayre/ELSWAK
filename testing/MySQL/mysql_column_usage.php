<?php
// connect to the database
$connection = mysql_connect('localhost', 'backup');

// initialize some variables
$totals = array();

// first get a list of the database
echo '<h1>Gathering Database Information</h1>';
$databaseQuery = 'SHOW DATABASES;';
$databaseResult = mysql_query($databaseQuery, $connection);
echo '<dl>';
while ($database = mysql_fetch_object($databaseResult))
{
	// output this database
	echo '<dt>'.$database->Database.'</dt>';
	
	// use this database
	mysql_select_db($database->Database, $connection);
	
	// query for the tables in this database
	$tableQuery = 'SHOW TABLES;';
	$tableResult = mysql_query($tableQuery, $connection);
	echo '<dd><dl>';
	while ($table = mysql_fetch_object($tableResult))
	{
		// get the table name, because MySQL has to be all screwy...
		$tableName = $table->{'Tables_in_'.$database->Database};
		
		// output this table
		echo '<dt>'.$tableName.'</dt>';
		
		// query for the fields in this database
		$fieldQuery = 'SHOW COLUMNS FROM `'.$tableName.'`;';
		$fieldResult = mysql_query($fieldQuery, $connection);
		echo '<dd><dl>';
		while ($field = mysql_fetch_object($fieldResult))
		{
			// output this field
			echo '<dt>'.$field->Field.'</dt>';
			
			// determine the type
			if (strpos($field->Type, '(') > -1)
				$fieldType = substr($field->Type, 0, strpos($field->Type, '('));
			else
				$fieldType = $field->Type;
			
			// increment the count for this field type
			if (!isset($totals[$fieldType]))
			{
				$totals[$fieldType] = 0;
			}
			$totals[$fieldType]++;
		}
		echo '</dl></dd>';
	}
	echo '</dl></dd>';
}
echo '</dl>';

// now print all the totals
echo '<h1>Field Type Occurrence Totals</h1>';
arsort($totals);
foreach ($totals as $fieldType => $useCount)
{
	echo ''.$fieldType.': <b>'.$useCount.'</b><br>';
}
?>