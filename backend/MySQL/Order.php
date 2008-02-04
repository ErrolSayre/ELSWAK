<?php
/*
	ELSWebAppKit MySQL Order
	
	This class represents the operators used to determine a MySQL sort order.
*/
class ELSWebAppKit_MySQL_Order
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
	}
	public function sql($format = '', $order = '')
	{
		return $this->order;
	}
}
?>