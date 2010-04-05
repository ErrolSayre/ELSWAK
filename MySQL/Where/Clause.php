<?php
/*
	ELSWAK MySQL Where Clause
*/
require_once('ELSWAK/MySQL/Conditional/Group.php');
class ELSWAK_MySQL_Where_Clause
	extends ELSWAK_MySQL_Conditional_Group
{
	public function sql($format = '', $indent = '')
	{
		// set up the sql
		$sql = '';
		
		// determine if we have any conditions
		if (count($this->conditions) > 0)
		{
			$sql .= $indent.'WHERE'.LF.
				$indent.parent::sql($format, $indent.'  ');
		}
		
		// return the sql
		return $sql;
	}
}
?>