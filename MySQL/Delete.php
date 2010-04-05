<?php
/*
	ELSWAK MySQL Delete
	
	DELETE query as an object...
*/
require_once('ELSWAK/MySQL/Table.php');
require_once('ELSWAK/MySQL/Where/Clause.php');
require_once('ELSWAK/MySQL/Order/Clause.php');
require_once('ELSWAK/MySQL/Limit/Clause.php');
class ELSWAK_MySQL_Delete
{
	protected $table;
	protected $whereClause;
	protected $orderClause;
	protected $limitClause;
	protected $lowPriority;
	protected $quick;
	protected $ignore;
	
	public function __construct
	(
		ELSWAK_MySQL_Table $table = null,
		ELSWAK_MySQL_Where_Clause $whereClause = null,
		ELSWAK_MySQL_Order_Clause $orderClause = null,
		ELSWAK_MySQL_Limit_Clause $limitClause = null,
		$priority = null,
		$quick = null,
		$ignore = null
	)
	{
		$this->setTable
		(
			($table instanceOf ELSWAK_MySQL_Table)?
				$table:
				new ELSWAK_MySQL_Table('', new ELSWAK_MySQL_Database(''))
		);
		$this->setWhereClause($whereClause);
		$this->setOrderClause($orderClause);
		$this->setLimitClause($limitClause);
		$this->setPriority($priority);
		$this->setQuick($quick);
		$this->setIgnore($ignore);
	}
	public function table()
	{
		return $this->table;
	}
	public function setTable(ELSWAK_MySQL_Table $table)
	{
		$this->table = $table;
		return $this;
	}
	public function whereClause()
	{
		return $this->whereClause;
	}
	public function setWhereClause(ELSWAK_MySQL_Where_Clause $whereClause = null)
	{
		$this->whereClause = $whereClause;
		return $this;
	}
	public function orderClause()
	{
		return $this->orderClause;
	}
	public function setOrderClause(ELSWAK_MySQL_Order_Clause $orderClause = null)
	{
		$this->orderClause = $orderClause;
		return $this;
	}
	public function limitClause()
	{
		return $this->limitClause;
	}
	public function setLimitClause(ELSWAK_MySQL_Limit_Clause $limitClause = null)
	{
		$this->limitClause = $limitClause;
		return $this;
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
		return $this;
	}
	public function quick()
	{
		return $this->quick;
	}
	public function setQuick($quick = true)
	{
		if ($quick === true)
		{
			$this->quick = true;
		}
		else
		{
			$this->quick = false;
		}
		return $this;
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
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = 'DELETE';
		
		// determine if we have a priority
		if ($this->priority !== null)
		{
			$sql .= ' '.$this->priority;
		}
		
		// determine if this query should execute quickly (i.e. not do cleanup immediately)
		if ($this->quick)
		{
			$sql .= ' QUICK';
		}
		
		// determine if this query should ignore errors
		if ($this->ignore)
		{
			$sql .= ' IGNORE';
		}
		$sql .= LF;
		
		// add the table
		$sql .= $indent.'FROM '.$this->table->sql($format, $indent.'  ').LF;
		
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
		$sql = substr($sql, 0, -1).';';
		
		// return this sql
		return $sql;
	}
}
?>