<?php
/*
	ELSWebAppKit MySQL Boolean
*/
require_once('ELSWebAppKit/MySQL/Expression.php');
class ELSWebAppKit_MySQL_Boolean
	implements ELSWebAppKit_MySQL_Expression
{
	protected $boolean;
	
	public function __construct($boolean = true)
	{
		$this->setBoolean($boolean);
	}
	public function boolean()
	{
		return $this->boolean;
	}
	public function setBoolean($boolean)
	{
		if (($boolean === true)	||
			(strtolower($boolean) == 'true'))
		{
			$this->boolean = true;
		}
		else
		{
			$this->boolean = false;
		}
	}
	public function sql($format = '', $indent = '')
	{
		if ($this->boolean)
		{
			return 'TRUE';
		}
		
		return 'FALSE';
	}
}
?>