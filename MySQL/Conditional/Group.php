<?php
/*
	ELSWAK MySQL Conditional Group
	
	This class lumps MySQL conditional statements together into one group using
	a specified conjunction (AND or OR).
*/
require_once 'ELSWAK/MySQL/Conditional.php';
require_once 'ELSWAK/MySQL/Conjunction.php';

class ELSWAK_MySQL_Conditional_Group
	implements ELSWAK_MySQL_Expression {
	protected $conditions;
	protected $conjunction;
	
	public function __construct($conditions = null, $conjunction = null) {
		if (!is_array($conditions)) {
			$conditions = array();
		}
		$this->setConditions($conditions);
		if (!($conjunction instanceof ELSWAK_MySQL_Conjunction)) {
			$conjunction = new ELSWAK_MySQL_Conjunction($conjunction);
		}
		$this->setConjunction($conjunction);
	}
	public function conditionForKey($index) {
		if (isset($this->conditions[$index])) {
			return $this->conditions[$index];
		} else {
			throw new Exception('Invalid key: Condition not found');
		}
	}
	public function addCondition(ELSWAK_MySQL_Expression $condition) {
		$this->conditions[] = $condition;
		return $this->conditions[count($this->conditions) - 1];
	}
	public function newCondition(ELSWAK_MySQL_Expression $leftSide, ELSWAK_MySQL_Operator $operator = null, ELSWAK_MySQL_Expression $rightSide = null) {
		$conditional = new ELSWAK_MySQL_Conditional($leftSide, $operator, $rightSide);
		$this->addCondition($conditional);
		return $conditional;
	}
	public function newConditionGroup($conditions = null, $conjunction = null) {
		$group = new ELSWAK_MySQL_Conditional_Group($conditions, $conjunction);
		$this->addCondition($group);
		return $group;	
	}
	public function removeConditionForKey($index) {
		if (isset($this->conditions[$index])) {
			array_splice($this->conditions, $index, 1);
		} else {
			throw new Exception('Invalid key: Condition not removed');
		}
	}
	public function conditionCount() {
		return count($this->conditions);
	}
	public function conditionKeys() {
		return array_keys($this->conditions);
	}
	public function conditions() {
		return $this->conditions;
	}
	public function setConditions(array $conditions) {
		$this->conditions = array();
		
		foreach ($conditions as $condition) {
			if ($condition instanceOf ELSWAK_MySQL_Expression) {
				$this->addCondition($condition);
			}
		}
		return $this;
	}
	public function conjunction() {
		return $this->conjunction;
	}
	public function setConjunction(ELSWAK_MySQL_Conjunction $conjunction) {
		$this->conjunction = $conjunction;
		return $this;
	}
	public function sql($format = '', $indent = '') {
		// set up the sql
		$sql = '';
		
		// determine if we have one condition
		$conditionCount = count($this->conditions);
		if ($conditionCount == 1) {
			return $indent.$this->conditions[0]->sql($format, $indent.'  ');
		} else if ($conditionCount > 1) {
			// assemble the conditions together as a group
			$sql .= '('.LF;
			
			// now process each condition and add it's sql
			for ($index = 0; $index < $conditionCount; $index++) {
				$sql .= $indent.'  '.$this->conditions[$index]->sql($format, $indent.'  ');
				
				if ($index < ($conditionCount - 1)) {
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