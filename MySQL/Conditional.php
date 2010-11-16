<?php
/*
	ELSWAK MySQL Conditional
	
	This class contains the data necessary to make a MySQL conditional statement
	such as TRUE, FALSE, table.field = table.file, table.field < value, value !=
	table.field, etc.
*/
class ELSWAK_MySQL_Conditional
	extends ELSWAK_Settable
	implements ELSWAK_MySQL_Expression {
	
	protected $leftSide;
	protected $operator;
	protected $rightSide;
	
	/**
	 * Setup the conditional. Conditionals are generally an equality, however it is possible to have only a literal (1 or TRUE).
	 * @param ELSWAK_MySQL_Expression $left the left hand side of the equality
	 * @param ELSWAK_MySQL_Operator|string $operator the operand of the equality
	 * @param ELSWAK_Expression $right the right hand side of the equality
	 **/
	public function __construct(ELSWAK_MySQL_Expression $left, $operator = null, ELSWAK_MySQL_Expression $right = null) {
		$this->setLeftSide($left);
		if ($operator != null) {
			if (!($operator instanceof ELSWAK_MySQL_Operator)) {
				$operator = new ELSWAK_MySQL_Operator($operator);
			}
			$this->setOperator($operator);
		}
		if ($right != null) {
			$this->setRightSide($right);
		}
	}
	public function setLeftSide(ELSWAK_MySQL_Expression $leftSide) {
		$this->leftSide = $leftSide;
		return $this;
	}
	public function setOperator(ELSWAK_MySQL_Operator $operator) {
		$this->operator = $operator;
		return $this;
	}
	public function setRightSide(ELSWAK_MySQL_Expression $rightSide) {
		$this->rightSide = $rightSide;
		return $this;
	}
	/**
	 * Return the formatted sql for this conditional.
	 * @param string $format optional indicates the MySQL field reference style to use (field, table.field, database.table.field)
	 * @param string $indent optional indicating any indention to be applied to the source to make it more readable
	 * @return string
	 */
	public function sql($format = 'table.field', $indent = '') {
		$sql = '';
		
		if ($this->leftSide != null) {
			$sql .= $this->leftSide->sql($format, $indent);
		}
		if ($this->operator != null) {
			$sql .= ' '.$this->operator->sql($format, $indent);
		}
		if ($this->rightSide != null) {
			$sql .= ' '.$this->rightSide->sql($format, $indent);
		}
		
		return $sql;
	}
}
