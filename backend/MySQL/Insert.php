<?php
/*
	ELSWebAppKit MySQL Insert
	
	This class represents a MySQL insert query.
*/
require_once('ELSWebAppKit/MySQL/Field/Value.php');
class ELSWebAppKit_MySQL_Insert
{
	protected $table;
	protected $fieldValues;
	protected $fieldNameIndex;
	protected $priority;
	protected $ignore;
	
	public function __construct(ELSWebAppKit_MySQL_Table $table = null, array $fieldValues = null, $priority = null, $ignore = null)
	{
		$this->setTable
		(
			($table instanceOf ELSWebAppKit_MySQL_Table)?
				$table:
				new ELSWebAppKit_MySQL_Table('', new ELSWebAppKit_MySQL_Database(''))
		);
		$this->fieldNameIndex = array();
		$this->setFieldValues
		(
			is_array($fieldValues)?
				$fieldValues:
				array()
		);
		$this->setPriority($priority);
		$this->setIgnore($ignore);
	}
	public function table()
	{
		return $this->table;
	}
	public function setTable(ELSWebAppKit_MySQL_Table $table)
	{
		$this->table = $table;
		return $this->table;
	}
	public function fieldValueForKey($index)
	{
		if (isset($this->fieldValues[$index]))
		{
			return $this->fieldValues[$index];
		}
		else
		{
			throw new Exception('Invalid key: Field Value Pair not found.');
		}
	}
	public function fieldValueForFieldName($fieldName)
	{
		if (isset($this->fieldNameIndex[$fieldName]))
		{
			return $this->fieldValueForKey($this->fieldNameIndex[$fieldName]);
		}
		else
		{
			throw new Exception('Invalid Field Name: Field Value Pair not found.');
		}
	}
	public function addFieldValue(ELSWebAppKit_MySQL_Field_Value $fieldValue)
	{
		// determine if the field is already in the list
		if (isset($this->fieldNameIndex[$fieldValue->field()->name()]))
		{
			// update the entry for this field value
			$this->fieldValues[$this->fieldNameIndex[$fieldValue->field()->name()]] = $fieldValue;
		}
		else
		{
			// add the new field value pair
			$this->fieldNameIndex[$fieldValue->field()->name()] = count($this->fieldValues);
			$this->fieldValues[] = $fieldValue;
		}
		return $this->fieldValues[$this->fieldNameIndex[$fieldValue->field()->name()]];
	}
	public function removeFieldValueForKey($index)
	{
		if (isset($this->fieldValues[$index]))
		{
			// remove the name index also
			unset($this->fieldNameIndex[$this->fieldValues[$index]->field()->name()]);
			array_splice($this->fieldValues, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Field Value Pair not removed.');
		}
	}
	public function removeFieldValueForFieldName($fieldName)
	{
		if (isset($this->fieldNameIndex[$fieldName]))
		{
			$this->removeFieldValueForKey($this->fieldNameIndex[$fieldName]);
		}
		else
		{
			throw new Exception('Invalid Field Name: Field Value Pair not removed.');
		}
	}
	public function fieldValueCount()
	{
		return count($this->fieldValues);
	}
	public function fieldValueKeys()
	{
		return array_keys($this->fieldValues);
	}
	public function fieldValues()
	{
		return $this->fieldValues;
	}
	public function setFieldValues(array $fieldValues)
	{
		$this->fieldValues = array();
		$this->fieldNameIndex = array();
		
		foreach ($fieldValues as $fieldValue)
		{
			if ($fieldValue instanceOf ELSWebAppKit_MySQL_Field_Value)
			{
				$this->addFieldValue($fieldValue);
			}
		}
	}
	public function priority()
	{
		return $this->priority;
	}
	public function setPriority($priority)
	{
		// set up a test value
		$testValue = strtoupper($priority);
		
		if ($priority === null)
		{
			$this->priority = null;
		}
		else if ($testValue == 'LOW_PRIORITY')
		{
			$this->priority = 'LOW_PRIORITY';
		}
		else if ($testValue == 'HIGH_PRIORITY')
		{
			$this->priority = 'HIGH_PRIORITY';
		}
		else if ($testValue == 'DELAYED')
		{
			$this->priority = 'DELAYED';
		}
	}
	public function ignore()
	{
		return $this->ignore;
	}
	public function setIgnore($ignore = true)
	{
		if ($ignore === true)
		{
			$this->ignore = true;
		}
		else
		{
			$this->ignore = false;
		}
	}
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = 'INSERT';
		
		// determine if we have a priority
		if ($this->priority !== null)
		{
			$sql .= ' '.$this->priority;
		}
		
		// determine if this query should ignore errors
		if ($this->ignore)
		{
			$sql .= ' IGNORE';
		}
		$sql .= LF;
		
		// add the table
		$sql .= $indent.'  INTO '.$this->table->sql($format, $indent.'  ').LF;
		
		// now add each of the field and value pairs
		$fieldSql = '';
		$valueSql = '';
		foreach ($this->fieldValues as $fieldValue)
		{
			// look for the field in its.. place
			$fieldSql .= $indent.'    '.$fieldValue->field()->sql($format, $indent.'    ').','.LF;
			
			// look for the value for this field
			$valueSql .= $indent.'    '.$fieldValue->value()->sql($format, $indent.'    ').','.LF;
		}
		
		// now add the field sql removing the last comma and line feed
		if ($fieldSql != '')
		{
			$sql .= $indent.'  ('.LF.
				substr($fieldSql, 0, -2).LF.
				$indent.'  )'.LF;
		}
		
		// now add the values sql
		if ($valueSql != '')
		{
			$sql .= $indent.'  VALUES'.LF.
				$indent.'  ('.LF.
				substr($valueSql, 0, -2).LF.
				$indent.'  )'.LF;
		}
		
		// remove the last line feed and add the semicolon
		$sql = substr($sql, 0, -1).';';
		
		// return this sql
		return $sql;
	}
}