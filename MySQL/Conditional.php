<?php
/*
	ELSWAK MySQL Conditional
	
	This class contains the data necessary to make a MySQL conditional statement
	such as TRUE, FALSE, table.field = table.file, table.field < value, value !=
	table.field, etc.
*/
require_once('ELSWAK/MySQL/Field.php');
require_once('ELSWAK/MySQL/Boolean.php');
require_once('ELSWAK/MySQL/Field.php');
require_once('ELSWAK/MySQL/Literal.php');
require_once('ELSWAK/MySQL/Operator.php');
require_once('ELSWAK/MySQL/String.php');

class ELSWAK_MySQL_Conditional
	implements ELSWAK_MySQL_Expression
{
	protected $leftSide;
	protected $operator;
	protected $rightSide;
	
	public function __construct(ELSWAK_MySQL_Expression $leftSide, ELSWAK_MySQL_Operator $operator = null, ELSWAK_MySQL_Expression $rightSide = null)
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
	public function setLeftSide(ELSWAK_MySQL_Expression $leftSide)
	{
		$this->leftSide = $leftSide;
		return $this;
	}
	public function operator()
	{
		return $this->operator;
	}
	public function setOperator(ELSWAK_MySQL_Operator $operator)
	{
		$this->operator = $operator;
		return $this;
	}
	public function rightSide()
	{
		return $this->rightSide;
	}
	public function setRightSide(ELSWAK_MySQL_Expression $rightSide)
	{
		$this->rightSide = $rightSide;
		return $this;
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