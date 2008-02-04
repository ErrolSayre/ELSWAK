<?php
/*
	ELSWebAppKit MySQL Update
	
	This class represents a standard MySQL UPDATE query.
	
	For multiple table updates, see MySQL Update Multiple
*/
require_once('ELSWebAppKit/MySQL/Table.php');
require_once('ELSWebAppKit/MySQL/Field/Value.php');
require_once('ELSWebAppKit/MySQL/Where/Clause.php');
require_once('ELSWebAppKit/MySQL/Order/Clause.php');
require_once('ELSWebAppKit/MySQL/Limit/Clause.php');

class ELSWebAppKit_MySQL_Update
{
	protected $table;
	protected $fieldValues;
	protected $fieldNameIndex;
	protected $whereClause;
	protected $orderClause;
	protected $limitClause;
	protected $lowPriority;
	protected $ignore;
	
	public function __construct
	(
		ELSWebAppKit_MySQL_Table $table = null,
		array $fieldValues = null,
		ELSWebAppKit_MySQL_Where_Clause $whereClause = null,
		ELSWebAppKit_MySQL_Order_Clause $orderClause = null,
		ELSWebAppKit_MySQL_Limit_Clause $limitClause = null,
		$priority = null,
		$ignore = null
	)
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
		$this->setWhereClause($whereClause);
		$this->setOrderClause($orderClause);
		$this->setLimitClause($limitClause);
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
	public function whereClause()
	{
		return $this->whereClause;
	}
	public function setWhereClause(ELSWebAppKit_MySQL_Where_Clause $whereClause = null)
	{
		$this->whereClause = $whereClause;
		return $this->whereClause;
	}
	public function orderClause()
	{
		return $this->orderClause;
	}
	public function setOrderClause(ELSWebAppKit_MySQL_Order_Clause $orderClause = null)
	{
		$this->orderClause = $orderClause;
		return $this->orderClause;
	}
	public function limitClause()
	{
		return $this->limitClause;
	}
	public function setLimitClause(ELSWebAppKit_MySQL_Limit_Clause $limitClause = null)
	{
		$this->limitClause = $limitClause;
		return $this->limitClause;
	}
	public function priority()
	{
		return $this->priority;
	}
	public function setPriority($priority)
	{
		if ($priority === null)
		{
			$this->priority = null;
		}
		else if (strtoupper($priority) == 'LOW_PRIORITY')
		{
			$this->priority = 'LOW_PRIORITY';
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
		$sql = 'UPDATE';
		
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
		$sql .= $indent.'  '.$this->table->sql($format, $indent.'  ').LF;
		
		// now add each of the field and value pairs
		$sql .= $indent.'SET'.LF;
		foreach ($this->fieldValues as $fieldValue)
		{
			// look for the field in its.. place
			$sql .= $indent.'  '.$fieldValue->field()->sql($format, $indent.'  ').' = '.$fieldValue->value()->sql($format, $indent.'  ').','.LF;
		}
		
		// remove the trailing comma and line feed
		$sql = substr($sql, 0, -2).LF;
		
		// add the where clause
		if ($this->whereClause !== null)
		{
			$sql .= $indent.$this->whereClause()->sql($format, $indent).LF;
		}
		
		// add the order clause
		if ($this->orderClause !== null)
		{
			$sql .= $indent.$this->orderClause()->sql($format, $indent).LF;
		}
		
		// add the limit clause
		if ($this->limitClause !== null)
		{
			$sql .= $indent.$this->limitClause()->sql($format, $indent).LF;
		}
		
		// remove the last line feed and add the semicolon
		$sql = trim($sql).';';
		
		// return this sql
		return $sql;
	}
}