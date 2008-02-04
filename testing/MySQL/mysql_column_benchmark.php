<?php
/*
	This script determines what column types are most used in your system. By examining which column types you use you can modify the order of testing for these datatypes to minimize execution times for programatically determining the type of a column... I built this and used it once, but it may be helpful for you.
*/
// connect to the database
$connection = mysql_connect('localhost', 'backup');

// set up the comparison average
$comparisonSum = 0;
$comparisonRounds = 0;

// set up  the other variables needed
$maxComparisons = 0;
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
			
			// keep track of the number of comparisons needed to locate the
			//	field type
			$comparisonCount = 1;
			
			// determine the type
			// in order to match our other naming conventions, move
			//	the data to a new object
			$fieldData = null;
			$fieldData->name = $field->Field;
			
			// determine generic field type and other field attributes
			// using the specific field type from MySQL, determine our generic
			//	field type
			
			// we'll put the fields in order of "popularity" to minimize
			//	time spent in this section of code
			if (($comparisonCount++) && strpos(($field->Type), 'varchar') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'varchar';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'int') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'int';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'datetime') === 0)
			{
				// this is a date data type
				$fieldData->type = 'date';
				$fieldData->mysqlType = 'datetime';
				
				// determine the date format
				$fieldData->format = 'Y-m-d H:i:s';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'enum') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'enum';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'tinyint') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'tinyint';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'char') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'char';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'text') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'text';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'bigint') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'bigint';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'timestamp') === 0)
			{
				// this is a date data type
				$fieldData->type = 'date';
				$fieldData->mysqlType = 'timestamp';
				
				// determine the date format
				$fieldData->format = 'Y-m-d H:i:s';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'date') === 0)
			{
				// this is a date data type
				$fieldData->type = 'date';
				$fieldData->mysqlType = 'date';
				
				// determine the date format
				$fieldData->format = 'Y-m-d';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'double') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'double';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'tinyblob') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'tinyblob';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'longtext') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'longtext';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'smallint') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'smallint';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'blob') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'blob';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'set') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'set';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'mediumblob') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'mediumblob';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'longblob') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'longblob';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'tinytext') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'tinytext';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'mediumint') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'mediumint';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'year') === 0)
			{
				// this is a date data type
				$fieldData->type = 'date';
				$fieldData->mysqlType = 'year';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'mediumtext') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'mediumtext';
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'decimal') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'decimal';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'integer') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'integer';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'float') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'float';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'double precision') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'double precision';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'real') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'real';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'dec') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'dec';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'numeric') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'numeric';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'fixed') === 0)
			{
				// this is a numeric data type
				$fieldData->type = 'numeric';
				$fieldData->mysqlType = 'fixed';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ','));
				$fieldData->decimal = substr($field->Type, strpos($field->Type, ','), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'binary') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'binary';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'varbinary') === 0)
			{
				// this is a string data type
				$fieldData->type = 'string';
				$fieldData->mysqlType = 'varbinary';
				
				// determine the length of the field
				$fieldData->length = substr($field->Type, strpos($field->Type, '('), strpos($field->Type, ')'));
			}
			else if (($comparisonCount++) && strpos(($field->Type), 'time') === 0)
			{
				// this is a date data type
				$fieldData->type = 'date';
				$fieldData->mysqlType = 'time';
				
				// determine the date format
				$fieldData->format = 'Y-m-d H:i:s';
			}
			else
			{
				$fieldData->type = 'unknown';
			}
			
			// remove the one comparison
			$comparisonCount--;
			
			echo '<dd>'.$fieldData->mysqlType.' ('.$field->Type.'): '.$comparisonCount.'</dd>';
			
			// increment the count for this comparison count
			if (!isset($comparisonCounts[$comparisonCount]))
			{
				$comparisonCounts[$comparisonCount] = 0;
			}
			$comparisonCounts[$comparisonCount]++;
			
			// add to the average
			$comparisonSum += $comparisonCount;
			$comparisonRounds++;
			
			if ($comparisonCount > $maxComparisons)
			{
				$maxComparisons = $comparisonCount;
			}
			
			// increment the count for this field type
			if (!isset($totals[$fieldData->mysqlType]))
			{
				$totals[$fieldData->mysqlType] = 0;
			}
			$totals[$fieldData->mysqlType]++;
		}
		echo '</dl></dd>';
	}
	echo '</dl></dd>';
}
echo '</dl>';

// now print all the totals
echo '<h1>Field Type Counts</h1>';
arsort($totals);
foreach ($totals as $fieldType => $useCount)
{
	echo '<b>'.$fieldType.'</b> :'.$useCount.'<br>';
}

// output the comparison average
echo '<h2>Averages</h2>';
echo 'Average number of comparisons to identify field type: '.($comparisonSum / $comparisonRounds).'<br>';
echo 'Maximum number of comparisons to identify field type: '.$maxComparisons.'<br>';
echo '<h2>Counts</h2>';
arsort($comparisonCounts);
foreach ($comparisonCounts as $comparisons => $rounds)
{
	echo 'Fields identified in <b>'.$comparisons.'</b> comparison'.(($comparisons == 1) ? '': 's').': <b>'.$rounds.'</b><br>';
}
?>