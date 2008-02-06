<?php
/*
	ELSWebAppKit Store Search Criteria
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_Store_Search_Criteria
	extends ELSWebAppKit_Iterable
{
	protected $property;
	protected $value;
	protected $operation;
	
	// member listing for iterator methods
	protected $members = array
	(
		'property',
		'value',
		'operation'
	);
	
	public function __construct($property, $value = '', $operation = 'equalTo')
	{
		$this->setProperty($property);
		$this->setValue($value);
		$this->setOperation($operation);
	}
	public function property()
	{
		return $this->property;
	}
	public function setProperty($property)
	{
		$this->property = $property;
	}
	public function value()
	{
		return $this->value;
	}
	public function setValue($value)
	{
		$this->value = $value;
	}
	public function operation()
	{
		return $this->operation;
	}
	public function setOperation($operation)
	{
		$operation = strtolower($operation);
		
		if (($operation == '=') || ($operation == 'equalto') || ($operation == 'equals') || ($operation == 'equal') || ($operation == 'is'))
		{
			$this->operation = 'equalTo';
		}
		else if (($operation == '<') || ($operation == 'lessthan'))
		{
			$this->operation = 'lessThan';
		}
		else if (($operation == '<=') || ($operation == 'lessthanequalto'))
		{
			$this->operation = 'lessThanEqualTo';
		}
		else if (($operation == '>=') || ($operation == 'greaterthanequalto'))
		{
			$this->operation = 'greaterThanEqualTo';
		}
		else if (($operation == '>') || ($operation == 'greaterthan'))
		{
			$this->operation = 'greaterThan';
		}
		else if (($operation == '!=') || ($operation == 'notequalto') || ($operation == 'isnot'))
		{
			$this->operation = 'notEqualTo';
		}
		else if (($operation == '%=') || ($operation == 'like'))
		{
			$this->operation = 'like';
		}
		else if (($operation == '!%=') || ($operation == 'notlike'))
		{
			$this->operation = 'notLike';
		}
		else if (($operation == '*%') || ($operation == 'startswith'))
		{
			$this->operation = 'startsWith';
		}
		else if (($operation == '%*%') || ($operation == 'contains'))
		{
			$this->operation = 'contains';
		}
		else if (($operation == '%*') || ($operation == 'endswith'))
		{
			$this->operation = 'endsWith';
		}
		else
		{
			$this->operation = 'equalTo';
			throw new Exception('Invalid operation: operation reset to "equal to" ('.$operation.').');
		}
	}
	public function operator()
	{
		if ($this->operation == 'equalTo')
		{
			return '=';
		}
		else if ($this->operation == 'lessThan')
		{
			return '<';
		}
		else if ($this->operation == 'lessThanEqualTo')
		{
			return '<=';
		}
		else if ($this->operation == 'greaterThanEqualTo')
		{
			return '>=';
		}
		else if ($this->operation == 'greaterThan')
		{
			return '>';
		}
		else if ($this->operation == 'notEqualTo')
		{
			return '!=';
		}
		else if ($this->operation == 'like')
		{
			return 'LIKE';
		}
		else if ($this->operation == 'notLike')
		{
			return 'NOT LIKE';
		}
		else if ($this->operation == 'startsWith')
		{
			return 'LIKE';
		}
		else if ($this->operation == 'contains')
		{
			return 'LIKE';
		}
		else if ($this->operation == 'endsWith')
		{
			return 'LIKE';
		}
		
		return '=';
	}
	public function comparableValue()
	{
		if ($this->operation == 'startsWith')
		{
			return $this->value.'%';
		}
		else if ($this->operation == 'contains')
		{
			return '%'.$this->value.'%';
		}
		else if ($this->operation == 'endsWith')
		{
			return '%'.$this->value;
		}
		
		return $this->value;
	}
}