<?php
/*
	ELSWebAppKit MySQL Store Coordinator
		
	There are three defined levels of depth: shallow, deep, and complete. Generally, the depth of an object is broken down according to these rules:
		1) Shallow depth includes only those attributes that can be loaded in a query yielding a single row. (Attribute members and 1to1 relations.)
		2) Deep depth includes only those attributes that can be loaded with one query for each relationship. (Generally a "deep object" includes a shallow version of each of its relations managed or external, 1to1 or 1to*.)
		3) Complete depth includes deep objects for managed relations and shallow objects for external relations. (Any amount of data required beyond this will have to be collected programatically by the client of the Store Coordinator.)
*/
?>