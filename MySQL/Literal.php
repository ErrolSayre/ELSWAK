<?php
/*
	ELSWebAppKit MySQL Literal
	
	A MySQL literal is an expression made up of a specific value. It also allows
	an hard coded string to be appended to a dynamic query.
*/
require_once('ELSWebAppKit/MySQL/Expression.php');
class ELSWebAppKit_MySQL_Literal
	implements ELSWebAppKit_MySQL_Expression
{
	protected $literal;
	
	public function __construct($literal = '')
	{
		$this->setLiteral($literal);
	}
	public function literal()
	{
		return $this->literal;
	}
	public function setLiteral($literal)
	{
		// ensure that the literal contains only the allowable characters
		if (preg_match('/^[\w\+\-\.\(\)]*$/', $literal) === 1)
		{
			$this->literal = $literal;
		}
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		return $this->literal;
	}
}
?>