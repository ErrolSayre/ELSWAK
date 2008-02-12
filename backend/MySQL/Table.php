<?php
/*
	ELSWebAppKit MySQL Table
	
	This class provides some basic information for an object representing a
	table in the database.
*/
require_once('ELSWebAppKit/MySQL/Database.php');
class ELSWebAppKit_MySQL_Table
{
	protected $name;
	protected $prettyName;
	protected $database;
	protected $primaryKey;
	
	public function __construct($name, ELSWebAppKit_MySQL_Database $database, $primaryKey = null)
	{
		$this->setName($name);
		$this->setDatabase($database);
		$this->setPrimaryKey($primaryKey);
	}
	public function name()
	{
		return $this->name;
	}
	protected function setName($name)
	{
		$this->name = $name;
		
		// break the name up and make it pretty
		$this->prettyName = '';
		$pieces = explode('_', $name);
		foreach ($pieces as $piece)
		{
			$this->prettyName .= ucfirst(strtolower($piece)).' ';
		}
		$this->prettyName = trim($this->prettyName);
		return $this;
	}
	public function prettyName()
	{
		return $this->prettyName;
	}
	public function database()
	{
		return $this->database;
	}
	protected function setDatabase(ELSWebAppKit_MySQL_Database $database)
	{
		$this->database = $database;
		return $this;
	}
	protected function primaryKey()
	{
		return $this->primaryKey;
	}
	protected function setPrimaryKey($primaryKey)
	{
		// reset the primary key
		$this->primaryKey = null;
		
		// determine if the key is a string or an array
		if (is_string($primaryKey))
		{
			// determine if this is a delimited list or a field name
			if (strpos($primaryKey, ',') !== false)
			{
				// this is a comma delimited list of field names
				// replace the primary key with an empty array
				$this->primaryKey = array();
				
				// process each of the field names
				$fieldNames = explode(',', $primaryKey);
				foreach ($fieldNames as $fieldName)
				{
					$this->primaryKey[] = trim($fieldName);
				}
			}
			else
			{
				// assume this is a field name
				$this->primaryKey = trim($fieldName);
			}
		}
		else if (is_array($primaryKey))
		{
			// replace the primary key with an empty array
			$this->primaryKey = array();
			
			// process each item to see if it is a string
			foreach ($primaryKey as $fieldName)
			{
				if (is_string($fieldName))
				{
					$this->primaryKey[] = trim($fieldName);
				}
			}
		}
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		if (strpos($format, 'database') !== false)
		{
			return $this->database->sql($format).'.`'.$this->name.'`';
		}
		else
		{
			return '`'.$this->name.'`';
		}
	}
}
?>