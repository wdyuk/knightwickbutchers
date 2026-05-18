<?php
	
	require 'init.php';

	$id = $_REQUEST['id'];
	$table = $_REQUEST['table'];
	
	$fields = array('status');
	$values = array('status' => $_REQUEST['status']);
	
	$where = sprintf('id = %d', $id);
	table_update($table, $fields, $values, $where)

?>