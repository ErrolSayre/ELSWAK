<?php
/*
	ELSWebAppKit MySQL Table Join
*/
require_once('ELSWebAppKit/MySQL/Operator.php');
require_once('ELSWebAppKit/MySQL/Table.php');
require_once('ELSWebAppKit/MySQL/Conditional/Group.php');
class ELSWebAppKit_MySQL_Table_Join
{
	protected $type;
	protected $direction;
	protected $table;
	protected $conditions;
	
	public function __construct($type = null, $direction = 'LEFT', ELSWebAppKit_MySQL_Table $table = null, ELSWebAppKit_MySQL_Conditional_Group $conditions = null)
	{
		$this->setType($type);
		$this->setDirection($direction);
		$this->setTable
		(
			($table !== null)?
				$table:
				new ELSWebAppKit_MySQL_Table()
		);
		$this->setConditions
		(
			($conditions !== null)?
				$conditions:
				new ELSWebAppKit_MySQL_Conditional_Group
				(
					array(),
					new ELSWebAppKit_MySQL_Conjunction('AND')
				)
		);
	}
	public function type()
	{
		return $this->type;
	}
	public function setType($type)
	{
		$type = strtoupper($type);
		
		if ($type == 'INNER')
		{
			$this->type = 'INNER';
		}
		else if ($type == 'CROSS')
		{
			$this->type = 'CROSS';
		}
		else
		{
			$this->type = null;
		}
		return $this;
	}
	public function direction()
	{
		return $this->direction;
	}
	public function setDirection($direction = 'LEFT')
	{
		$direction = strtoupper($direction);
		
		if ($direction == 'LEFT')
		{
			$this->direction = 'LEFT';
		}
		else if ($direction == 'LEFT OUTER')
		{
			$this->direction = 'LEFT OUTER';
		}
		else if ($direction == 'RIGHT')
		{
			$this->direction = 'RIGHT';
		}
		else if ($direction == 'RIGHT OUTER')
		{
			$this->direction = 'RIGHT OUTER';
		}
		else if ($direction == 'NATURAL')
		{
			$this->direction = 'NATURAL';
		}
		else if ($direction == 'NATURAL LEFT')
		{
			$this->direction = 'NATURAL LEFT';
		}
		else if ($direction == 'NATURAL LEFT OUTER')
		{
			$this->direction = 'NATURAL LEFT OUTER';
		}
		else if ($direction == 'NATURAL RIGHT')
		{
			$this->direction = 'NATURAL RIGHT';
		}
		else if ($direction == 'NATURAL RIGHT OUTER')
		{
			$this->direction = 'NATURAL RIGHT OUTER';
		}
		else
		{
			$this->direction = 'STRAIGHT_JOIN';
		}
		return $this;
	}
	public function table()
	{
		return $this->table;
	}
	public function setTable(ELSWebAppKit_MySQL_Table $table)
	{
		$this->table = $table;
		return $this;
	}
	public function conditions()
	{
		return $this->conditions;
	}
	public function setConditions(ELSWebAppKit_MySQL_Conditional_Group $conditions)
	{
		$this->conditions = $conditions;
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = '';
		
		// add the join type if provided
		if ($this->type !== null)
		{
			$sql .= $this->type.' ';
		}
		
		// add the join direction
		$sql .= $this->direction;
		
		// add the table
		$sql .= ' JOIN '.$this->table->sql($format, $indent);
		
		// add the conditions if we have any
		if ($this->conditions->conditionCount() > 0)
		{
			$sql .= ' ON'.LF;
			$sql .= $indent.$this->conditions->sql($format, $indent);
		}
		
		// return the finished sql
		return $sql;
	}
}
?>