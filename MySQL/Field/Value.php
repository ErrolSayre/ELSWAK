<?php
/*
	ELSWAK MySQL Field Value
	
	This class provides a pairing of a MySQL Field with an appropriate value
	designed for use in INSERT and UPDATE queries.
*/
require_once('ELSWAK/MySQL/Field.php');
class ELSWAK_MySQL_Field_Value
{
	protected $field;
	protected $value;
	
	public function __construct(ELSWAK_MySQL_Field $field, ELSWAK_MySQL_Expression $value)
	{
		$this->setField($field);
		$this->setValue($value);
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
	public function value()
	{
		return $this->value;
	}
	public function setValue(ELSWAK_MySQL_Expression $value)
	{
		$this->value = $value;
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		return $this->field->sql($format, $indent).' = '.$this->value->sql($format, $indent);
	}
}