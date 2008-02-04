<?php
/*
	ELSWebAppKit MySQL Conditional Group
	
	This class lumps MySQL conditional statements together into one group using
	a specified conjunction (AND or OR).
*/
require_once('ELSWebAppKit/MySQL/Conditional.php');
require_once('ELSWebAppKit/MySQL/Conjunction.php');
class ELSWebAppKit_MySQL_Conditional_Group
	implements ELSWebAppKit_MySQL_Expression
{
	protected $conditions;
	protected $conjunction;
	
	public function __construct(array $conditions = null, ELSWebAppKit_MySQL_Conjunction $conjunction = null)
	{
		$this->setConditions
		(
			($conditions !== null)?
				$conditions:
				array()
		);
		$this->setConjunction
		(
			($conjunction !== null)?
				$conjunction:
				new ELSWebAppKit_MySQL_Conjunction()
		);
	}
	public function conditionForKey($index)
	{
		if (isset($this->conditions[$index]))
		{
			return $this->conditions[$index];
		}
		else
		{
			throw new Exception('Invalid key: Condition not found');
		}
	}
	public function addCondition(ELSWebAppKit_MySQL_Expression $condition)
	{
		$this->conditions[] = $condition;
		return $this->conditions[count($this->conditions) - 1];
	}
	public function removeConditionForKey($index)
	{
		if (isset($this->conditions[$index]))
		{
			array_splice($this->conditions, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Condition not removed');
		}
	}
	public function conditionCount()
	{
		return count($this->conditions);
	}
	public function conditionKeys()
	{
		return array_keys($this->conditions);
	}
	public function conditions()
	{
		return $this->conditions;
	}
	public function setConditions(array $conditions)
	{
		$this->conditions = array();
		
		foreach ($conditions as $condition)
		{
			if ($condition instanceOf ELSWebAppKit_MySQL_Expression)
			{
				$this->addCondition($condition);
			}
		}
	}
	public function conjunction()
	{
		return $this->conjunction;
	}
	public function setConjunction(ELSWebAppKit_MySQL_Conjunction $conjunction)
	{
		$this->conjunction = $conjunction;
		return $this->conjunction;
	}
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = '';
		
		// determine if we have one condition
		$conditionCount = count($this->conditions);
		if ($conditionCount == 1)
		{
			return $indent.$this->conditions[0]->sql($format, $indent.'  ');
		}
		else if ($conditionCount > 1)
		{
			// assemble the conditions together as a group
			$sql .= '('.LF;
			
			// now process each condition and add it's sql
			for ($index = 0; $index < $conditionCount; $index++)
			{
				$sql .= $indent.'  '.$this->conditions[$index]->sql($format, $indent.'  ');
				
				if ($index < ($conditionCount - 1))
				{
					$sql .= ' '.$this->conjunction->sql($format, $indent.'  ');
				}
				
				$sql .= LF;
			}
			
			// close the group
			$sql .= $indent.')';
		}
		
		// return the sql
		return $sql;
	}
}
?>