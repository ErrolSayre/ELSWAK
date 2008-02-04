<?php
/*
	ELSWebAppKit MySQL Database
	
	This class provides some basic information for an object representing the
	attributes of a database.
*/
class ELSWebAppKit_MySQL_Database
{
	protected $name;
	
	public function __construct($name)
	{
		$this->setName($name);
	}
	public function name()
	{
		return $this->name;
	}
	protected function setName($name)
	{
		$this->name = $name;
	}
	public function prettyName()
	{
		return $this->name;
	}
	public function sql($format = '', $indent = '')
	{
		return '`'.$this->name.'`';
	}
}
?>