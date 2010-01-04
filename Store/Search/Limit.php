<?php
/*
	ELSWebAppKit Store Search Limit
*/
class ELSWebAppKit_Store_Search_Limit
{
	protected $count;
	protected $offset;
	
	public function __construct($count = 0, $offset = 0)
	{
		$this->setCount($count);
		$this->setOffset($offset);
	}
	public function count()
	{
		return $this->count;
	}
	public function setCount($count)
	{
		$this->count = intval($count);
		return $this;
	}
	public function offset()
	{
		return $this->offset;
	}
	public function setOffset($offset)
	{
		$this->offset = intval($offset);
		return $this;
	}
}