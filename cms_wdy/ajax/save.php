<?php
	
	require 'init.php';

	$id = $_REQUEST['id'];
	$table = $_REQUEST['table'];
	$fields = $_REQUEST['fields'];
	
	$where = sprintf('id = %d', $id);
	$row = table_fetch_row($table, $where);
	
	if (strpos($fields, ',') !== false) {
		$fields = explode(',', $fields);
	} else {
		$fields = array($fields);
	}
	
	if ($row == false) {
		table_insert($table, $fields, $_REQUEST);
	} else {
		table_update($table, $fields, $_REQUEST, $where);
	}

?>