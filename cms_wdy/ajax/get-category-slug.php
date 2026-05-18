<?php
	
	require 'init.php';
	
	$category_id = (int)$_POST['category_id'];
	$table = sanitize_sql_string($_POST['table']);
	
	$slug = '';
	$where = sprintf('id = %d', $category_id);

	$row = table_fetch_row($table, $where);
	
	if ($row) {
		$slug = $row['name'].'/'.$slug;
	}

	$slug = '/articles/'.$slug;
	
	$response = array('success' => 1, 'slug' => $slug);

	echo json_encode($response, JSON_UNESCAPED_SLASHES);
	
?>