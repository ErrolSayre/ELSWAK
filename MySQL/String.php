<?php
/*
	ELSWebAppKit MySQL String
	
	Since MySQL Strings should be encoded, this class requires a mysqli database connection object
*/
require_once('ELSWebAppKit/MySQL/Expression.php');
class ELSWebAppKit_MySQL_String
	implements ELSWebAppKit_MySQL_Expression
{
	protected $string;
	protected $db;
	
	public function __construct($string, mysqli $db)
	{
		$this->setString($string);
		$this->db = $db;
	}
	public function string()
	{
		return $this->string;
	}
	public function setString($string)
	{
		$this->string = strval($string);
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		return '"'.$this->db->escape_string($this->string).'"';
	}
}