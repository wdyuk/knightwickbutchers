<?php
	
	require 'init.php';

	$id = $_REQUEST['id'];
	$table = $_REQUEST['table'];
	
	$where = sprintf('id = %d', $id);
	$row = table_fetch_row($table, $where);
	
	echo json_encode($row);
	/*echo '{';
		
		if (is_array($row)) {
			$arr = array();
			foreach ($row as $key => $val) {
				$arr[] = "{$key} : '{$val}'";
			}
			
			echo implode(',', $arr);
		}
		
	echo '}';*/

?>