<?php
/*
	ELSWebAppKit Iterable
	
	This class is defined to provide a common base for objects with private members which wish to implement the Iterator interface. I use this primarily to make models with protected members iterable. Simply definte the members array as a list of your protected members' names.
*/
class ELSWebAppKit_Iterable
	implements Iterator
{
	protected $_iterables = array();
	
	public function rewind()
	{
		reset($this->_iterables);
	}
	public function current()
	{
		return $this->{current($this->_iterables)};
	}
	public function key()
	{
		return current($this->_iterables);
	}
	public function next()
	{
		return next($this->_iterables);
	}
	public function valid()
	{
		return (current($this->_iterables) !== false)? true: false;
	}
}
?>