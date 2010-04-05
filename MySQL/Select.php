<?php
/*
	ELSWAK MySQL Select
	
	This class represents the attributes of a MySQL SELECT query.
*/
require_once('ELSWAK/MySQL/Select/Clause.php');
require_once('ELSWAK/MySQL/From/Clause.php');
require_once('ELSWAK/MySQL/Where/Clause.php');
require_once('ELSWAK/MySQL/Order/Clause.php');
class ELSWAK_MySQL_Select
{
	protected $selectClause;
	protected $fromClause;
	protected $whereClause;
	protected $groupClause;
	protected $orderClause;
	protected $limitClause;
	
	public function __construct(ELSWAK_MySQL_Select_Clause $selectClause = null, ELSWAK_MySQL_From_Clause $fromClause = null, ELSWAK_MySQL_Where_Clause $whereClause = null, ELSWAK_MySQL_Order_Clause $orderClause = null)
	{
		$this->setSelectClause
		(
			($selectClause !== null)?
				$selectClause:
				new ELSWAK_MySQL_Select_Clause()
		);
		$this->setFromClause
		(
			($fromClause !== null)?
				$fromClause:
				new ELSWAK_MySQL_From_Clause()
		);
		$this->setWhereClause
		(
			($whereClause !== null)?
				$whereClause:
				new ELSWAK_MySQL_Where_Clause()
		);
		$this->setOrderClause
		(
			($orderClause !== null)?
				$orderClause:
				new ELSWAK_MySQL_Order_Clause()
		);
	}
	public function selectClause()
	{
		return $this->selectClause;
	}
	public function setSelectClause(ELSWAK_MySQL_Select_Clause $selectClause)
	{
		$this->selectClause = $selectClause;
		return $this;
	}
	public function fromClause()
	{
		return $this->fromClause;
	}
	public function setFromClause(ELSWAK_MySQL_From_Clause $fromClause)
	{
		$this->fromClause = $fromClause;
		return $this;
	}
	public function whereClause()
	{
		return $this->whereClause;
	}
	public function setWhereClause(ELSWAK_MySQL_Where_Clause $whereClause)
	{
		$this->whereClause = $whereClause;
		return $this;
	}
	public function orderClause()
	{
		return $this->orderClause;
	}
	public function setOrderClause(ELSWAK_MySQL_Order_Clause $orderClause)
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
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = '';
		
		// add the select clause
		if ($this->selectClause !== null)
		{
			$sql .= $indent.$this->selectClause->sql($format, $indent).LF;
		}
		
		// add the from clause
		if ($this->fromClause !== null)
		{
			$sql .= $indent.$this->fromClause->sql($format, $indent).LF;
		}
		
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