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


$sql = 'SELECT * FROM ibol_public WHERE class_reg="Mammalia"';

$result = $db->Execute($sql);
if ($result == false) die("failed [" . __LINE__ . "]: " . $sql);
while (!$result->EOF) 
{
	$obj = new stdclass;
	$obj->processid = $result->fields['processid'];
	$obj->species = $result->fields['species_reg'];
	$obj->seq = $result->fields['nucraw'];

	$id = $obj->processid;

	$elastic->send('PUT', 'sequence/' . $id, json_encode($obj));

	$result->MoveNext();	
}

?>
