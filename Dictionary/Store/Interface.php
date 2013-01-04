<?php
/*
	ELSWAK Dictionary Store Interface
	
	This interface defines a "dictionary store" as a store coordinator that saves items by key like a dictionary collection. The ultimate implementation could save the items as a whole (i.e. saving a backing dictionary object to a batch store) or perhaps could save each item to its own storage medium.
*/
interface ELSWAK_Dictionary_Store_Interface {
	public function readForKey($key);
	public function writeForKey($data, $key);
}