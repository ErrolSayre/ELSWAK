<?php
/*
	ELSWebAppKit MySQL Conditional
	
	This class contains the data necessary to make a MySQL conditional statement
	such as TRUE, FALSE, table.field = table.file, table.field < value, value !=
	table.field, etc.
*/
require_once('ELSWebAppKit/MySQL/Field.php');
require_once('ELSWebAppKit/MySQL/Boolean.php');
require_once('ELSWebAppKit/MySQL/Field.php');
require_once('ELSWebAppKit/MySQL/Literal.php');
require_once('ELSWebAppKit/MySQL/Operator.php');
require_once('ELSWebAppKit/MySQL/String.php');

class ELSWebAppKit_MySQL_Conditional
	implements ELSWebAppKit_MySQL_Expression
{
	protected $leftSide;
	protected $operator;
	protected $rightSide;
	
	public function __construct(ELSWebAppKit_MySQL_Expression $leftSide, ELSWebAppKit_MySQL_Operator $operator = null, ELSWebAppKit_MySQL_Expression $rightSide = null)
	{
		// setup default values
		$this->leftSide = null;
		$this->operator = null;
		$this->rightSide = null;
		
		$this->setLeftSide($leftSide);
		$this->setOperator($operator);
		$this->setRightSide($rightSide);
	}
	public function leftSide()
	{
		return $this->leftSide;
	}
	public function setLeftSide(ELSWebAppKit_MySQL_Expression $leftSide)
	{
		$this->leftSide = $leftSide;
		return $this->leftSide;
	}
	public function operator()
	{
		return $this->operator;
	}
	public function setOperator(ELSWebAppKit_MySQL_Operator $operator)
	{
		$this->operator = $operator;
		return $this->operator;
	}
	public function rightSide()
	{
		return $this->rightSide;
	}
	public function setRightSide(ELSWebAppKit_MySQL_Expression $rightSide)
	{
		$this->rightSide = $rightSide;
		return $this->rightSide;
	}
	public function sql($format = 'table.field', $indent = '')
	{
		// build the sql for this condition
		$sql = '';
		
		// add the left side
		if ($this->leftSide !== null)
		{
			$sql .= $this->leftSide->sql($format, $indent);
		}
		
		// add the operator
		if ($this->operator !== null)
		{
			$sql .= ' '.$this->operator->sql($format, $indent);
		}
		
		// add the right side
		if ($this->rightSide !== null)
		{
			$sql .= ' '.$this->rightSide->sql($format, $indent);
		}
		
		return $sql;
	}
}
?>