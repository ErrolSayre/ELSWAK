<?php
/*
	ELSWebAppKit MySQL Limit Clause
	
	Represents a MySQL LIMIT clause.
*/
class ELSWebAppKit_MySQL_LimitClause
{
	protected $rowCount;
	protected $offset;
	
	public function __construct($rowCount, $offset = null)
	{
		$this->setRowCount($rowCount);
		$this->setOffset($offset);
	}
	public function rowCount()
	{
		return $this->rowCount;
	}
	public function setRowCount($rowCount)
	{
		$this->rowCount = intVal($rowCount);
		return $this;
	}
	public function offset()
	{
		return $this->offset;
	}
	public function setOffset($offset)
	{
		if ($offset !== null)
		{
			$this->offset = intVal($offset);
		}
		else
		{
			$this->offset = null;
		}
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		$sql = 'LIMIT ';
		if ($this->offset !== null)
		{
			$sql .= $this->offset.', ';
		}
		$sql .= $this->rowCount;
		
		return $sql;
	}
}
?>