<?php
/*
	ELSWebAppKit Iterable
	
	This class is defined to provide a common base for objects with private members which wish to implement the Iterator interface. I use this primarily to make models with protected members iterable. Simply definte the members array as a list of your protected members' names.
*/
class ELSWebAppKitIterable
	implements Iterator
{
	protected $members = array();
	
	public function rewind()
	{
		reset($this->members);
	}
	public function current()
	{
		return $this->{current($this->members)};
	}
	public function key()
	{
		return current($this->members);
	}
	public function next()
	{
		return next($this->members);
	}
	public function valid()
	{
		return (current($this->members) !== false)? true: false;
	}
}
?>