<?php
/*
	ELSWebAppKit MySQL From Clause
	
	This class represents the attributes that make up a MySQL FROM clause. This
	class is currently very rudimentary and assumes that all tables should be
	listed separately and concatenated with commas and that the table joins
	should be joined to the last table listed. For this reason, most queries
	will either use just the tables array, or have one table in the tables array
	and all other tables will be joined to that table.
*/
require_once('ELSWebAppKit/MySQL/Table/Join.php');
class ELSWebAppKit_MySQL_From_Clause
{
	protected $tables;
	protected $tableJoins;
	
	public function __construct(array $tables = null, array $tableJoins = null)
	{
		$this->setTables
		(
			($tables !== null)?
				$tables:
				array()
		);
		$this->setTableJoins
		(
			($tableJoins !== null)?
				$tableJoins:
				array()
		);
	}
	public function tableForKey($index)
	{
		if (isset($this->tables[$index]))
		{
			return $this->tables[$index];
		}
		else
		{
			throw new Exception('Invalid key: Table not found');
		}
	}
	public function addTable(ELSWebAppKit_MySQL_Table $table)
	{
		$this->tables[] = $table;
		return $this->tables[count($this->tables) - 1];
	}
	public function removeTableForKey($index)
	{
		if (isset($this->tables[$index]))
		{
			array_splice($this->tables, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Table not removed');
		}
	}
	public function tableCount()
	{
		return count($this->tables);
	}
	public function tableKeys()
	{
		return array_keys($this->tables);
	}
	public function tables()
	{
		return $this->tables;
	}
	public function setTables(array $tables)
	{
		$this->tables = array();
		
		foreach ($tables as $table)
		{
			if ($table instanceOf ELSWebAppKit_MySQL_Table)
			{
				$this->addTable($table);
			}
		}
	}
	public function tableJoinForKey($index)
	{
		if (isset($this->tableJoins[$index]))
		{
			return $this->tableJoins[$index];
		}
		else
		{
			throw new Exception('Invalid key: Table Join not found');
		}
	}
	public function addTableJoin(ELSWebAppKit_MySQL_Table_Join $tableJoin)
	{
		$this->tableJoins[] = $tableJoin;
		return $this->tableJoins[count($this->tableJoins) - 1];
	}
	public function removeTableJoinForKey($index)
	{
		if (isset($this->tableJoins[$index]))
		{
			array_splice($this->tableJoins, $index, 1);
		}
		else
		{
			throw new Exception('Invalid key: Table Join not removed');
		}
	}
	public function tableJoinCount()
	{
		return count($this->tableJoins);
	}
	public function tableJoinKeys()
	{
		return array_keys($this->tableJoins);
	}
	public function tableJoins()
	{
		return $this->tableJoins;
	}
	public function setTableJoins(array $tableJoins)
	{
		$this->tableJoins = array();
		
		foreach ($tableJoins as $tableJoin)
		{
			if ($tableJoin instanceOf ELSWebAppKit_MySQL_Table_Join)
			{
				$this->addTableJoin($tableJoins);
			}
		}
	}
	public function sql($format = '', $indent = '')
	{
		// build the SQL string
		$sql = 'FROM';
		
		// add the tables
		if (count($this->tables) > 0)
		{
			$sql .= LF;
			foreach ($this->tables as $table)
			{
				$sql .= $indent.'  '.$table->sql($format, $indent.'  ').','.LF;
			}
			
			// remove the last comma
			$sql = substr($sql, 0, -2);
		}
		
		// add the table joins
		if (count($this->tableJoins) > 0)
		{
			$sql .= LF;
			foreach ($this->tableJoins as $tableJoin)
			{
				$sql .= $indent.'  '.$tableJoin->sql($format, $indent.'  ').LF;
			}
			
			// remove the last line feed
			$sql = substr($sql, 0, -1);
		}
		
		// return the finished string
		return $sql;
	}
}