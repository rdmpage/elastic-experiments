<?php

// Sequences

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/adodb5/adodb.inc.php');

require_once (dirname(__FILE__) . '/elastic.php');


//--------------------------------------------------------------------------------------------------
$db = NewADOConnection('mysql');
$db->Connect("localhost", 'root', '', 'ibol');

// Ensure fields are (only) indexed by column name
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

$page = 100;
$offset = 0;

$result = $db->Execute('SET max_heap_table_size = 1024 * 1024 * 1024');
$result = $db->Execute('SET tmp_table_size = 1024 * 1024 * 1024');

$done = false;

$count = 1;

while (!$done)
{
//	$sql = 'SELECT * FROM ibol_public WHERE order_reg="Lepidoptera"'
	$sql = 'SELECT * FROM ibol_public WHERE class_reg="Reptilia"'
//	$sql = 'SELECT * FROM ibol_public WHERE class_reg="Actinopterygii"'
	. ' LIMIT ' . $page . ' OFFSET ' . $offset;

	$result = $db->Execute($sql);
	if ($result == false) die("failed [" . __FILE__ . ":" . __LINE__ . "]: " . $sql);

	while (!$result->EOF && ($result->NumRows() > 0)) 
	{	
		$obj = new stdclass;
		
		// id
		$obj->processid = $result->fields['processid'];
		$obj->processid = str_replace('.COI-5P', '', $obj->processid);
		
		
		// taxonomy
		$keys = array('phylum_reg', 'class_reg', 'order_reg', 'family_reg', 'subfamily_reg', 'genus_reg');
		foreach ($keys as $k)
		{
			if ($result->fields[$k] != '')
			{
				$key = str_replace('_reg', '', $k);
				$obj->{$key} = $result->fields[$k];
			}
		}
		
		// species name
		$obj->species = $result->fields['species_reg'];		
		
		// sequence
		$obj->seq = $result->fields['nucraw'];
		
		// locality
		if ($result->fields['lat'] != '')
		{
			$obj->geometry = new stdclass;
			$obj->geometry->type = "Point";
			$obj->geometry->coordinates = array($result->fields['lon'], $result->fields['lat']);
		}		

		$elastic->send('PUT', 'sequence/' . $obj->processid, json_encode($obj));
		
		// Give server a break every 10 items
		echo ".";
		if (($count++ % 10) == 0)
		{
			$rand = rand(100000, 300000);
			echo '...sleeping for ' . round(($rand / 1000000),2) . ' seconds' . "\n";
			usleep($rand);
		}
		

		$result->MoveNext();
	}
	
	echo "\nOffset $offset, page $page\n";
	
	
	if ($result->NumRows() < $page)
	{
		$done = true;
	}
	else
	{
		$offset += $page;	
		// if we want to bail out early	
		//if ($offset > 0) { $done = true; }
	}
	
	
		
}

?>
