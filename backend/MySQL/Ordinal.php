<?php
/*
	ELSWebAppKit MySQL Ordinal
	
	This class pairs a field and a direction to represent an order by statement.
*/
require_once('ELSWebAppKit/MySQL/Field.php');
require_once('ELSWebAppKit/MySQL/Order.php');
class ELSWebAppKit_MySQL_Ordinal
{
	protected $field;
	protected $direction;
	
	public function __construct(ELSWebAppKit_MySQL_Field $field, ELSWebAppKit_MySQL_Order $direction = null)
	{
		$this->setField($field);
		$this->setDirection
		(
			($direction !== null)?
				$direction:
				new ELSWebAppKit_MySQL_Order()
		);
	}
	public function field()
	{
		return $this->field;
	}
	public function setField(ELSWebAppKit_MySQL_Field $field)
	{
		$this->field = $field;
		return $this->field;
	}
	public function direction()
	{
		return $this->direction;
	}
	public function setDirection(ELSWebAppKit_MySQL_Order $direction)
	{
		$this->direction = $direction;
		return $this->direction;
	}
	public function sql($format = '', $indent = '')
	{
		return $this->field->sql($format, $indent).
			' '.$this->direction->sql($format, $indent);
	}
}
?>