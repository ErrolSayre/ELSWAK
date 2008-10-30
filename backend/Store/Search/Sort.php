<?php
/*
	ELSWebAppKit Store Search Sort
	
	Sorts are made up of an object property name and a sort order.
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_Store_Search_Sort
	extends ELSWebAppKit_Iterable
{
	protected $property;
	protected $order;
	
	// member listing for iterator methods
	protected $_iterables = array
	(
		'property',
		'order'
	);
	
	public function __construct($property, $order = 'ASC')
	{
		$this->setProperty($property);
		$this->setOrder($order);
	}
	public function property()
	{
		return $this->property;
	}
	public function setProperty($property)
	{
		$this->property = $property;
		return $this;
	}
	public function order()
	{
		return $this->order;
	}
	public function setOrder($order)
	{
		$order = strtolower($order);
		
		if (($order == 'asc') || ($order == 'ascending') || ($order == '<<'))
		{
			$this->order = 'ascending';
		}
		else if (($order == 'desc') || ($order == 'descending') || ($order == 'dsc') || ($order == '>>'))
		{
			$this->order = 'descending';
		}
		else
		{
			$this->order = 'ascending';
			throw new Exception('Invalid sort order: order reset to ascending.');
		}
		return $this;
	}
}