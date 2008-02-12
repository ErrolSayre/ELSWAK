<?php
/*
	ELSWebAppKit MySQL Select Clause
	
	This class represents the attributes that make up a MySQL SELECT clause.
*/
require_once('ELSWebAppKit/MySQL/Field.php');
class ELSWebAppKit_MySQL_Select_Clause
{
	protected $fields;
	
	public function __construct(array $fields = null)
	{
		$this->setFields
		(
			($fields !== null)?
				$fields:
				array()
		);
	}
	public function __toString()
	{
		return $this->sql();
	}
	public function fieldForKey($index)
	{
		if (isset($this->fields[$index]))
		{
			return $this->fields[$index];
		}
		else
		{
			throw new Exception('Invalid key: Field not found.');
		}
	}
	public function addField(ELSWebAppKit_MySQL_Field $field)
	{
		$this->fields[] = $field;
		return $this->fields[count($this->fields) - 1];
	}
	public function removeFieldForKey($index)
	{
		if (isset($this->fields[$index]))
		{
			array_splice($this->fields, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Field not removed.');
		}
	}
	public function fieldCount()
	{
		return count($this->fields);
	}
	public function fieldKeys()
	{
		return array_keys($this->fields);
	}
	public function fields()
	{
		return $this->fields;
	}
	public function setFields(array $fields)
	{
		$this->fields = array();
		
		foreach ($fields as $field)
		{
			if ($field instanceOf ELSWebAppKit_MySQL_Field)
			{
				$this->addField($field);
			}
		}
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		// build the SQL string
		$sql = 'SELECT'.LF;
		if (count($this->fields) > 0)
		{
			foreach ($this->fields as $field)
			{
				$sql .= $indent.'  '.$field->sql($format, $indent).','.LF;
			}
			
			// remove the last comma and line feed
			$sql = substr($sql, 0, -2);
		}
		else
		{
			$sql .= $indent.'  *';
		}
		
		// return the finished string
		return $sql;
	}
}