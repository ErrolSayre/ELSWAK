<?php
/*
	ELSWAK Batch Store Interface
	
	This interface defines a "batch store" as a store coordinator that can store batch data (as opposed to items).
*/
interface ELSWAK_Batch_Store_Interface {
	public function read();
	public function write($data);
}