<?php
/*
	ELSWebAppKit Store Coordinator Interface
	
	Store Coordinators are designed to be interchangeable so that a controller can easily swap between objects stored in a file, a database, or any other means of "storage". This interface defines the bare minimum of functionality expected from a store coordinator. Namely, that it can load, save, and delete an object or objects based on a "context". Implementations of the context can vary, but generally they will be as such:
		• Load contexts can be an id, an object, an array of ids, an array of objects, or a string indicating that all records should be loaded.
		• Save contexts can be an object, or an array of objects.
		• Delete contexts can be an id, an object, an array of ids, an array of objects, or a string indicating that all records should be loaded.
		
	Each method also provides a "depth indicator" so that in cases of cyclical relations between objects, the store coordinator can know how little or how much of the hierarchy to act upon. The details of implementation again can vary, but generally will be as such:
		• Shallow depth will include the primary object only.
		• Deep objects will include the primary object and any subordinate objects (objects that do not have their own store coordinator).
		• Complete objects will include the primary object, its subordinates, and its external related objects (objects referenced by the primary object but which have their own store coordinator).
*/
require_once('ELSWebAppKit/Store/Search.php');
interface ELSWebAppKit_Store_Coordinator_Interface
{
	public static function load($context, $depth = null);
	public static function save($context, $depth = null);
	public static function delete($context, $depth = null);
	public static function search(ELSWebAppKit_Store_Search $search);
}
?>