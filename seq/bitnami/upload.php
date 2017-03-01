<?php

require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/elastic.php');


// project 
if (1)
{
	$url = 'http://www.boldsystems.org/index.php/API_Public/combined';
	
	// GBAP GenBank amphibians

	$parameters = array(
		'container' => 'GBAP',
		'format' => 'tsv'
		);
}
	
// taxon 
if (1)
{
	$url = 'http://www.boldsystems.org/index.php/API_Public/combined';

	$parameters = array(
		'taxon' => 'Agnotecous yahoue',
		'format' => 'tsv'
		);	
}
	
$url .= '?' . http_build_query($parameters);

echo $url . "\n";

$data = get($url);

if ($data)
{
	$lines = explode("\n", $data);

	$keys = array();
	$row_count = 0;
	
	foreach ($lines as $line)
	{
		if ($line == '') break;
		$row = explode("\t", $line);
		
		if ($row_count == 0)
		{
			$keys = $row;
		}
		else
		{			
			$obj = new stdclass;
			
			$n = count($row);
			for ($i = 0; $i < $n; $i++)
			{
				if (trim($row[$i]) != '')
				{
					$obj->{$keys[$i]} = $row[$i];
				}
			}
			
			// Do any post processing here
			if (isset($obj->lat) && isset($obj->lon))
			{
				$obj->geometry = new stdclass;
				$obj->geometry->type = "Point";
				$obj->geometry->coordinates = array(
					(float) $obj->lon,
					(float) $obj->lat
					);			
			}
			
			print_r($obj);
			
			// Upload to elasticsearch as datatype "sequence"
			$id = $obj->processid;
			
			if (0)
			{
				// create record
				$elastic->send('PUT', 'sequence/' . urlencode($id) . '/_create', json_encode($obj));
			}
			else
			{
				// update even if doesn't exist
				// https://www.elastic.co/guide/en/elasticsearch/reference/2.2/docs-update.html#_literal_doc_as_upsert_literal
				// http://stackoverflow.com/a/25037572
				$doc = new stdclass;
				$doc->doc = $obj;
				$doc->doc_as_upsert = true;
				
				$elastic->send('POST', 'sequence/' . urlencode($id) . '/_update', json_encode($doc));
			}
			
			
			exit();
			
		}
		$row_count++;
	}
}

?>
