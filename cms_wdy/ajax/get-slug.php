<?php
	
	require 'init.php';

	$parent_id = (int)$_POST['parent_id'];
	$table = sanitize_sql_string($_POST['table']);
	
	$slug = '';

	while ($parent_id > 0) {
		$where = sprintf('id = %d', $parent_id);

		$row = table_fetch_row($table, $where);
		if ($row) {
			$slug = $row['menu_title'].'/'.$slug;
		}
		$parent_id = $row['parent_id'];
		
	}

	$slug = '/'.$slug;
	
	$response = array('success' => 1, 'slug' => $slug);

	echo json_encode($response, JSON_UNESCAPED_SLASHES);
	
?>