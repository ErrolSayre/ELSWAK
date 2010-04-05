<?php
/*
	ELSWAK MySQL Ordinal
	
	This class pairs a field and a direction to represent an order by statement.
*/
require_once('ELSWAK/MySQL/Field.php');
require_once('ELSWAK/MySQL/Order.php');
class ELSWAK_MySQL_Ordinal
{
	protected $field;
	protected $direction;
	
	public function __construct(ELSWAK_MySQL_Field $field, ELSWAK_MySQL_Order $direction = null)
	{
		$this->setField($field);
		$this->setDirection
		(
			($direction !== null)?
				$direction:
				new ELSWAK_MySQL_Order()
		);
	}
	public function field()
	{
		return $this->field;
	}
	public function setField(ELSWAK_MySQL_Field $field)
	{
		$this->field = $field;
		return $this;
	}
	public function direction()
	{
		return $this->direction;
	}
	public function setDirection(ELSWAK_MySQL_Order $direction)
	{
		$this->direction = $direction;
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		return $this->field->sql($format, $indent).
			' '.$this->direction->sql($format, $indent);
	}
}
?>