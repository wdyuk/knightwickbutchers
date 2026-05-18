<?php
	
	require 'init.php';

	$positions = $_REQUEST['positions'];
	$table = $_REQUEST['table'];
	
	foreach ($positions as $id => $position) {
		$fields = array('position');
		$values = array('position' => $position);
		
		$where = sprintf('id = %d', $id);
		table_update($table, $fields, $values, $where);
	}
	
?>