<?php
/*
	ELSWebAppKit MySQL Field
	
	This class represents the data associated with a column from a MySQL table.
*/
require_once('ELSWebAppKit/MySQL/Expression.php');
require_once('ELSWebAppKit/MySQL/Table.php');
class ELSWebAppKit_MySQL_Field
	implements ELSWebAppKit_MySQL_Expression
{
	protected $name;
	protected $prettyName;
	protected $table;
	protected $dataType;
	protected $dataFormat;
	protected $dataLength;
	protected $permissibleValues;
	protected $presentationType;
	
	public function __construct($name, ELSWebAppKit_MySQL_Table $table, $mysqlType = null)
	{
		$this->setName($name);
		$this->setTable($table);
		if ($mysqlType !== null)
		{
			$this->setAttributesFromMySQLType($mysqlType);
		}
	}
	public function name()
	{
		return $this->name;
	}
	public function setName($name)
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
	public function table()
	{
		return $this->table;
	}
	protected function setTable(ELSWebAppKit_MySQL_Table $table)
	{
		$this->table = $table;
		return $this;
	}
	public function database()
	{
		return $this->table->database();
	}
	public function dataType()
	{
		return $this->dataType;
	}
	protected function setDataType($dataType)
	{
		$this->dataType = $dataType;
		return $this;
	}
	public function dataFormat()
	{
		return $this->dataFormat;
	}
	protected function setDataFormat($dataFormat)
	{
		$this->dataFormat = $dataFormat;
		return $this;
	}
	public function dataLength()
	{
		return $this->dataLength;
	}
	protected function setDataLength($dataLength)
	{
		$this->dataLength = $dataLength;
		return $this;
	}
	public function permissibleValues()
	{
		return $this->permissibleValues;
	}
	protected function addPermissibleValue($value)
	{
		if (!isset($this->permissibleValueIndex[$value]))
		{
			$this->permissibleValueIndex[$value] = count($this->permissibleValues);
			$this->permissibleValues[] = $value;
		}
	}
	public function isPermissibleValue($value)
	{
		if (isset($this->permissibleValueIndex[$value]))
		{
			return true;
		}
		return false;
	}
	public function presentationType()
	{
		return $this->presentationType;
	}
	protected function setPresentationType($presentationType)
	{
		$this->presentationType = $presentationType;
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		if (strpos($format, 'table') !== false)
		{
			return $this->table->sql($format).'.`'.$this->name.'`';
		}
		else
		{
			return '`'.$this->name.'`';
		}
	}
	protected function setAttributesFromMySQLType($mysqlTypeString)
	{
		if (strpos($mysqlTypeString, 'varchar') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('varchar');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
		}
		else if (strpos($mysqlTypeString, 'int') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('int');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'datetime') === 0)
		{
			// this is a date data type
			$this->setPresentationType('date');
			$this->setDataType('datetime');
			
			// determine the date format
			$this->setDataFormat('Y-m-d H:i:s');
		}
		else if (strpos($mysqlTypeString, 'enum') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('enum');
			
			// break out the possible values
			$values = explode(',', $this->extractParentheticalString($mysqlTypeString));
			foreach ($values as $value)
			{
				// strip off the quotes around the values
				$this->addPermissibleValue(substr($value, 1, -1));
			}
		}
		else if (strpos($mysqlTypeString, 'tinyint') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('tinyint');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'char') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('char');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
		}
		else if (strpos($mysqlTypeString, 'text') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('text');
		}
		else if (strpos($mysqlTypeString, 'bigint') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('bigint');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'timestamp') === 0)
		{
			// this is a date data type
			$this->setPresentationType('date');
			$this->setDataType('timestamp');
			
			// determine the date format
			$this->setDataFormat('Y-m-d H:i:s');
		}
		else if (strpos($mysqlTypeString, 'date') === 0)
		{
			// this is a date data type
			$this->setPresentationType('date');
			$this->setDataType('date');
			
			// determine the date format
			$this->setDataFormat('Y-m-d');
		}
		else if (strpos($mysqlTypeString, 'double') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('double');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'tinyblob') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('tinyblob');
		}
		else if (strpos($mysqlTypeString, 'longtext') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('longtext');
		}
		else if (strpos($mysqlTypeString, 'smallint') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('smallint');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'blob') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('blob');
		}
		else if (strpos($mysqlTypeString, 'set') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('set');
			
			// break out the possible values
			$values = explode(',', $this->extractParentheticalString($mysqlTypeString));
			foreach ($values as $value)
			{
				// strip off the quotes around the values
				$this->addPermissibleValue(substr($value, 1, -1));
			}
		}
		else if (strpos($mysqlTypeString, 'mediumblob') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('mediumblob');
		}
		else if (strpos($mysqlTypeString, 'longblob') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('longblob');
		}
		else if (strpos($mysqlTypeString, 'tinytext') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('tinytext');
		}
		else if (strpos($mysqlTypeString, 'mediumint') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('mediumint');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'year') === 0)
		{
			// this is a date data type
			$this->setPresentationType('date');
			$this->setDataType('year');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
		}
		else if (strpos($mysqlTypeString, 'mediumtext') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('mediumtext');
		}
		else if (strpos($mysqlTypeString, 'decimal') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('decimal');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'integer') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('integer');
			
			// determine the length of the field
			$this->setDataLength($this->extractParentheticalString($mysqlTypeString));
			
			// determine the format of the field
			if (strpos($mysqlTypeString, 'unsigned') > -1)
			{
				$this->setDataFormat('unsigned');
			}
			else
			{
				$this->setDataFormat('signed');
			}
		}
		else if (strpos($mysqlTypeString, 'float') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('float');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'double precision') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('double precision');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'real') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('real');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'dec') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('dec');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'numeric') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('numeric');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'fixed') === 0)
		{
			// this is a numeric data type
			$this->setPresentationType('numeric');
			$this->setDataType('fixed');
			
			// determine the length of the field
			$mysqlLength = $this->extractParentheticalString($mysqlTypeString);
			$this->setDataLength(substr($mysqlLength, 0, strpos($mysqlTypeString, ',')));
			$this->setDataFormat('%01.'.substr($mysqlLength, strpos($mysqlLength, ',')));
		}
		else if (strpos($mysqlTypeString, 'binary') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('binary');
			
			// determine the length of the field
			$this->setDataLength(substr($mysqlTypeString, strpos($mysqlTypeString, '('), strpos($mysqlTypeString, ')')));
		}
		else if (strpos($mysqlTypeString, 'varbinary') === 0)
		{
			// this is a string data type
			$this->setPresentationType('string');
			$this->setDataType('varbinary');
			
			// determine the length of the field
			$this->setDataLength(substr($mysqlTypeString, strpos($mysqlTypeString, '('), strpos($mysqlTypeString, ')')));
		}
		else if (strpos($mysqlTypeString, 'time') === 0)
		{
			// this is a date data type
			$this->setPresentationType('date');
			$this->setDataType('time');
			
			// determine the date format
			$this->setDataFormat('Y-m-d H:i:s');
		}
		else
		{
			$this->setPresentationType('string');
		}
		return $this;
	}
	public static function extractParentheticalString($string)
	{
		// trim the string to the opening parenthesis
		$string = substr($string, strpos($string, '(') + 1);
		// trim the string to the closing parenthesis
		$string = substr($string, 0, strpos($string, ')'));
		// return the value
		return $string;
	}
}
?>