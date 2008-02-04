<?php
/*
	ClassTester
	
	This script was devloped to provide a simple and reusable way to test the
	properties, methods, and exception handling of classes.
	
	To use ClassTester, please supply the variables below and include this
	script in your test file.
	
	$className - name of the class as used for constructing the class
	$classPath - path accessible by this script to the file containing your class
	$classData - array of test data for use to create objects of your class
		'constructor parameters' - an array of values to be listed in the
			constructor method of your class
		'property values' - an associative array of values keyed by the
			property name (please note that this tester expects your class to
			use the accepted <property>() and set<Property>() style of getter/
			setter methods
		'methods' - an associative array of method names and associated values
			to be tested for the given object (please note that methods that
			should be tested on all objects should be listed in the
			$classMethods variable
	$classMethods - methods to be tested for each object
*/
// verify the class file exists
if (isset($classPath))
{
	// include the file
	echo '<p>Including Class File</p>'.LF;
	
	include_once($classPath);
}

// determine if the class exists
if (class_exists($className))
{
	// the class is good
	echo '<h1>Testing class: '.$className.'</h1>'.LF;
	
	// process through the class data
	if (isset($classData)	&&
		is_array($classData))
	{
		$classDataCount = 0;
		foreach ($classData as $objectData)
		{
			$classDataCount++;
			echo '<h2>Class Data Item '.$classDataCount.'</h2>'.LF;
			echo '<p><b>Creating new '.$className.'</b>'.BR.LF;
			
			// determine if this class contains constructor values
			if (isset($objectData['constructor parameters']))
			{
				// try to construct the object with the parameters
				
				// collect the parameters into a comma separated list
				$parameterList = '';
				foreach ($objectData['constructor parameters'] as $value)
				{
					if (is_array($value)	||
						is_object($value))
					{
						$parameterList .= 'unserialize(\''.serialize($value).'\'), ';
					}
					else if (is_null($value))
					{
						$parameterList .= 'null, ';
					}
					else
					{
						$parameterList .= '"'.$value.'", ';
					}
				}
				
				// strip the last 2 characters off
				$parameterList = substr($parameterList, 0, -2);
				echo '&nbsp;&nbsp;&nbsp;&nbsp;Using arguments ('.$parameterList.')'.BR.LF;
				try
				{
					$object = eval('return new '.$className.'('.$parameterList.');');
				}
				catch (Exception $e)
				{
					echo '&nbsp;&nbsp;&nbsp;&nbsp;Exception! '.$e->getMessage().BR.LF;
					// just create the default object
					echo '&nbsp;&nbsp;&nbsp;&nbsp;Using empty constructor.'.BR.LF;
					$object = new $className();
				}
			}
			else
			{
				// just create the default object
				echo '&nbsp;&nbsp;&nbsp;&nbsp;Using empty constructor.'.BR.LF;
				$object = new $className();
			}
			
			// output the constructed object
			echo '<b>Constructed Object</b>'.BR.LF;
			print_r_html($object);
			
			// test each of the supplied properties
			if (isset($objectData['property values'])	&&
				is_array($objectData['property values']))
			{
				foreach ($objectData['property values'] as $property => $value)
				{
					// try to set the value using the appropriate method
					echo '<b>Testing Property: '.$property.'</b>'.BR.LF;
					echo '&nbsp;&nbsp;&nbsp;&nbsp;Getting value: ';
					$result = $object->{$property}();
					if ((is_array($result)) || (is_object($result)))
					{
						print_r_html($result);
						echo LF;
					}
					else
					{
						echo htmlentities($result).BR.LF;
					}
					echo '&nbsp;&nbsp;&nbsp;&nbsp;Setting value: ';
					if ((is_array($value)) || (is_object($value)))
					{
						print_r_html($value);
						echo LF;
					}
					else
					{
						echo htmlentities($value).BR.LF;
					}
					try
					{
						$object->{'set'.$property}($value);
						echo '&nbsp;&nbsp;&nbsp;&nbsp;Getting value: ';
						$result = $object->{$property}();
						if ((is_array($result)) || (is_object($result)))
						{
							print_r_html($result);
							echo LF;
						}
						else
						{
							echo htmlentities($result).BR.LF;
						}
					}
					catch (Exception $e)
					{
						echo $e->getMessage().BR.LF;
					}
				}
			}
			
			// test each of the supplied additional methods
			if (isset($objectData['additional methods'])	&&
				is_array($objectData['additional methods']))
			{
				foreach ($objectData['additional methods'] as $method => $value)
				{
					// try to call the method with the supplied value
					echo '<b>Testing Method: '.$method.'</b>'.BR.LF;
					try
					{
						if ($value !== null)
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;Method Returns:'.LF;
							echo htmlentities($object->{$method}($value)).LF;
							echo 'given value "'.htmlentities($value).'": '.BR.LF;
						}
						else
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;Method Returns: '.LF;
							echo htmlentities($object->{$method}()).BR.LF;
						}
					}
					catch (Exception $e)
					{
						echo $e->getMessage().BR.LF;
					}
				}
			}
			
			// now test the standard suite of methods
			if (isset($classMethods)	&&
				is_array($classMethods))
			{
				foreach ($classMethods as $method => $value)
				{
					// try to call the method with the supplied value
					echo '<b>Testing Method: '.$method.'</b>'.BR.LF;
					try
					{
						if ($value !== null)
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;Method Returns:'.LF;
							$result = $object->{$method}($value);
							if ((is_array($result)) || (is_object($result)))
							{
								print_r_html($result);
								echo LF;
							}
							else
							{
								echo htmlentities($result).BR.LF;
							}
							echo 'given value "'.htmlentities($value).'": '.BR.LF;
						}
						else
						{
							echo '&nbsp;&nbsp;&nbsp;&nbsp;Method Returns: '.LF;
							$result = $object->{$method}();
							if ((is_array($result)) || (is_object($result)))
							{
								print_r_html($result);
								echo LF;
							}
							else
							{
								echo htmlentities($result).BR.LF;
							}
						}
					}
					catch (Exception $e)
					{
						echo $e->getMessage().BR.LF;
					}
				}
			}
			
			// output the constructed object
			echo '<b>Final Object</b>'.BR.LF;
			print_r_html($object);
			echo '</p>'.LF;
		}
	}
	else
	{
		echo '<h1>No class data provided</h1>'.LF;
	}
}
else
{
	echo '<h1>Class "'.$className.'" does not exist</h1>'.LF;
}

// define a helpful function
if (!function_exists('print_r_html'))
{
	function print_r_html($item)
	{
		echo '<pre>';
		print_r($item);
		echo '</pre>';
	}
}
?>