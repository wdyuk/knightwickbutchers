<?php
	
	require 'init.php';

	$id = $_REQUEST['id'];
	$table = $_REQUEST['table'];
	
	$where = sprintf('id = %d', $id);
	table_delete_row($table, $where);
	
	$where = sprintf('photogallery_id = %d', $id);
	$rows = table_fetch_rows('photos', $where);
	
	foreach ($rows as $row) {
		$img_path = get_image_path('photos/' . $row['id'], true);
		unlink($img_path);
	}
	

	table_delete_row('photos', $where);

?>