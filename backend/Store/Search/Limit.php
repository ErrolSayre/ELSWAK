<?php
/*
	ELSWebAppKit Store Search Limit
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_Store_Search_Limit
	extends ELSWebAppKit_Iterable
{
	protected $count;
	protected $offset;
	
	// member listing for iterator methods
	protected $members = array
	(
		'count',
		'offset'
	);
	
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
	}
	public function offset()
	{
		return $this->offset;
	}
	public function setOffset($offset)
	{
		$this->offset = intval($offset);
	}
}