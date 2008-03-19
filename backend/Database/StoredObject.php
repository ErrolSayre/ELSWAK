<?php
/*
	ELSWebAppKit Database Stored Object
	
	The stored object defines a few standard properties of a class that should be used to define meta data for an object saved in a database table.
*/
require_once('ELSWebAppKit/Iterable.php');
class ELSWebAppKit_Database_StoredObject
{
	// database schema
	protected var $databaseName;
	protected var $tableName;
	protected var $primaryKeyName;
	
	// database metadata
	protected var $id;
	protected var $creator;
	protected var $timeCreated;
	protected var $timeModified;
	protected var $timeSaved;
	protected var $unsavedChanges = true;
	
	public function __construct()
	{
		// set up the iterable members
		$this->members[] = 'id';
		$this->members[] = 'creator';
		$this->members[] = 'timeModified';
		$this->members[] = 'timeSaved';
	}
	
	public function id()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	public function creator()
	{
		return $this->creator;
	}
	public function setCreator($creator)
	{
		$this->creator = $creator;
		return $this;
	}
	public function timeCreated()
	{
		return $this->timeCreated;
	}
	public function setTimeCreated($time)
	{
		$this->timeCreated = $time;
		return $this;
	}
	public function timeSaved()
	{
		return $this->timeSaved;
	}
	public function setTimeSaved($time)
	{
		$this->timeSaved = $time;
		return $this;
	}
	public function unsavedChanges()
	{
		return $this->unsavedChanges;
	}
}
?>