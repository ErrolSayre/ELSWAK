<?php
/*
	ELSWAK MySQL Order
	
	This class represents the operators used to determine a MySQL sort order.
*/
class ELSWAK_MySQL_Order
{
	protected $order;
	
	public function __construct($order = 'ASC')
	{
		$this->setOrder($order);
	}
	public function order()
	{
		return $this->order;
	}
	public function setOrder($order)
	{
		if (strtoupper($order) == 'ASC')
		{
			$this->order = 'ASC';
		}
		else
		{
			$this->order = 'DESC';
		}
		return $this;
	}
	public function sql($format = '', $order = '')
	{
		return $this->order;
	}
}
?>