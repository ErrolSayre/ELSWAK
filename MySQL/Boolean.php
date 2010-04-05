<?php
/*
	ELSWAK MySQL Boolean
*/
require_once('ELSWAK/MySQL/Expression.php');
class ELSWAK_MySQL_Boolean
	implements ELSWAK_MySQL_Expression
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
		return $this;
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