<?php
	
	require 'init.php';

	$id = $_REQUEST['id'];
	$table = $_REQUEST['table'];
	
	$fields = explode(',', $_REQUEST['fields']);
	$values = array();
	foreach ($fields as $field) {
		$values[$field] = $_REQUEST[$field];
	}
	
	$where = sprintf('id = %d', $id);
	table_update($table, $fields, $values, $where)

?>