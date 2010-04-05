<?php
/*
	ELSWAK MySQL Order Clause
*/
require_once('ELSWAK/MySQL/Ordinal.php');
class ELSWAK_MySQL_Order_Clause
{
	protected $ordinals;
	
	public function __construct(array $ordinals = null)
	{
		$this->setOrdinals
		(
			($ordinals !== null)?
				$ordinals:
				array()
		);
	}
	public function ordinalForKey($index)
	{
		if (isset($this->ordinals[$index]))
		{
			return $this->ordinals[$index];
		}
		else
		{
			throw new Exception('Invalid key: Ordinal not found.');
		}
	}
	public function addOrdinal(ELSWAK_MySQL_Ordinal $ordinal)
	{
		$this->ordinals[] = $ordinal;
		return $this->ordinals[count($this->ordinals) - 1];
	}
	public function removeOrdinalForKey($index)
	{
		if (isset($this->ordinals[$index]))
		{
			array_splice($this->ordinals, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Ordinal not removed.');
		}
	}
	public function ordinalCount()
	{
		return count($this->ordinals);
	}
	public function ordinalKeys()
	{
		return array_keys($this->ordinals);
	}
	public function ordinals()
	{
		return $this->ordinals;
	}
	public function setOrdinals(array $ordinals)
	{
		$this->ordinals = array();
		
		foreach ($ordinals as $ordinal)
		{
			if ($ordinal instanceof ELSWAK_MySQL_Ordinal)
			{
				$this->addOrdinal($ordinal);
			}
		}
		return $this;
	}
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = '';
		
		// determine if we have one ordinal
		$ordinalCount = count($this->ordinals);
		if ($ordinalCount > 0)
		{
			// assemble the ordinals together as a group
			$sql .= 'ORDER BY'.LF;
			
			// now process each ordinal and add its sql
			for ($index = 0; $index < $ordinalCount; $index++)
			{
				$sql .= $indent.'  '.$this->ordinals[$index]->sql($format, $indent.'  ');
				
				if ($index < ($ordinalCount - 1))
				{
					$sql .= ',';
				}
				$sql .= LF;
			}
		}
		
		// return the sql
		return $sql;
	}
}
?>